<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveTypeRequest;
use App\Http\Requests\UpdateLeaveTypeRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = LeaveType::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['name', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        $leaveTypes = $query->paginate(10)->withQueryString();

        return view('mpm.page.leaveType.index', compact('leaveTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.leaveType.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeaveTypeRequest $request): RedirectResponse
    {
        LeaveType::create($request->validated());
        return redirect()->route('leave-types.index')->with('success', 'Leave Type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveType $leaveType): View
    {
        return view('mpm.page.leaveType.show', compact('leaveType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveType $leaveType): View
    {
        return view('mpm.page.leaveType.edit', compact('leaveType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveTypeRequest $request, LeaveType $leaveType): RedirectResponse
    {
        $leaveType->update($request->validated());
        return redirect()->route('leave-types.index')->with('success', 'Leave Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveType $leaveType): RedirectResponse
    {
        try {
            $leaveType->delete();

            return redirect()
                ->route('leave-types.index')
                ->with('success', 'Leave Type deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete Leave Type. Please try again.');
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(LeaveType $leaveType): RedirectResponse
    {
        try {
            $leaveType->update(['status' => !$leaveType->status]);

            $message = $leaveType->status ? 'Leave Type activated successfully.' : 'Leave Type deactivated successfully.';

            return redirect()
                ->route('leave-types.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update Leave Type status. Please try again.');
        }
    }

    /**
     * Get Leave Types data for API/AJAX requests.
     */
    public function getLeaveTypes(Request $request)
    {
        $query = LeaveType::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        $leaveTypes = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $leaveTypes->map(function ($leaveType) {
                return [
                    'id' => $leaveType->id,
                    'name' => $leaveType->name,
                    'status' => $leaveType->status_text,
                    'status_badge_class' => $leaveType->status_badge_class,
                    'created_at' => $leaveType->created_at->format('M d, Y'),
                    'actions' => [
                        'show' => route('leave-types.show', $leaveType),
                        'edit' => route('leave-types.edit', $leaveType),
                        'destroy' => route('leave-types.destroy', $leaveType),
                        'toggle_status' => route('leave-types.toggle-status', $leaveType),
                    ]
                ];
            })
        ]);
    }
}
