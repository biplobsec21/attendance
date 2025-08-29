<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkillCategoryRequest;
use App\Http\Requests\UpdateSkillCategoryRequest;
use App\Models\SkillCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkillCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = SkillCategory::query();

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

        $skillCategories  = $query->paginate(10)->withQueryString();

        return view('mpm.page.skillcategory.index', compact('skillCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.skillcategory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSkillCategoryRequest $request): RedirectResponse
    {
        $skillcategories = SkillCategory::create($request->validated());

        return redirect()->route('skillcategory.index')->with('success', 'SkillCategory created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SkillCategory $skillcategory): View
    {
        return view('mpm.page.skillcategory.show', compact('skillcategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SkillCategory $skillcategory): View
    {
        return view('mpm.page.skillcategory.edit', compact('skillcategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSkillCategoryRequest $request, SkillCategory $skillcategory): RedirectResponse
    {
        $skillcategory->update($request->validated());

        return redirect()->route('skillcategory.index')->with('success', 'Skill Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SkillCategory $skillcategory): RedirectResponse
    {
        // Count linked soldiers
        $skill = $skillcategory->skills()->count();

        if ($skill > 0) {
            return redirect()->route('skillcategory.index')
                ->with('error', "This skillcategory cannot be deleted because it has $skill assigned soldier(s).");
        }

        // Safe to delete
        $skillcategory->delete();

        return redirect()->route('skillcategory.index')->with('success', 'Skill Category deleted successfully.');
    }


    public function toggleStatus(SkillCategory $skillcategory): RedirectResponse
    {
        $skillcategory->update(['status' => !$skillcategory->status]);
        $message = $skillcategory->status ? 'skillcategory activated successfully.' : 'skillcategory deactivated successfully.';
        return redirect()->route('skillcategory.index')->with('success', $message);
    }
}
