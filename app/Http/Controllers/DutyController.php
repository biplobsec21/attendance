<?php

namespace App\Http\Controllers;

// Import necessary classes
use App\Http\Requests\StoreDutyRequest;
use App\Http\Requests\UpdateDutyRequest;
use App\Models\Duty;
use App\Models\DutyRank;
use App\Models\Rank;
use App\Models\Soldier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DutyController extends Controller
{
    /**
     * Display a listing of the resource with search, sort, and pagination.
     */
    public function index(Request $request): View
    {
        $query = Duty::with(['dutyRanks.rank']); // Eager load the dutyRanks with their rank relationship

        // Server-side search functionality
        if ($request->filled('search')) {
            $query->where('duty_name', 'like', '%' . $request->search . '%')
                ->orWhere('remark', 'like', '%' . $request->search . '%');
        }

        // Server-side status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Server-side sorting
        $sortBy = $request->get('sort_by', 'created_at'); // Default sort column
        $sortDirection = $request->get('sort_direction', 'desc'); // Default sort direction

        if (in_array($sortBy, ['duty_name', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Get all duties without pagination
        $duties = $query->get();

        // Get all ranks for the filter dropdown
        $ranks = \App\Models\Rank::orderBy('name')->get();

        return view('mpm.page.duty.index', compact('duties', 'ranks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $ranks = Rank::all();
        return view('mpm.page.duty.create', compact('ranks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDutyRequest $request): RedirectResponse
    {
        // Create the duty record
        $duty = Duty::create($request->validated());

        // Get the rank_manpower data from the request
        $rankManpower = $request->input('rank_manpower', []);

        // Create duty_rank records for each selected rank with its specific manpower
        foreach ($rankManpower as $rankData) {
            DutyRank::create([
                'duty_id' => $duty->id,
                'rank_id' => $rankData['rank_id'],
                'duty_type' => 'roster',
                'manpower' => $rankData['manpower'],
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
            ]);
        }

        return redirect()->route('duty.index')->with('success', 'Duty record created successfully.');
    }

    /**
     * Display the specified resource. (Optional - good for a details page)
     */
    public function show(Duty $duty): View
    {
        return view('mpm.page.duty.show', compact('duty'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Duty $duty): View
    {
        $ranks = Rank::all();
        $selectedRanks = $duty->dutyRanks()->pluck('rank_id')->toArray();
        return view('mpm.page.duty.edit', compact('duty', 'ranks', 'selectedRanks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDutyRequest $request, Duty $duty): RedirectResponse
    {
        // Update the duty record
        $duty->update($request->validated());

        // Get the rank_manpower data from the request
        $rankManpower = $request->input('rank_manpower', []);

        // Delete existing duty_rank records for this duty
        DutyRank::where('duty_id', $duty->id)->delete();

        // Create new duty_rank records for each selected rank with its specific manpower
        foreach ($rankManpower as $rankData) {
            DutyRank::create([
                'duty_id' => $duty->id,
                'rank_id' => $rankData['rank_id'],
                'duty_type' => 'roster',
                'manpower' => $rankData['manpower'],
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
            ]);
        }

        return redirect()->route('duty.index')->with('success', 'Duty record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Duty $duty): RedirectResponse
    {
        // You could add checks here if this duty is linked to other models, like the CourseController example
        // For now, we will just delete it.
        $duty->delete();

        return redirect()->route('duty.index')->with('success', 'Duty record deleted successfully.');
    }


    public function assignList()
    {

        $assignments = DutyRank::with(['duty', 'rank'])->get();
        return view('mpm.page.duty.assignments', compact('assignments'));
    }

    public function createAssignments()
    {
        $duties = Duty::all();
        $ranks = Rank::all();
        $soldiers = Soldier::all();
        $assignments = DutyRank::with(['duty', 'rank'])->get();
        return view('mpm.page.duty.createAssign', compact('duties', 'ranks', 'soldiers'));
    }


    public function storeAssignments(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'duty_id' => 'required|exists:duties,id',
            'rank_id' => 'required|exists:ranks,id',
            'duty_type' => 'required|in:fixed,roster,regular',
            'priority' => 'nullable|integer|min:1',
            'rotation_days' => 'nullable|integer|min:1',
            'remarks' => 'nullable|string|max:255',
            'fixed_soldier_id' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->duty_type === 'fixed') {
                        $soldier = Soldier::where('id', $value)
                            ->where('rank_id', $request->rank_id)
                            ->first();

                        if (!$soldier) {
                            $fail('The selected soldier does not belong to the selected rank.');
                        }
                    }
                }
            ]


        ]);

        // Prevent duplicate assignment
        $exists = DutyRank::where('duty_id', $request->duty_id)
            ->where('rank_id', $request->rank_id)
            ->first();

        if ($exists) {
            // Send old input back + custom error message
            return back()
                ->withInput() // preserve all input values
                ->with('error', 'This duty is already assigned to this rank. Duty: '
                    . $exists->duty->duty_name
                    . ', Rank: ' . $exists->rank->name
                    . ', Type: ' . $exists->duty_type);
        }

        DutyRank::create($request->all());

        return redirect()->route('duty.assigntorank')->with('success', 'Duty assigned successfully.');
    }

    public function editAssignment($id)
    {
        $assignment = DutyRank::findOrFail($id);
        $duties = Duty::all();
        $ranks = Rank::all();

        return view('mpm.page.duty.editAssign', compact('assignment', 'duties', 'ranks'));
    }

    public function updateAssignment(Request $request, $id)
    {
        $assignment = DutyRank::findOrFail($id);

        $request->validate([
            'duty_id' => 'required|exists:duties,id',
            'rank_id' => 'required|exists:ranks,id',
            'duty_type' => 'required|in:fixed,roster,regular',
            'priority' => 'nullable|integer|min:1',
            'rotation_days' => 'nullable|integer|min:1',
            'remarks' => 'nullable|string|max:255',
        ]);

        $assignment->update($request->all());

        return redirect()->route('duty.assign')->with('success', 'Assignment updated successfully.');
    }

    public function deleteAssignment($id)
    {
        $assignment = DutyRank::findOrFail($id);
        $assignment->delete();

        return redirect()->route('duty.assign')->with('success', 'Assignment deleted successfully.');
    }
}
