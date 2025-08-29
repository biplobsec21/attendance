<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttsRequest;
use App\Http\Requests\UpdateAttsRequest;
use App\Models\Atts;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AttsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Atts::query();

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

        $atts = $query->paginate(10)->withQueryString();

        return view('mpm.page.att.index', compact('atts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.att.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttsRequest $request): RedirectResponse
    {
        Atts::create($request->validated());

        return redirect()->route('atts.index')->with('success', 'ATT created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Atts $att): View
    {
        return view('mpm.page.att.show', compact('att'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Atts $att): View
    {
        return view('mpm.page.att.edit', compact('att'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttsRequest $request, Atts $att): RedirectResponse
    {
        $att->update($request->validated());
        return redirect()->route('atts.index')->with('success', 'ATT updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Atts $att): RedirectResponse
    {
        try {
            $att->delete();

            return redirect()
                ->route('atts.index')
                ->with('success', 'Att deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete att. Please try again.');
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(Atts $att): RedirectResponse
    {
        try {
            $att->update(['status' => !$att->status]);

            $message = $att->status ? 'Att activated successfully.' : 'Att deactivated successfully.';

            return redirect()
                ->route('atts.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update att status. Please try again.');
        }
    }

    /**
     * Get ranks data for API/AJAX requests.
     */
    public function getAtts(Request $request)
    {
        $query = Atts::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        $atts = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $atts->map(function ($att) {
                return [
                    'id' => $att->id,
                    'name' => $att->name,
                    'status' => $att->status_text,
                    'status_badge_class' => $att->status_badge_class,
                    'created_at' => $att->created_at->format('M d, Y'),
                    'actions' => [
                        'show' => route('atts.show', $att),
                        'edit' => route('atts.edit', $att),
                        'destroy' => route('atts.destroy', $att),
                        'toggle_status' => route('atts.toggle-status', $att),
                    ]
                ];
            })
        ]);
    }
}
