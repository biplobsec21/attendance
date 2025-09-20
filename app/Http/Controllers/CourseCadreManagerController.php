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


use Illuminate\Http\Request;

class CourseCadreManagerController extends Controller
{
    public function index()
    {
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
    }

    public function create()
    {
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
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:course,cadre',
            'course_id' => 'required_if:type,course|exists:courses,id',
            'cadre_id' => 'required_if:type,cadre|exists:cadres,id',
            'soldier_ids' => 'required|array|min:1',
            'soldier_ids.*' => 'exists:soldiers,id',
            'note' => 'nullable|string',
        ]);

        $type = $request->type;
        $note = $request->note;

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
        }

        return redirect()->route('coursecadremanager.index')->with('success', 'Assignment created successfully.');
    }
    public function completeCourse($id, Request $request)
    {
        $assignment = SoldierCourse::findOrFail($id);

        $assignment->completion_date = now();
        if ($request->has('completion_note')) {
            $assignment->remarks = $request->completion_note;
        }
        $assignment->save();

        return redirect()->route('coursecadremanager.index')->with('success', 'Course marked as completed.');
    }

    public function completeCadre($id, Request $request)
    {
        $assignment = SoldierCadre::findOrFail($id);

        $assignment->completion_date = now();
        if ($request->has('completion_note')) {
            $assignment->remarks = $request->completion_note;
        }
        $assignment->save();

        return redirect()->route('coursecadremanager.index')->with('success', 'Cadre marked as completed.');
    }

    public function destroy($type, $id)
    {
        if ($type === 'course') {
            $assignment = SoldierCourse::findOrFail($id);
        } else {
            $assignment = SoldierCadre::findOrFail($id);
        }

        $assignment->delete();

        return redirect()->route('coursecadremanager.index')->with('success', 'Assignment deleted successfully.');
    }
    public function bulkCompleteCourses(Request $request)
    {
        $courseIds = $request->course_ids;

        // Decode JSON if string
        if (is_string($courseIds)) {
            $courseIds = json_decode($courseIds, true) ?? [];
        }

        // ✅ Manual validation to redirect back with errors
        $validator = Validator::make(
            [
                'course_ids'       => $courseIds,
                'remarks'  => $request->completion_note,
            ],
            [
                'course_ids'       => 'required|array',
                'course_ids.*'     => 'exists:soldier_courses,id',
                'remarks'  => 'nullable|string|max:500',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator) // errors bag
                ->withInput();           // keep form input
        }

        // ✅ Safe validated data
        $data      = $validator->validated();
        $courseIds = $data['course_ids'];

        SoldierCourse::whereIn('id', $courseIds)->update([
            'completion_date' => now(),
            'remarks'         => $request->completion_note ?? null,
        ]);

        $count = count($courseIds);

        return redirect()->route('coursecadremanager.index')
            ->with('success', "{$count} course" . ($count > 1 ? 's' : '') . " marked as completed.");
    }

    public function bulkCompleteCadres(Request $request)
    {
        $cadreIds = $request->cadre_ids;

        // Decode JSON if string
        if (is_string($cadreIds)) {
            $cadreIds = json_decode($cadreIds, true) ?? [];
        }

        // ✅ Manually validate so we can redirect back with errors
        $validator = Validator::make(
            [
                'cadre_ids'       => $cadreIds,
                'remarks' => $request->completion_note,
            ],
            [
                'cadre_ids'       => 'required|array',
                'cadre_ids.*'     => 'exists:soldier_cadres,id',
                'remarks' => 'nullable|string|max:500',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator) // sends errors to session
                ->withInput();           // keep old input
        }

        // ✅ Safe validated data
        $data     = $validator->validated();
        $cadreIds = $data['cadre_ids'];

        SoldierCadre::whereIn('id', $cadreIds)->update([
            'completion_date' => now(),
            'remarks'         => $request->completion_note ?? null,
        ]);

        $count = count($cadreIds);

        return redirect()->route('coursecadremanager.index')
            ->with('success', "{$count} cadre" . ($count > 1 ? 's' : '') . " marked as completed.");
    }
}
