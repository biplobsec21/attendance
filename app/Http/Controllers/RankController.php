<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRankRequest;
use App\Http\Requests\UpdateRankRequest;
use App\Models\Rank;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Rank::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        // filter functionality
        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        // Sorting
        // This is the corrected line to set 'id' as the default.
        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['id', 'name', 'status', 'created_at', 'type'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        $ranks = $query->paginate(20)->withQueryString();

        return view('mpm.page.rank.index', compact('ranks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.rank.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRankRequest $request): RedirectResponse
    {
        try {
            $validatedData = $request->validated();

            // Debug: Log the validated data
            \Log::info('Creating rank with data:', $validatedData);

            $rank = Rank::create($validatedData);

            \Log::info('Rank created successfully:', ['id' => $rank->id, 'name' => $rank->name]);

            return redirect()
                ->route('ranks.index')
                ->with('success', 'Rank created successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to create rank:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create rank: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Rank $rank): View
    {
        return view('mpm.page.rank.show', compact('rank'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rank $rank): View
    {
        return view('mpm.page.rank.edit', compact('rank'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRankRequest $request, Rank $rank): RedirectResponse
    {
        try {
            $request->validated();
            $rank->update($request->validated());
            return redirect()
                ->route('ranks.index')
                ->with('success', 'Rank updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update rank. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rank $rank): RedirectResponse
    {
        try {
            $rank->delete();

            return redirect()
                ->route('ranks.index')
                ->with('success', 'Rank deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete rank. Please try again.');
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(Rank $rank): RedirectResponse
    {
        try {
            $rank->update(['status' => !$rank->status]);

            $message = $rank->status ? 'Rank activated successfully.' : 'Rank deactivated successfully.';

            return redirect()
                ->route('ranks.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update rank status. Please try again.');
        }
    }

    /**
     * Get ranks data for API/AJAX requests.
     */
    public function getRanks(Request $request)
    {
        $query = Rank::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        $ranks = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $ranks->map(function ($rank) {
                return [
                    'id' => $rank->id,
                    'name' => $rank->name,
                    'type' => $rank->type,
                    'status' => $rank->status_text,
                    'status_badge_class' => $rank->status_badge_class,
                    'created_at' => $rank->created_at->format('M d, Y'),
                    'actions' => [
                        'show' => route('ranks.show', $rank),
                        'edit' => route('ranks.edit', $rank),
                        'destroy' => route('ranks.destroy', $rank),
                        'toggle_status' => route('ranks.toggle-status', $rank),
                    ]
                ];
            })
        ]);
    }
}
