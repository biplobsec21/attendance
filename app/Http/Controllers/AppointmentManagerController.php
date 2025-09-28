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
use Carbon\Carbon;

class AppointmentManagerController extends Controller
{
    /**
     * Show the form for creating a new appointment manager.
     */
    public function index()
    {

        // Check if we need to update statuses (run at most once per day)
        $lastRunKey = 'appointment_status_last_run';
        $today = now()->toDateString();
        if (cache()->get($lastRunKey) !== $today) {
            \Artisan::call('appointments:update-statuses');
            cache()->put($lastRunKey, $today, now()->addDay());
        }

        // Get all current appointments with relationships
        $currentServices = SoldierServices::where('status', '!=', 'completed')
            ->with(['soldier.rank', 'soldier.company', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->get();

        $previousServices = SoldierServices::completed()
            ->with(['soldier.rank', 'soldier.company', 'appointment'])
            ->orderBy('appointments_to_date', 'desc')
            ->get();
        // dd($previousServices);
        return view('mpm.page.appointment.index', compact('currentServices', 'previousServices'));
    }

    public function create()
    {
        $appointments = Appointment::active()->get();
        $soldiers = Soldier::where('status', true)->with(['rank', 'company'])->get();
        $ranks = Rank::all();
        $companies = Company::all();

        // Get soldiers with current appointments
        $assignedSoldierIds = $assignedSoldierIds = SoldierServices::whereIn('status', ['active', 'scheduled'])
            ->pluck('soldier_id')
            ->toArray();
        // Get soldiers with their active assignments using the new model methods
        $soldiers = Soldier::with(['rank', 'company', 'activeServices'])->get();

        // Separate soldiers into available and assigned
        $availableSoldiers = $soldiers->reject(function ($soldier) {
            return $soldier->hasActiveAssignments();
        });

        $assignedSoldiers = $soldiers->filter(function ($soldier) {
            return $soldier->hasActiveAssignments();
        });
        return view('mpm.page.appointment.create', compact(
            'appointments',
            'soldiers',
            'ranks',
            'companies',
            'assignedSoldierIds', // Pass to view
            'availableSoldiers',    // Changed from 'soldiers'
            'assignedSoldiers',     // New variable
        ));
    }

    /**
     * Store a newly created appointment manager.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'appointment_id' => 'required|exists:appointments,id',
        //     'soldier_ids'    => 'required|array|min:1',
        //     'soldier_ids.*'  => 'exists:soldiers,id',
        //     'note'           => 'nullable|string|max:500',
        // ]);
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'soldier_ids'    => 'required|array|min:1',
            'soldier_ids.*'  => 'exists:soldiers,id',
            'appointments_from_date' => 'required|date|after_or_equal:today',
            'appointments_to_date'   => 'nullable|date|after:appointments_from_date',
            'note'           => 'nullable|string|max:300',
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
            foreach ($request->soldier_ids as $soldierId) {
                // Check for overlapping appointments
                $this->validateAppointmentDates($soldierId, $request->appointments_from_date, $request->appointments_to_date);

                // Adjust start date if needed
                $adjustedStartDate = $this->getAdjustedStartDate($soldierId, $request->appointments_from_date);

                // Determine status
                $status = $this->determineStatus($adjustedStartDate, $request->appointments_to_date);

                SoldierServices::create([
                    'soldier_id'             => $soldierId,
                    'appointment_id'         => $request->appointment_id,
                    'appointments_name'      => $appointment->name,
                    'appointment_type'       => 'current',
                    'appointments_from_date' => $adjustedStartDate,
                    'appointments_to_date'   => $request->appointments_to_date,
                    'status'                 => $status,
                    'note'                   => $request->note,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('appointmanager.index')
                ->with('success', 'Appointments assigned successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create appointments: ' . $e->getMessage());

            return redirect()
                ->route('appointmanager.index')
                ->with('error', 'Failed to assign appointments: ' . $e->getMessage());
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
            'appointments_from_date' => 'required|date',
            'appointments_to_date'   => 'nullable|date|after:appointments_from_date',
            'note'           => 'nullable|string|max:500',
        ]);

        $service = SoldierServices::findOrFail($id);
        $appointment = Appointment::find($request->appointment_id);

        if (!$appointment) {
            return redirect()
                ->route('appointmanager.index')
                ->with('error', 'Selected appointment not found.');
        }

        // Check for overlapping appointments (excluding current one)
        $this->validateAppointmentDates(
            $service->soldier_id,
            $request->appointments_from_date,
            $request->appointments_to_date,
            $service->id
        );

        DB::beginTransaction();

        try {
            // Determine status
            $status = $this->determineStatus($request->appointments_from_date, $request->appointments_to_date);

            $service->update([
                'appointment_id'    => $request->appointment_id,
                'appointments_name' => $appointment->name,
                'appointments_from_date' => $request->appointments_from_date,
                'appointments_to_date'   => $request->appointments_to_date,
                'status'           => $status,
                'note'             => $request->note,
                'updated_at'       => now(),
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
                ->with('error', 'Failed to update appointment: ' . $e->getMessage());
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

        if ($service->status === 'completed') {
            return redirect()
                ->route('appointmanager.index')
                ->with('error', 'This appointment is already completed.');
        }

        DB::beginTransaction();

        try {
            $service->update([
                'appointments_to_date'  => Carbon::today()->toDateString(),
                'status'                => 'completed',
                'appointment_type'      => 'previous',
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

    /**
     * Validate appointment dates to prevent overlaps
     */
    private function validateAppointmentDates($soldierId, $fromDate, $toDate, $excludeId = null)
    {
        $fromDate = Carbon::parse($fromDate);
        $toDate = $toDate ? Carbon::parse($toDate) : null;

        $query = SoldierServices::where('soldier_id', $soldierId)
            ->where('id', '!=', $excludeId)
            ->whereIn('status', ['active', 'scheduled'])
            ->where(function ($q) use ($fromDate, $toDate) {
                // Check for any overlap
                $q->where(function ($query) use ($fromDate, $toDate) {
                    // Existing appointment starts before or on new end date
                    $query->where('appointments_from_date', '<=', $toDate ?? $fromDate)
                        // And ends after or on new start date
                        ->where(function ($q) use ($fromDate) {
                            $q->whereNull('appointments_to_date')
                                ->orWhere('appointments_to_date', '>=', $fromDate);
                        });
                });
            });

        if ($query->exists()) {
            throw new \Exception('Soldier has overlapping appointments on these dates');
        }
    }

    /**
     * Get adjusted start date to prevent same-day assignments
     */
    private function getAdjustedStartDate($soldierId, $requestedDate)
    {
        $requestedDate = Carbon::parse($requestedDate);

        // Check if soldier has an appointment ending today (regardless of status)
        $endingToday = SoldierServices::where('soldier_id', $soldierId)
            ->whereDate('appointments_to_date', Carbon::today())
            ->exists();

        // If appointment ends today and requested date is today, set to tomorrow
        if ($endingToday && $requestedDate->isToday()) {
            return Carbon::tomorrow()->toDateString();
        }

        return $requestedDate->toDateString();
    }

    /**
     * Determine appointment status based on dates
     */
    private function determineStatus($fromDate, $toDate)
    {
        $fromDate = Carbon::parse($fromDate);
        $today = Carbon::today();

        if ($toDate) {
            $toDate = Carbon::parse($toDate);
            if ($toDate->lt($today)) {
                return 'completed';
            }
        }

        return $fromDate->lte($today) ? 'active' : 'scheduled';
    }
}
