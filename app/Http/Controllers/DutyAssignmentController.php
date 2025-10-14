<?php

namespace App\Http\Controllers;

use App\Services\DutyAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DutyAssignmentController extends Controller
{
    protected $dutyService;

    public function __construct(DutyAssignmentService $dutyService)
    {
        $this->dutyService = $dutyService;
    }

    /**
     * Display the duty assignment dashboard
     */
    public function index()
    {
        return view('mpm.page.duty-assignments.index');
    }

    /**
     * Assign duties for a specific date
     */
    public function assignForDate(Request $request)
    {
        // dd($request->all());
        $request->validate([
            // 'date' => 'required|date|after_or_equal:' . now()->format('Y-m-d')
            'date' => 'nullable'
        ]);

        try {
            $this->dutyService->assignDutiesForDate($request->date);

            return response()->json([
                'success' => true,
                'message' => 'Duties assigned successfully',
                'date' => $request->date
            ]);
        } catch (\Exception $e) {
            Log::error('Duty assignment failed', [
                'date' => $request->date,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign duties',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign duties for a date range
     */
    public function assignForDateRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        try {
            $result = $this->dutyService->assignDutiesForDateRange(
                $request->start_date,
                $request->end_date
            );

            return response()->json([
                'success' => true,
                'message' => 'Date range processed',
                'assigned_dates' => $result['assigned_dates'],
                'errors' => $result['errors'],
                'success_rate' => count($result['assigned_dates']) . '/' .
                    (count($result['assigned_dates']) + count($result['errors']))
            ]);
        } catch (\Exception $e) {
            Log::error('Date range assignment failed', [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process date range',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assignment statistics
     */
    public function statistics(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        try {
            $stats = $this->dutyService->getAssignmentStatistics($request->date);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get statistics', [
                'date' => $request->date,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed duty assignments for a specific date
     */
    public function details(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        try {
            $details = $this->dutyService->getDutyDetailsForDate($request->date);

            return response()->json([
                'success' => true,
                'data' => $details
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get duty details', [
                'date' => $request->date,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve duty details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check unfulfilled duties
     */
    public function unfulfilled(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        try {
            $unfulfilled = $this->dutyService->getUnfulfilledDuties($request->date);

            return response()->json([
                'success' => true,
                'has_unfulfilled' => !empty($unfulfilled),
                'data' => $unfulfilled
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get unfulfilled duties', [
                'date' => $request->date,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve unfulfilled duties',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if soldier can be assigned
     */
    public function checkEligibility(Request $request)
    {
        $request->validate([
            'soldier_id' => 'required|integer',
            'duty_id' => 'required|integer',
            'date' => 'required|date'
        ]);

        try {
            $result = $this->dutyService->canAssignSoldierToDuty(
                $request->soldier_id,
                $request->duty_id,
                $request->date
            );

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Eligibility check failed', [
                'soldier_id' => $request->soldier_id,
                'duty_id' => $request->duty_id,
                'date' => $request->date,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check eligibility',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reassign a soldier
     */
    public function reassign(Request $request)
    {
        $request->validate([
            'soldier_id' => 'required|integer',
            'from_duty_id' => 'required|integer',
            'to_duty_id' => 'required|integer',
            'date' => 'required|date'
        ]);

        try {
            $result = $this->dutyService->reassignSoldier(
                $request->soldier_id,
                $request->from_duty_id,
                $request->to_duty_id,
                $request->date
            );

            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            Log::error('Reassignment failed', [
                'soldier_id' => $request->soldier_id,
                'from_duty_id' => $request->from_duty_id,
                'to_duty_id' => $request->to_duty_id,
                'date' => $request->date,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reassign soldier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel assignment
     */
    public function cancel(Request $request)
    {
        $request->validate([
            'soldier_id' => 'required|integer',
            'duty_id' => 'required|integer',
            'date' => 'required|date'
        ]);

        try {
            $cancelled = $this->dutyService->cancelDutyAssignment(
                $request->soldier_id,
                $request->duty_id,
                $request->date
            );

            return response()->json([
                'success' => $cancelled,
                'message' => $cancelled ? 'Assignment cancelled successfully' : 'Assignment not found'
            ]);
        } catch (\Exception $e) {
            Log::error('Cancellation failed', [
                'soldier_id' => $request->soldier_id,
                'duty_id' => $request->duty_id,
                'date' => $request->date,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel assignment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export duty assignments to CSV
     */
    public function export(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        try {
            $details = $this->dutyService->getDutyDetailsForDate($request->date);
            $date = $request->date;

            // Generate CSV content
            $filename = "duty_assignments_{$date}.csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($details, $date) {
                $file = fopen('php://output', 'w');

                // Write report header
                fputcsv($file, ['MILITARY DUTY ASSIGNMENT REPORT']);
                fputcsv($file, ['Date:', $date]);
                fputcsv($file, ['Generated:', date('Y-m-d H:i:s')]);
                fputcsv($file, []);

                // Write table headers
                fputcsv($file, [
                    'Section',
                    'Duty Name',
                    'Duty ID',
                    'Time',
                    'Duration',
                    'Required Manpower',
                    'Assigned Count',
                    'Fulfillment Rate',
                    'Rank Name',
                    'Rank Manpower',
                    'Soldier ID',
                    'Army No',
                    'Full Name',
                    'Rank',
                    'Company',
                    'Duty Type'
                ]);

                // Process roster duties
                if (isset($details['roster_duties']) && is_array($details['roster_duties'])) {
                    foreach ($details['roster_duties'] as $duty) {
                        // Format time for display
                        $startTime = $duty['start_time'] instanceof \Illuminate\Support\Carbon
                            ? $duty['start_time']->format('H:i')
                            : $duty['start_time'];
                        $endTime = $duty['end_time'] instanceof \Illuminate\Support\Carbon
                            ? $duty['end_time']->format('H:i')
                            : $duty['end_time'];

                        // Process each rank requirement
                        if (isset($duty['rank_requirements']) && is_array($duty['rank_requirements'])) {
                            foreach ($duty['rank_requirements'] as $requirement) {
                                // Process each soldier assigned to this duty
                                if (isset($duty['assigned_soldiers']) && is_array($duty['assigned_soldiers'])) {
                                    foreach ($duty['assigned_soldiers'] as $soldier) {
                                        fputcsv($file, [
                                            'ROSTER DUTY',
                                            $duty['duty_name'],
                                            $duty['duty_id'],
                                            $startTime . ' - ' . $endTime,
                                            $duty['duration_days'] . ' day',
                                            $duty['required_manpower'],
                                            $duty['assigned_count'],
                                            $duty['fulfillment_rate'] . '%',
                                            $requirement['rank_name'] ?? 'N/A',
                                            $requirement['manpower'] ?? 0,
                                            $soldier['soldier_id'],
                                            $soldier['army_no'],
                                            $soldier['full_name'],
                                            $soldier['rank'],
                                            $soldier['company'],
                                            'Roster'
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                // Process fixed duties
                if (isset($details['fixed_duties'])) {
                    // Convert to array if it's a collection
                    $fixedDuties = $details['fixed_duties'] instanceof \Illuminate\Support\Collection
                        ? $details['fixed_duties']->toArray()
                        : $details['fixed_duties'];

                    if (is_array($fixedDuties) && count($fixedDuties) > 0) {
                        foreach ($fixedDuties as $duty) {
                            // Format time for display
                            $startTime = $duty['start_time'] instanceof \Illuminate\Support\Carbon
                                ? $duty['start_time']->format('H:i')
                                : $duty['start_time'];
                            $endTime = $duty['end_time'] instanceof \Illuminate\Support\Carbon
                                ? $duty['end_time']->format('H:i')
                                : $duty['end_time'];

                            fputcsv($file, [
                                'FIXED DUTY',
                                $duty['duty_name'],
                                $duty['duty_id'],
                                $startTime . ' - ' . $endTime,
                                '1 day',
                                1,
                                1,
                                '100%',
                                $duty['rank'], // Use the soldier's rank
                                1,
                                $duty['soldier_id'],
                                $duty['army_no'],
                                $duty['full_name'],
                                $duty['rank'],
                                $duty['company'],
                                'Fixed'
                            ]);
                        }
                    }
                }

                // Write summary statistics
                fputcsv($file, []);
                fputcsv($file, ['SUMMARY STATISTICS']);
                fputcsv($file, ['Total Duties:', $details['summary']['total_duties']]);
                fputcsv($file, ['Total Assignments:', $details['summary']['total_assignments']]);
                fputcsv($file, ['Unique Soldiers:', $details['summary']['unique_soldiers']]);
                fputcsv($file, ['Unfulfilled Duties:', $details['summary']['unfulfilled_duties']]);
                fputcsv($file, ['Average Duties/Soldier:', $details['summary']['average_duties_per_soldier']]);

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Export failed', [
                'date' => $request->date,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }
    /**
     * Manually assign a soldier to a duty
     */
    public function assignSoldier(Request $request)
    {
        $request->validate([
            'soldier_id' => 'required|integer|exists:soldiers,id',
            'duty_id' => 'required|integer|exists:duties,id',
            'date' => 'required|date|after_or_equal:today',
            'force_assignment' => 'sometimes|boolean' // Optional flag to override some checks
        ]);

        try {
            $force = $request->boolean('force_assignment', false);

            // If force assignment is requested, we might want to bypass some checks
            // You can modify the service method to accept this parameter
            $result = $this->dutyService->assignSoldierToDuty(
                $request->soldier_id,
                $request->duty_id,
                $request->date
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'assignment' => $result['assignment_details'] ?? null,
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'reasons' => $result['reasons'] ?? [],
                    'data' => $result
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Manual soldier assignment failed', [
                'soldier_id' => $request->soldier_id,
                'duty_id' => $request->duty_id,
                'date' => $request->date,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign soldier to duty',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get available soldiers for a duty
     */
    public function availableSoldiers(Request $request)
    {
        $request->validate([
            'duty_id' => 'required|integer|exists:duties,id',
            'date' => 'required|date'
        ]);

        try {
            $soldiers = $this->dutyService->getAvailableSoldiersForDuty(
                $request->duty_id,
                $request->date
            );

            return response()->json([
                'success' => true,
                'data' => $soldiers
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get available soldiers', [
                'duty_id' => $request->duty_id,
                'date' => $request->date,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available soldiers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available duties for a soldier
     */
    public function availableDuties(Request $request)
    {
        $request->validate([
            'soldier_id' => 'required|integer|exists:soldiers,id',
            'date' => 'required|date',
            'exclude_duty_id' => 'sometimes|integer|exists:duties,id'
        ]);

        try {
            $duties = $this->dutyService->getAvailableDutiesForSoldier(
                $request->soldier_id,
                $request->date,
                $request->exclude_duty_id
            );

            return response()->json([
                'success' => true,
                'data' => $duties
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get available duties', [
                'soldier_id' => $request->soldier_id,
                'date' => $request->date,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available duties',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
