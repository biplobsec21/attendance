<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExAreaRequest;
use App\Http\Requests\UpdateExAreaRequest;
use App\Models\ExArea;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = ExArea::query();

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

        $exAreas = $query->paginate(10)->withQueryString();

        return view('mpm.page.ex-area.index', compact('exAreas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.ex-area.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExAreaRequest $request): RedirectResponse
    {
        ExArea::create($request->validated());

        return redirect()->route('ex-areas.index')->with('success', 'Exercise Area created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ExArea $exArea): View
    {
        return view('mpm.page.ex-area.show', compact('exArea'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExArea $exArea): View
    {
        return view('mpm.page.ex-area.edit', compact('exArea'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExAreaRequest $request, ExArea $exArea): RedirectResponse
    {
        $exArea->update($request->validated());
        return redirect()->route('ex-areas.index')->with('success', 'Exercise Area updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExArea $exArea): RedirectResponse
    {
        try {
            $exArea->delete();

            return redirect()
                ->route('ex-areas.index')
                ->with('success', 'Exercise Area deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete exercise area. Please try again.');
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(ExArea $exArea): RedirectResponse
    {
        try {
            $exArea->update(['status' => !$exArea->status]);

            $message = $exArea->status ? 'Exercise Area activated successfully.' : 'Exercise Area deactivated successfully.';

            return redirect()
                ->route('ex-areas.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update exercise area status. Please try again.');
        }
    }

    /**
     * Get exercise areas data for API/AJAX requests.
     */
    public function getExAreas(Request $request)
    {
        $query = ExArea::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        $exAreas = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $exAreas->map(function ($exArea) {
                return [
                    'id' => $exArea->id,
                    'name' => $exArea->name,
                    'status' => $exArea->status_text,
                    'status_badge_class' => $exArea->status_badge_class,
                    'created_at' => $exArea->created_at->format('M d, Y'),
                    'actions' => [
                        'show' => route('ex-areas.show', $exArea),
                        'edit' => route('ex-areas.edit', $exArea),
                        'destroy' => route('ex-areas.destroy', $exArea),
                        'toggle_status' => route('ex-areas.toggle-status', $exArea),
                    ]
                ];
            })
        ]);
    }
}
