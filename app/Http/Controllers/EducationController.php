<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEducationRequest;
use App\Http\Requests\UpdateEducationRequest;
use App\Models\Education;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EducationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Education::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['id', 'name', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        $educations = $query->paginate(10)->withQueryString();

        return view('mpm.page.education.index', compact('educations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.education.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEducationRequest $request): RedirectResponse
    {
        $education = Education::create($request->validated());

        return redirect()->route('education.index')->with('success', 'Education created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Education $education): View
    {
        return view('mpm.page.education.show', compact('education'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Education $education): View
    {
        return view('mpm.page.education.edit', compact('education'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEducationRequest $request, Education $education): RedirectResponse
    {
        $education->update($request->validated());

        return redirect()->route('education.index')->with('success', 'Education updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Education $education): RedirectResponse
    {
        // Count linked soldiers
        $soldiersCount = $education->soldiers()->count();

        if ($soldiersCount > 0) {
            return redirect()->route('education.index')
                ->with('error', "This Education cannot be deleted because it has $soldiersCount assigned soldier(s).");
        }

        // Safe to delete
        $education->delete();

        return redirect()->route('education.index')->with('success', 'Education deleted successfully.');
    }


    public function toggleStatus(Education $education): RedirectResponse
    {
        $education->update(['status' => !$education->status]);
        $message = $education->status ? 'Education activated successfully.' : 'Education deactivated successfully.';
        return redirect()->route('education.index')->with('success', $message);
    }
}
