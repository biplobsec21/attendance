<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstructionRecomendationRequest;
use App\Http\Requests\UpdateInstructionRecomendationRequest;
use App\Models\InstructionRecomendation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructionRecomendationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = InstructionRecomendation::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['id', 'title', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('id', 'asc');
        }

        $instructionRecomendations = $query->paginate(10)->withQueryString();

        return view('mpm.page.instruction-recomendation.index', compact('instructionRecomendations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.instruction-recomendation.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInstructionRecomendationRequest $request): RedirectResponse
    {
        InstructionRecomendation::create($request->validated());

        return redirect()->route('instruction-recomendations.index')->with('success', 'Instruction recommendation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InstructionRecomendation $instructionRecomendation): View
    {
        return view('mpm.page.instruction-recomendation.show', compact('instructionRecomendation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InstructionRecomendation $instructionRecomendation): View
    {
        return view('mpm.page.instruction-recomendation.edit', compact('instructionRecomendation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInstructionRecomendationRequest $request, InstructionRecomendation $instructionRecomendation): RedirectResponse
    {
        $instructionRecomendation->update($request->validated());

        return redirect()->route('instruction-recomendations.index')->with('success', 'Instruction recommendation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InstructionRecomendation $instructionRecomendation): RedirectResponse
    {
        $instructionRecomendation->delete();

        return redirect()->route('instruction-recomendations.index')->with('success', 'Instruction recommendation deleted successfully.');
    }

    /**
     * Toggle status of the instruction recommendation.
     */
    public function toggleStatus(InstructionRecomendation $instructionRecomendation): RedirectResponse
    {
        $instructionRecomendation->update(['status' => !$instructionRecomendation->status]);
        $message = $instructionRecomendation->status ? 'Instruction recommendation activated successfully.' : 'Instruction recommendation deactivated successfully.';
        return redirect()->route('instruction-recomendations.index')->with('success', $message);
    }
}
