<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCmdRequest;
use App\Http\Requests\UpdateCmdRequest;
use App\Models\Cmd;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CmdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Cmd::query();

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

        $cmds = $query->paginate(10)->withQueryString();

        return view('mpm.page.cmd.index', compact('cmds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.cmd.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCmdRequest $request): RedirectResponse
    {
        Cmd::create($request->validated());

        return redirect()->route('cmds.index')->with('success', 'CMD created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cmd $cmd): View
    {
        return view('mpm.page.cmd.show', compact('cmd'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cmd $cmd): View
    {
        return view('mpm.page.cmd.edit', compact('cmd'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCmdRequest $request, Cmd $cmd): RedirectResponse
    {
        $cmd->update($request->validated());
        return redirect()->route('cmds.index')->with('success', 'CMD updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cmd $cmd): RedirectResponse
    {
        try {
            $cmd->delete();

            return redirect()
                ->route('cmds.index')
                ->with('success', 'Cmd deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete cmd. Please try again.');
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(Cmd $cmd): RedirectResponse
    {
        try {
            $cmd->update(['status' => !$cmd->status]);

            $message = $cmd->status ? 'Cmd activated successfully.' : 'Cmd deactivated successfully.';

            return redirect()
                ->route('cmds.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update cmd status. Please try again.');
        }
    }

    /**
     * Get cmds data for API/AJAX requests.
     */
    public function getCmds(Request $request)
    {
        $query = Cmd::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        $cmds = $query->orderBy('name', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $cmds->map(function ($cmd) {
                return [
                    'id' => $cmd->id,
                    'name' => $cmd->name,
                    'status' => $cmd->status_text,
                    'status_badge_class' => $cmd->status_badge_class,
                    'created_at' => $cmd->created_at->format('M d, Y'),
                    'actions' => [
                        'show' => route('cmds.show', $cmd),
                        'edit' => route('cmds.edit', $cmd),
                        'destroy' => route('cmds.destroy', $cmd),
                        'toggle_status' => route('cmds.toggle-status', $cmd),
                    ]
                ];
            })
        ]);
    }
}
