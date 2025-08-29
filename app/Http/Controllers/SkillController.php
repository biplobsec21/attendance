<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Models\Skill;
use App\Models\SkillCategory;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Skill::query();

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

        $skills  = $query->paginate(10)->withQueryString();

        return view('mpm.page.skill.index', compact('skills'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = SkillCategory::get(); // only active categories
        return view('mpm.page.skill.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSkillRequest $request): RedirectResponse
    {
        $skillcategories = Skill::create($request->validated());

        return redirect()->route('skill.index')->with('success', 'Skill created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Skill $skill): View
    {
        return view('mpm.page.skill.show', compact('skill'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Skill $skill): View
    {
        $categories = SkillCategory::get(); // show only active categories
        return view('mpm.page.skill.edit', compact('skill', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSkillRequest $request, Skill $skill): RedirectResponse
    {
        $skill->update($request->validated());

        return redirect()->route('skill.index')->with('success', 'Skill updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill): RedirectResponse
    {
        // Count linked soldiers
        $skill = $skill->soldiers()->count();

        if ($skill > 0) {
            return redirect()->route('skill.index')
                ->with('error', "This skill cannot be deleted because it has $skill assigned soldier(s).");
        }

        // Safe to delete
        $skill->delete();

        return redirect()->route('skill.index')->with('success', 'Skill deleted successfully.');
    }


    public function toggleStatus(Skill $skill): RedirectResponse
    {
        $skill->update(['status' => !$skill->status]);
        $message = $skill->status ? 'Skill activated successfully.' : 'Skill deactivated successfully.';
        return redirect()->route('skill.index')->with('success', $message);
    }
}
