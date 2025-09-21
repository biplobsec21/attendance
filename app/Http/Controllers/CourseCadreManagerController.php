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

class CourseCadreManagerController extends Controller
{
    public function index()
    {
        try {
            $currentCourses = SoldierCourse::with(['soldier.rank', 'soldier.company', 'course'])
                ->whereNull('completion_date')
                ->latest()
                ->get();

            $previousCourses = SoldierCourse::with(['soldier.rank', 'soldier.company', 'course'])
                ->whereNotNull('completion_date')
                ->latest()
                ->get();

            $currentCadres = SoldierCadre::with(['soldier.rank', 'soldier.company', 'cadre'])
                ->whereNull('completion_date')
                ->latest()
                ->get();

            $previousCadres = SoldierCadre::with(['soldier.rank', 'soldier.company', 'cadre'])
                ->whereNotNull('completion_date')
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
            $soldiers = Soldier::with(['rank', 'company'])->get();
            $ranks = Rank::all();
            $companies = Company::all();

            return view('mpm.page.cadre_course.create', compact(
                'courses',
                'cadres',
                'soldiers',
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
                'note' => 'nullable|string',
            ];

            // Add conditional validation rules based on type
            if ($request->type === 'course') {
                $rules['course_id'] = 'required|exists:courses,id';
                $rules['cadre_id'] = 'nullable|exists:cadres,id';
            } else {
                $rules['cadre_id'] = 'required|exists:cadres,id';
                $rules['course_id'] = 'nullable|exists:courses,id';
            }

            $request->validate($rules);

            $type = $request->type;
            $note = $request->note;
            $createdCount = 0;

            foreach ($request->soldier_ids as $soldierId) {
                if ($type === 'course') {
                    SoldierCourse::create([
                        'soldier_id' => $soldierId,
                        'course_id' => $request->course_id,
                        'from_date' => now(),
                        'note' => $note,
                    ]);
                } else {
                    SoldierCadre::create([
                        'soldier_id' => $soldierId,
                        'cadre_id' => $request->cadre_id,
                        'from_date' => now(),
                        'note' => $note,
                    ]);
                }
                $createdCount++;
            }

            Log::info("Created {$createdCount} new {$type} assignments for soldiers: " . json_encode($request->soldier_ids));

            return redirect()->route('coursecadremanager.index')->with('success', "{$createdCount} assignment(s) created successfully.");
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

            $assignment->completion_date = now();
            if ($request->has('completion_note')) {
                $assignment->remarks = $request->completion_note;
            }
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

            $assignment->completion_date = now();
            if ($request->has('completion_note')) {
                $assignment->remarks = $request->completion_note;
            }
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

            SoldierCourse::whereIn('id', $courseIds)->update([
                'completion_date' => now(),
                'remarks' => $request->completion_note ?? null,
            ]);

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

            SoldierCadre::whereIn('id', $cadreIds)->update([
                'completion_date' => now(),
                'remarks' => $request->completion_note ?? null,
            ]);

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
}
