<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalCategoryRequest;
use App\Http\Requests\UpdateMedicalCategoryRequest;
use App\Models\MedicalCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MedicalCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = MedicalCategory::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['name', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        $medicalCategories = $query->paginate(10)->withQueryString();

        return view('mpm.page.medical-categories.index', compact('medicalCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.medical-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicalCategoryRequest $request): RedirectResponse
    {
        MedicalCategory::create($request->only(['name']));

        return redirect()->route('medical-categories.index')
            ->with('success', 'Medical category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalCategory $medicalCategory): View
    {
        return view('mpm.page.medical-categories.show', compact('medicalCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalCategory $medicalCategory): View
    {
        return view('mpm.page.medical-categories.edit', compact('medicalCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicalCategoryRequest $request, MedicalCategory $medicalCategory): RedirectResponse
    {
        $medicalCategory->update($request->only(['name']));

        return redirect()->route('medical-categories.index')
            ->with('success', 'Medical category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalCategory $medicalCategory): RedirectResponse
    {
        try {
            // Check if category is being used by any soldiers
            if ($medicalCategory->soldiers()->exists()) {
                return redirect()
                    ->back()
                    ->with('error', 'Cannot delete medical category. It is being used by soldiers.');
            }

            $medicalCategory->delete();

            return redirect()
                ->route('medical-categories.index')
                ->with('success', 'Medical category deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete medical category. Please try again.');
        }
    }

    /**
     * Get medical categories data for API/AJAX requests.
     */
    public function getMedicalCategories(Request $request)
    {
        $query = MedicalCategory::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $medicalCategories = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $medicalCategories->map(function ($medicalCategory) {
                return [
                    'id' => $medicalCategory->id,
                    'name' => $medicalCategory->name,
                    'created_at' => $medicalCategory->created_at->format('M d, Y'),
                    'actions' => [
                        'show' => route('medical-categories.show', $medicalCategory),
                        'edit' => route('medical-categories.edit', $medicalCategory),
                        'destroy' => route('medical-categories.destroy', $medicalCategory),
                    ]
                ];
            })
        ]);
    }
}
