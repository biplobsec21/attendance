<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Soldier;
use App\Models\Rank;
use App\Models\Company;
use App\Models\SoldierServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentManagerController extends Controller
{
    /**
     * Show the form for creating a new appointment manager.
     */
    public function index()
    {
        // Get all current appointments with relationships
        $currentServices = SoldierServices::current()
            ->with(['soldier.rank', 'soldier.company', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->get();

        $previousServices = SoldierServices::previous()
            ->with(['soldier.rank', 'soldier.company', 'appointment'])
            ->orderBy('appointments_to_date', 'desc')
            ->get();

        return view('mpm.page.appointment.index', compact('currentServices', 'previousServices'));
    }

    public function create()
    {
        $appointments = Appointment::active()->get();   // Only active appointments
        $soldiers     = Soldier::where('status', true)->with(['rank', 'company'])->get();
        $ranks        = Rank::all();
        $companies    = Company::all();

        return view('mpm.page.appointment.create', compact(
            'appointments',
            'soldiers',
            'ranks',
            'companies'
        ));
    }

    /**
     * Store a newly created appointment manager.
     */
    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'soldier_ids'    => 'required|array|min:1',
            'soldier_ids.*'  => 'exists:soldiers,id',
            'note'           => 'nullable|string|max:500',
        ]);

        // Find the appointment name from the provided appointment_id
        $appointment = Appointment::find($request->appointment_id);
        if (!$appointment) {
            return redirect()
                ->route('appointmanager.index')
                ->with('error', 'Appointment not found.');
        }

        DB::beginTransaction();

        try {
            // Loop through each soldier and create a new record
            foreach ($request->soldier_ids as $soldierId) {
                SoldierServices::create([
                    'soldier_id'             => $soldierId,
                    'appointment_id'         => $request->appointment_id,
                    'appointments_name'      => $appointment->name,
                    'appointment_type'       => 'current',
                    'appointments_from_date' => now(),
                    'note'                   => $request->note,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('appointmanager.index')
                ->with('success', 'Appointments assigned to soldiers successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create appointments: ' . $e->getMessage());

            return redirect()
                ->route('appointmanager.index')
                ->with('error', 'Failed to assign appointments. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $service = SoldierServices::with(['soldier.rank', 'soldier.company', 'appointment'])
            ->findOrFail($id);

        return view('mpm.page.appointment.show', compact('service'));
    }

    /**
     * Update the specified appointment.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'note'           => 'nullable|string|max:500',
        ]);

        $service = SoldierServices::findOrFail($id);
        $appointment = Appointment::find($request->appointment_id);

        if (!$appointment) {
            return redirect()
                ->route('appointmanager.index')
                ->with('error', 'Selected appointment not found.');
        }

        DB::beginTransaction();

        try {
            // Update the service record
            $service->update([
                'appointment_id'    => $request->appointment_id,
                'appointments_name' => $appointment->name,
                'note'              => $request->note,
                'updated_at'        => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('appointmanager.index')
                ->with('success', 'Appointment updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update appointment: ' . $e->getMessage());

            return redirect()
                ->route('appointmanager.index')
                ->with('error', 'Failed to update appointment. Please try again.');
        }
    }

    /**
     * Release soldier from current duty (move to previous appointments).
     */
    public function release(Request $request, $id)
    {
        $request->validate([
            'release_note' => 'nullable|string|max:500',
        ]);

        $service = SoldierServices::findOrFail($id);

        // Check if it's already a previous appointment
        if ($service->appointment_type === 'previous') {
            return redirect()
                ->route('appointmanager.index')
                ->with('error', 'This soldier is already released from duty.');
        }

        DB::beginTransaction();

        try {
            // Update the service record to mark as previous
            $service->update([
                'appointment_type'      => 'previous',
                'appointments_to_date'  => now(),
                'note'                  => $service->note .
                    ($request->release_note ? "\n\nRelease Note: " . $request->release_note : ''),
                'updated_at'            => now(),
            ]);

            DB::commit();

            return redirect()
                ->route('appointmanager.index')
                ->with('success', 'Soldier released from duty successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to release soldier from duty: ' . $e->getMessage());

            return redirect()
                ->route('appointmanager.index')
                ->with('error', 'Failed to release soldier from duty. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $service = SoldierServices::findOrFail($id);

        DB::beginTransaction();

        try {
            $service->delete();
            DB::commit();

            return redirect()
                ->route('appointmanager.index')
                ->with('success', 'Appointment deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete appointment: ' . $e->getMessage());

            return redirect()
                ->route('appointmanager.index')
                ->with('error', 'Failed to delete appointment. Please try again.');
        }
    }
}
