<?php

namespace App\Http\Controllers;

// Import necessary classes
use App\Http\Requests\StoreDutyRequest;
use App\Http\Requests\UpdateDutyRequest;
use App\Models\Duty;
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
        $query = Duty::query();

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

        if (in_array($sortBy, ['duty_name', 'status', 'manpower', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Paginate the results and append query strings to pagination links
        $duties = $query->paginate(10)->withQueryString();

        return view('mpm.page.duty.index', compact('duties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.duty.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDutyRequest $request): RedirectResponse
    {
        // Validation is handled by StoreDutyRequest
        Duty::create($request->validated());

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
        return view('mpm.page.duty.edit', compact('duty'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDutyRequest $request, Duty $duty): RedirectResponse
    {
        // Validation is handled by UpdateDutyRequest
        $duty->update($request->validated());

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
}