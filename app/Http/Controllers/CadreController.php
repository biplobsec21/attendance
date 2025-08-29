<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCadreRequest;
use App\Http\Requests\UpdateCadreRequest;
use App\Models\Cadre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CadreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Cadre::query();

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
            $query->orderBy('id', 'asc');
        }

        $cadres = $query->paginate(10)->withQueryString();

        return view('mpm.page.cadre.index', compact('cadres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.cadre.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCadreRequest $request): RedirectResponse
    {
        Cadre::create($request->validated());

        return redirect()->route('cadres.index')->with('success', 'Cadre created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cadre $cadre): View
    {
        return view('mpm.page.cadre.show', compact('cadre'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cadre $cadre): View
    {
        return view('mpm.page.cadre.edit', compact('cadre'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCadreRequest $request, Cadre $cadre): RedirectResponse
    {

        $cadre->update($request->validated());

        return redirect()->route('cadres.index')->with('success', 'Cadre updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cadre $cadre): RedirectResponse
    {
        // Check if any soldiers are assigned to this cadre
        $soldiersCount = $cadre->soldiers()->count();

        if ($soldiersCount > 0) {
            return redirect()->route('cadres.index')
                ->with('error', "This cadre cannot be deleted because it has $soldiersCount assigned soldier(s).");
        }

        $cadre->delete();

        return redirect()->route('cadres.index')->with('success', 'Cadre deleted successfully.');
    }

    /**
     * Toggle status of the cadre.
     */
    public function toggleStatus(Cadre $cadre): RedirectResponse
    {

        $cadre->update(['status' => !$cadre->status]);
        $message = $cadre->status ? 'Cadre activated successfully.' : 'Cadre deactivated successfully.';
        return redirect()->route('cadres.index')->with('success', $message);
    }
}
