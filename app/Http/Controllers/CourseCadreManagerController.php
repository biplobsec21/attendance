<?php

namespace App\Http\Controllers;

use App\Models\Soldier;
use App\Models\Course;
use App\Models\Cadre;
use App\Models\Rank;
use App\Models\Company;
use App\Models\SoldierCadre;
use App\Models\SoldierCourse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CourseCadreManagerController extends Controller
{
    public function index()
    {
        try {
            // Update statuses for all active courses and cadres
            SoldierCourse::active()->each(function ($course) {
                $course->updateStatus();
            });

            SoldierCadre::active()->each(function ($cadre) {
                $cadre->updateStatus();
            });

            $currentCourses = SoldierCourse::with(['soldier.rank', 'soldier.company', 'course'])
                ->whereIn('status', ['active', 'scheduled'])
                ->latest()
                ->get();

            $previousCourses = SoldierCourse::with(['soldier.rank', 'soldier.company', 'course'])
                ->where('status', 'completed')
                ->latest()
                ->get();

            $currentCadres = SoldierCadre::with(['soldier.rank', 'soldier.company', 'cadre'])
                ->whereIn('status', ['active', 'scheduled'])
                ->latest()
                ->get();

            $previousCadres = SoldierCadre::with(['soldier.rank', 'soldier.company', 'cadre'])
                ->where('status', 'completed')
                ->latest()
                ->get();

            return view('mpm.page.cadre_course.index', compact(
                'currentCourses',
                'previousCourses',
                'currentCadres',
                'previousCadres'
            ));
        } catch (\Exception $e) {
            Log::error('Error in CourseCadreManagerController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load course/cadre data. Please try again.');
        }
    }

    public function create()
    {
        try {
            $courses = Course::active()->get();
            $cadres = Cadre::active()->get();
            $ranks = Rank::all();
            $companies = Company::all();

            // Get soldiers with their active assignments using the new model methods
            $soldiers = Soldier::with(['rank', 'company', 'activeCourses', 'activeCadres'])->get();

            // Separate soldiers into available and assigned
            $availableSoldiers = $soldiers->reject(function ($soldier) {
                return $soldier->hasActiveAssignments();
            });

            $assignedSoldiers = $soldiers->filter(function ($soldier) {
                return $soldier->hasActiveAssignments();
            });

            return view('mpm.page.cadre_course.create', compact(
                'courses',
                'cadres',
                'availableSoldiers',
                'assignedSoldiers',
                'ranks',
                'companies'
            ));
        } catch (\Exception $e) {
            Log::error('Error in CourseCadreManagerController@create: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load form data. Please try again.');
        }
    }

    public function store(Request $request)
    {
        try {
            // Base validation rules
            $rules = [
                'type' => 'required|in:course,cadre',
                'soldier_ids' => 'required|array|min:1',
                'soldier_ids.*' => 'exists:soldiers,id',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'note' => 'nullable|string',
            ];

            // Add conditional validation rules based on type
            if ($request->type === 'course') {
                $rules['course_id'] = 'required|exists:courses,id';
            } else {
                $rules['cadre_id'] = 'required|exists:cadres,id';
            }

            $request->validate($rules);

            $type = $request->type;
            $note = $request->note;
            $createdCount = 0;
            $skippedSoldiers = [];
            $adjustedDates = [];

            foreach ($request->soldier_ids as $soldierId) {
                $soldier = Soldier::find($soldierId);

                // Check if soldier already has active assignments
                if ($soldier->hasActiveAssignments()) {
                    $skippedSoldiers[] = $soldier->full_name;
                    continue;
                }

                // Check if the soldier has completed any assignments today
                $hasCompletedToday = $soldier->hasCompletedAssignmentsToday();

                // Prepare start date
                $startDate = Carbon::parse($request->start_date);
                $originalStartDate = $startDate->copy();

                // If the soldier has completed an assignment today and the requested start date is today,
                // adjust it to tomorrow
                if ($hasCompletedToday && $startDate->isToday()) {
                    $startDate = $startDate->addDay();
                    $adjustedDates[] = $soldier->full_name;
                }

                // Prepare end date
                $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

                // If we adjusted the start date and there's an end date, adjust it accordingly
                if ($endDate && $startDate->isAfter($originalStartDate)) {
                    $diffInDays = $originalStartDate->diffInDays($endDate);
                    if ($diffInDays > 0) {
                        $endDate = $startDate->copy()->addDays($diffInDays);
                    }
                }

                if ($type === 'course') {
                    $course = SoldierCourse::create([
                        'soldier_id' => $soldierId,
                        'course_id' => $request->course_id,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'remarks' => $note,
                    ]);
                    // Update status based on dates
                    $course->updateStatus();
                } else {
                    $cadre = SoldierCadre::create([
                        'soldier_id' => $soldierId,
                        'cadre_id' => $request->cadre_id,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'remarks' => $note,
                    ]);
                    // Update status based on dates
                    $cadre->updateStatus();
                }
                $createdCount++;
            }

            $message = "{$createdCount} assignment(s) created successfully.";
            if (count($skippedSoldiers) > 0) {
                $message .= " Skipped " . count($skippedSoldiers) . " soldiers who already have active assignments: " . implode(', ', $skippedSoldiers);
            }
            if (count($adjustedDates) > 0) {
                $message .= " Adjusted start dates for " . count($adjustedDates) . " soldiers who completed assignments today: " . implode(', ', $adjustedDates);
            }

            Log::info("Created {$createdCount} new {$type} assignments for soldiers: " . json_encode($request->soldier_ids));

            return redirect()->route('coursecadremanager.index')->with('success', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error in CourseCadreManagerController@store: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error in CourseCadreManagerController@store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create assignment. Please try again.')
                ->withInput();
        }
    }

    public function completeCourse($id, Request $request)
    {
        try {
            $assignment = SoldierCourse::findOrFail($id);

            // Set end_date to today if not already set
            if (!$assignment->end_date || $assignment->end_date->isFuture()) {
                $assignment->end_date = now();
            }

            if ($request->has('completion_note')) {
                $assignment->remarks = $request->completion_note;
            }

            // Update status to completed
            $assignment->status = 'completed';
            $assignment->save();

            Log::info("Course {$id} marked as completed for soldier {$assignment->soldier_id}");

            return redirect()->route('coursecadremanager.index')->with('success', 'Course marked as completed.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Course not found with ID: {$id}");
            return redirect()->route('coursecadremanager.index')->with('error', 'Course not found.');
        } catch (\Exception $e) {
            Log::error('Error in CourseCadreManagerController@completeCourse: ' . $e->getMessage());
            return redirect()->route('coursecadremanager.index')->with('error', 'Failed to complete course. Please try again.');
        }
    }

    public function completeCadre($id, Request $request)
    {
        try {
            $assignment = SoldierCadre::findOrFail($id);

            // Set end_date to today if not already set
            if (!$assignment->end_date || $assignment->end_date->isFuture()) {
                $assignment->end_date = now();
            }

            if ($request->has('completion_note')) {
                $assignment->remarks = $request->completion_note;
            }

            // Update status to completed
            $assignment->status = 'completed';
            $assignment->save();

            Log::info("Cadre {$id} marked as completed for soldier {$assignment->soldier_id}");

            return redirect()->route('coursecadremanager.index')->with('success', 'Cadre marked as completed.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Cadre not found with ID: {$id}");
            return redirect()->route('coursecadremanager.index')->with('error', 'Cadre not found.');
        } catch (\Exception $e) {
            Log::error('Error in CourseCadreManagerController@completeCadre: ' . $e->getMessage());
            return redirect()->route('coursecadremanager.index')->with('error', 'Failed to complete cadre. Please try again.');
        }
    }

    public function destroy($type, $id)
    {
        try {
            if ($type === 'course') {
                $assignment = SoldierCourse::findOrFail($id);
                $modelName = 'Course';
            } else {
                $assignment = SoldierCadre::findOrFail($id);
                $modelName = 'Cadre';
            }

            $soldierId = $assignment->soldier_id;
            $assignment->delete();

            Log::info("Deleted {$type} assignment {$id} for soldier {$soldierId}");

            return redirect()->route('coursecadremanager.index')->with('success', 'Assignment deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("{$type} assignment not found with ID: {$id}");
            return redirect()->route('coursecadremanager.index')->with('error', 'Assignment not found.');
        } catch (\Exception $e) {
            Log::error('Error in CourseCadreManagerController@destroy: ' . $e->getMessage());
            return redirect()->route('coursecadremanager.index')->with('error', 'Failed to delete assignment. Please try again.');
        }
    }

    public function bulkCompleteCourses(Request $request)
    {
        try {
            $courseIds = $request->course_ids;

            // Decode JSON if string
            if (is_string($courseIds)) {
                $courseIds = json_decode($courseIds, true) ?? [];
            }

            // Manual validation to redirect back with errors
            $validator = Validator::make(
                [
                    'course_ids' => $courseIds,
                    'remarks' => $request->completion_note,
                ],
                [
                    'course_ids' => 'required|array',
                    'course_ids.*' => 'exists:soldier_courses,id',
                    'remarks' => 'nullable|string|max:500',
                ]
            );

            if ($validator->fails()) {
                Log::warning('Validation error in CourseCadreManagerController@bulkCompleteCourses: ' . json_encode($validator->errors()));
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Safe validated data
            $data = $validator->validated();
            $courseIds = $data['course_ids'];

            // Get courses and update each one individually
            $courses = SoldierCourse::whereIn('id', $courseIds)->get();
            foreach ($courses as $course) {
                // Set end_date to today if not already set
                if (!$course->end_date || $course->end_date->isFuture()) {
                    $course->end_date = now();
                }

                if ($request->has('completion_note')) {
                    $course->remarks = $request->completion_note;
                }

                // Update status to completed
                $course->status = 'completed';
                $course->save();
            }

            $count = count($courseIds);
            Log::info("Bulk completed {$count} courses: " . json_encode($courseIds));

            return redirect()->route('coursecadremanager.index')
                ->with('success', "{$count} course" . ($count > 1 ? 's' : '') . " marked as completed.");
        } catch (\Exception $e) {
            Log::error('Error in CourseCadreManagerController@bulkCompleteCourses: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to complete courses. Please try again.')
                ->withInput();
        }
    }

    public function bulkCompleteCadres(Request $request)
    {
        try {
            $cadreIds = $request->cadre_ids;

            // Decode JSON if string
            if (is_string($cadreIds)) {
                $cadreIds = json_decode($cadreIds, true) ?? [];
            }

            // Manually validate so we can redirect back with errors
            $validator = Validator::make(
                [
                    'cadre_ids' => $cadreIds,
                    'remarks' => $request->completion_note,
                ],
                [
                    'cadre_ids' => 'required|array',
                    'cadre_ids.*' => 'exists:soldier_cadres,id',
                    'remarks' => 'nullable|string|max:500',
                ]
            );

            if ($validator->fails()) {
                Log::warning('Validation error in CourseCadreManagerController@bulkCompleteCadres: ' . json_encode($validator->errors()));
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Safe validated data
            $data = $validator->validated();
            $cadreIds = $data['cadre_ids'];

            // Get cadres and update each one individually
            $cadres = SoldierCadre::whereIn('id', $cadreIds)->get();
            foreach ($cadres as $cadre) {
                // Set end_date to today if not already set
                if (!$cadre->end_date || $cadre->end_date->isFuture()) {
                    $cadre->end_date = now();
                }

                if ($request->has('completion_note')) {
                    $cadre->remarks = $request->completion_note;
                }

                // Update status to completed
                $cadre->status = 'completed';
                $cadre->save();
            }

            $count = count($cadreIds);
            Log::info("Bulk completed {$count} cadres: " . json_encode($cadreIds));

            return redirect()->route('coursecadremanager.index')
                ->with('success', "{$count} cadre" . ($count > 1 ? 's' : '') . " marked as completed.");
        } catch (\Exception $e) {
            Log::error('Error in CourseCadreManagerController@bulkCompleteCadres: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to complete cadres. Please try again.')
                ->withInput();
        }
    }
    public function getEditData($type, $id)
    {
        try {
            if ($type === 'course') {
                $assignment = SoldierCourse::with(['soldier.rank', 'soldier.company', 'course'])->findOrFail($id);
                $courses = Course::active()->get();
                $cadres = collect(); // Empty collection for courses
                $courseId = $assignment->course_id;
                $cadreId = null;
            } else {
                $assignment = SoldierCadre::with(['soldier.rank', 'soldier.company', 'cadre'])->findOrFail($id);
                $cadres = Cadre::active()->get();
                $courses = collect(); // Empty collection for cadres
                $cadreId = $assignment->cadre_id;
                $courseId = null;
            }

            // Get all soldiers with their assignment status
            $soldiers = Soldier::with(['rank', 'company'])
                ->get()
                ->map(function ($soldier) {
                    $soldier->has_active_assignments = $soldier->hasActiveAssignments();
                    return $soldier;
                });

            // Get soldiers who have completed assignments today
            $completedTodaySoldiers = Soldier::whereHas('courses', function ($query) {
                $query->where('completion_date', now()->toDateString());
            })
                ->orWhereHas('cadres', function ($query) {
                    $query->where('completion_date', now()->toDateString());
                })
                ->pluck('id')
                ->toArray();

            return response()->json([
                'assignment' => $assignment,
                'start_date' => $assignment->start_date->format('Y-m-d'),
                'end_date' => $assignment->end_date ? $assignment->end_date->format('Y-m-d') : null,
                'remarks' => $assignment->remarks,
                'soldier_id' => $assignment->soldier_id,
                'course_id' => $courseId,
                'cadre_id' => $cadreId,
                'courses' => $courses,
                'cadres' => $cadres,
                'soldiers' => $soldiers,
                'completed_today_soldiers' => $completedTodaySoldiers
            ]);
        } catch (\Exception $e) {
            Log::error('Error in CourseCadreManagerController@getEditData: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load assignment data'], 500);
        }
    }
    public function update(Request $request, $type, $id)
    {
        try {
            // Base validation rules
            $rules = [
                'soldier_id' => 'required|exists:soldiers,id',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'note' => 'nullable|string',
            ];

            // Add conditional validation rules based on type
            if ($type === 'course') {
                $rules['course_id'] = 'required|exists:courses,id';
                $assignment = SoldierCourse::findOrFail($id);
            } else {
                $rules['cadre_id'] = 'required|exists:cadres,id';
                $assignment = SoldierCadre::findOrFail($id);
            }

            $request->validate($rules);

            // Check if the new soldier already has active assignments (excluding the current assignment)
            $soldier = Soldier::find($request->soldier_id);
            if ($request->soldier_id != $assignment->soldier_id && $soldier->hasActiveAssignments()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected soldier already has active assignments.'
                ], 422);
            }

            // Check if the soldier has completed any assignments today
            $hasCompletedToday = $soldier->hasCompletedAssignmentsToday();

            // Prepare start date
            $startDate = Carbon::parse($request->start_date);
            $originalStartDate = $startDate->copy();

            // If the soldier has completed an assignment today and the requested start date is today,
            // adjust it to tomorrow
            if ($hasCompletedToday && $startDate->isToday()) {
                $startDate = $startDate->addDay();
            }

            // Prepare end date
            $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

            // If we adjusted the start date and there's an end date, adjust it accordingly
            if ($endDate && $startDate->isAfter($originalStartDate)) {
                $diffInDays = $originalStartDate->diffInDays($endDate);
                if ($diffInDays > 0) {
                    $endDate = $startDate->copy()->addDays($diffInDays);
                }
            }

            // Update assignment
            $assignment->soldier_id = $request->soldier_id;
            $assignment->start_date = $startDate;
            $assignment->end_date = $endDate;
            $assignment->remarks = $request->note;

            if ($type === 'course') {
                $assignment->course_id = $request->course_id;
            } else {
                $assignment->cadre_id = $request->cadre_id;
            }

            $assignment->save();

            // Update status based on dates
            $assignment->updateStatus();

            $message = "Assignment updated successfully.";
            if ($startDate->isAfter($originalStartDate)) {
                $message .= " Start date was adjusted to tomorrow because the soldier completed an assignment today.";
            }

            Log::info("Updated {$type} assignment {$id}");

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error in CourseCadreManagerController@update: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check your input.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in CourseCadreManagerController@update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update assignment. Please try again.'
            ], 500);
        }
    }
}
