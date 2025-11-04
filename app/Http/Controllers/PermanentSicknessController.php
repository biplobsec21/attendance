<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermanentSicknessRequest;
use App\Http\Requests\UpdatePermanentSicknessRequest;
use App\Models\PermanentSickness;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PermanentSicknessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = PermanentSickness::withCount('soldiers');

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

        $permanentSicknesses = $query->paginate(10)->withQueryString();

        return view('mpm.page.permanent-sicknesses.index', compact('permanentSicknesses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.permanent-sicknesses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermanentSicknessRequest $request): RedirectResponse
    {
        PermanentSickness::create($request->only(['name']));

        return redirect()->route('permanent-sickness.index')
            ->with('success', 'Permanent sickness created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PermanentSickness $permanentSickness): View
    {
        // Load soldiers with pivot data for the show page
        $permanentSickness->load(['soldiers' => function ($query) {
            $query->orderBy('soldier_permanent_sickness.created_at', 'desc');
        }]);

        return view('mpm.page.permanent-sicknesses.show', compact('permanentSickness'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PermanentSickness $permanentSickness): View
    {
        return view('mpm.page.permanent-sicknesses.edit', compact('permanentSickness'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermanentSicknessRequest $request, PermanentSickness $permanentSickness): RedirectResponse
    {
        $permanentSickness->update($request->only(['name']));

        return redirect()->route('permanent-sickness.index')
            ->with('success', 'Permanent sickness updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PermanentSickness $permanentSickness): RedirectResponse
    {
        try {
            // Check if sickness is being used by any soldiers
            if ($permanentSickness->soldiers()->exists()) {
                return redirect()
                    ->back()
                    ->with('error', 'Cannot delete permanent sickness. It is being used by soldiers.');
            }

            $permanentSickness->delete();

            return redirect()
                ->route('permanent-sickness.index')
                ->with('success', 'Permanent sickness deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete permanent sickness. Please try again.');
        }
    }

    /**
     * Get permanent sicknesses data for API/AJAX requests.
     */
    public function getPermanentSicknesses(Request $request)
    {
        $query = PermanentSickness::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $permanentSicknesses = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $permanentSicknesses->map(function ($sickness) {
                return [
                    'id' => $sickness->id,
                    'name' => $sickness->name,
                    'soldiers_count' => $sickness->soldiers_count,
                    'created_at' => $sickness->created_at->format('M d, Y'),
                    'actions' => [
                        'show' => route('permanent-sickness.show', $sickness),
                        'edit' => route('permanent-sickness.edit', $sickness),
                        'destroy' => route('permanent-sickness.destroy', $sickness),
                    ]
                ];
            })
        ]);
    }
}
