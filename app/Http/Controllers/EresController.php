<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEresRequest;
use App\Http\Requests\UpdateEresRequest;
use App\Models\Eres;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EresController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Eres::query();

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

        $eres = $query->paginate(10)->withQueryString();

        return view('mpm.page.ere.index', compact('eres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.ere.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEresRequest $request): RedirectResponse
    {
        Eres::create($request->validated());
        return redirect()->route('eres.index')->with('success', 'ERE created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Eres $ere): View
    {
        return view('mpm.page.ere.show', compact('ere'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Eres $ere): View
    {
        return view('mpm.page.ere.edit', compact('ere'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEresRequest $request, Eres $ere): RedirectResponse
    {
        $ere->update($request->validated());
        return redirect()->route('eres.index')->with('success', 'ERE updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Eres $ere): RedirectResponse
    {
        try {
            $ere->delete();

            return redirect()
                ->route('eres.index')
                ->with('success', 'Eres deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete Eres. Please try again.');
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(Eres $ere): RedirectResponse
    {
        try {
            $ere->update(['status' => !$ere->status]);

            $message = $ere->status ? 'Eres activated successfully.' : 'Eres deactivated successfully.';

            return redirect()
                ->route('eres.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update Eres status. Please try again.');
        }
    }

    /**
     * Get Eress data for API/AJAX requests.
     */
    public function getEress(Request $request)
    {
        $query = Eres::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        $eres = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $eres->map(function ($ere) {
                return [
                    'id' => $ere->id,
                    'name' => $ere->name,
                    'status' => $ere->status_text,
                    'status_badge_class' => $ere->status_badge_class,
                    'created_at' => $ere->created_at->format('M d, Y'),
                    'actions' => [
                        'show' => route('eres.show', $ere),
                        'edit' => route('eres.edit', $ere),
                        'destroy' => route('eres.destroy', $ere),
                        'toggle_status' => route('eres.toggle-status', $ere),
                    ]
                ];
            })
        ]);
    }
}
