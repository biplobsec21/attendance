<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Appointment::query();

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

        $appointments = $query->paginate(20)->withQueryString();
        // dd($appointments);
        return view('mpm.page.appointment.crud.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('mpm.page.appointment.crud.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        Appointment::create($request->validated());

        return redirect()->route('appointments.index')->with('success', 'Cadre created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment): View
    {
        return view('mpm.page.appointment.crud.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment): View
    {
        return view('mpm.page.appointment.crud.edit', compact('appointment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {

        $appointment->update($request->validated());

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment): RedirectResponse
    {
        // Check if any soldiers are assigned to this cadre
        $appointments = $appointment->appoinmentsAssign()->count();

        if ($appointments > 0) {
            return redirect()->route('appointments.index')
                ->with('error', "This Appointment cannot be deleted because it has $appointments assigned appointments.");
        }

        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Appointment deleted successfully.');
    }

    /**
     * Toggle status of the cadre.
     */
    public function toggleStatus(Appointment $appointment): RedirectResponse
    {

        // dd($appointment->status);
        $appointment->update(['status' => !$appointment->status]);
        $message = $appointment->status ? 'Appointment activated successfully.' : 'appointment deactivated successfully.';
        return redirect()->route('appointments.index')->with('success', $message);
    }
}
