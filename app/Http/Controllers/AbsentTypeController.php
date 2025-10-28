<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAbsentTypeRequest;
use App\Http\Requests\UpdateAbsentTypeRequest;
use App\Models\AbsentType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AbsentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = AbsentType::query();

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

        $absentTypes = $query->paginate(10)->withQueryString();

        return view('mpm.page.absent-type.index', compact('absentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.absent-type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAbsentTypeRequest $request): RedirectResponse
    {
        AbsentType::create($request->validated());

        return redirect()->route('absent-types.index')->with('success', 'Absent Type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AbsentType $absentType): View
    {
        return view('mpm.page.absent-type.show', compact('absentType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AbsentType $absentType): View
    {
        return view('mpm.page.absent-type.edit', compact('absentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAbsentTypeRequest $request, AbsentType $absentType): RedirectResponse
    {
        $absentType->update($request->validated());
        return redirect()->route('absent-types.index')->with('success', 'Absent Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AbsentType $absentType): RedirectResponse
    {
        try {
            $absentType->delete();

            return redirect()
                ->route('absent-types.index')
                ->with('success', 'Absent Type deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete absent type. Please try again.');
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(AbsentType $absentType): RedirectResponse
    {
        try {
            $absentType->update(['status' => !$absentType->status]);

            $message = $absentType->status ? 'Absent Type activated successfully.' : 'Absent Type deactivated successfully.';

            return redirect()
                ->route('absent-types.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update absent type status. Please try again.');
        }
    }

    /**
     * Get absent types data for API/AJAX requests.
     */
    public function getAbsentTypes(Request $request)
    {
        $query = AbsentType::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        $absentTypes = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $absentTypes->map(function ($absentType) {
                return [
                    'id' => $absentType->id,
                    'name' => $absentType->name,
                    'status' => $absentType->status_text,
                    'status_badge_class' => $absentType->status_badge_class,
                    'created_at' => $absentType->created_at->format('M d, Y'),
                    'actions' => [
                        'show' => route('absent-types.show', $absentType),
                        'edit' => route('absent-types.edit', $absentType),
                        'destroy' => route('absent-types.destroy', $absentType),
                        'toggle_status' => route('absent-types.toggle-status', $absentType),
                    ]
                ];
            })
        ]);
    }
}
