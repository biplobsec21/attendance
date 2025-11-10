<?php

namespace App\Http\Controllers;

use App\Models\Duty;
use App\Models\SiteSetting;
use App\Models\SoldierDuty;
use App\Services\DutyAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PDF;
use Illuminate\Support\Carbon;

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
            'date' => 'required|date|after_or_equal:' . now()->format('Y-m-d')
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
            'end_date' => 'required|date' //|after_or_equal:start_date
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
            //  Log::error('Details LOGS', [
            //     'date' => $request->date,
            //     'details' => $details
            // ]);
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

            // Generate PDF
            $filename = "duty_assignments_{$date}.pdf";

            $pdf = PDF::loadView('exports.duty_assignments_pdf', [
                'details' => $details,
                'date' => $date,
                'generatedAt' => now()->format('Y-m-d H:i:s')
            ]);

            // Set paper size to legal for better table layout
            $pdf->setPaper('legal', 'landscape');

            return $pdf->download($filename);
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
    public function dutyDetails(Request $request, int $dutyId)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        try {
            $date = Carbon::parse($request->date)->toDateString();

            // Get the duty with all relationships
            $duty = Duty::with([
                'dutyRanks.rank',
                'dutyRanks.soldier',
                'dutyRanks.soldier.rank',
                'dutyRanks.soldier.company'
            ])->findOrFail($dutyId);

            Log::info('Loading duty details modal', [
                'duty_id' => $dutyId,
                'date' => $date
            ]);

            // Get site settings for session times
            $siteSettings = SiteSetting::first();

            // Helper function to extract time part from datetime string
            $extractTimePart = function ($datetime) {
                if (!$datetime) return null;

                if ($datetime instanceof \Carbon\Carbon) {
                    return $datetime->format('H:i:s');
                }

                if (is_string($datetime)) {
                    // If it's already just a time string (HH:MM:SS)
                    if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $datetime)) {
                        return $datetime;
                    }

                    // If it's a full datetime string, extract time part
                    if (preg_match('/\d{1,2}:\d{2}(:\d{2})?/', $datetime, $matches)) {
                        return $matches[0];
                    }
                }

                return '00:00:00';
            };

            // Helper function to check if duty overlaps with session times
            $checkSessionOverlap = function ($dutyStartTime, $dutyEndTime, $sessionTime, $sessionDurationMinutes = 60) use ($date, $extractTimePart) {
                if (!$sessionTime) return false;

                // Extract time parts
                $sessionTimeString = $extractTimePart($sessionTime);
                $dutyStartTimeString = $extractTimePart($dutyStartTime);
                $dutyEndTimeString = $extractTimePart($dutyEndTime);

                // Parse times with the selected date
                $sessionStart = Carbon::parse($date . ' ' . $sessionTimeString);
                $sessionEnd = $sessionStart->copy()->addMinutes($sessionDurationMinutes);

                $dutyStart = Carbon::parse($date . ' ' . $dutyStartTimeString);
                $dutyEnd = Carbon::parse($date . ' ' . $dutyEndTimeString);

                // Handle overnight duties
                if ($dutyEnd->lt($dutyStart)) {
                    $dutyEnd->addDay();
                }

                // Check if duty overlaps with session
                return $dutyStart->lt($sessionEnd) && $dutyEnd->gt($sessionStart);
            };

            // Determine duty excuses based on session overlaps
            $dutySessionExcuses = [
                'pt' => $siteSettings && $siteSettings->pt_time ?
                    $checkSessionOverlap($duty->start_time, $duty->end_time, $siteSettings->pt_time) : false,
                'games' => $siteSettings && $siteSettings->games_time ?
                    $checkSessionOverlap($duty->start_time, $duty->end_time, $siteSettings->games_time) : false,
                'roll_call' => $siteSettings && $siteSettings->roll_call_time ?
                    $checkSessionOverlap($duty->start_time, $duty->end_time, $siteSettings->roll_call_time) : false,
                'parade' => $siteSettings && $siteSettings->parade_time ?
                    $checkSessionOverlap($duty->start_time, $duty->end_time, $siteSettings->parade_time) : false,
            ];

            // Get roster assignments for this date
            $rosterAssignments = SoldierDuty::with([
                'soldier:id,army_no,full_name,rank_id,company_id',
                'soldier.rank:id,name',
                'soldier.company:id,name',
                'soldier.sickness',
                'soldier.medicalCategory'
            ])
                ->where('duty_id', $dutyId)
                ->where('assigned_date', $date)
                ->get()
                ->map(function ($assignment) {
                    return [
                        'assignment_id' => $assignment->id,
                        'soldier_id' => $assignment->soldier_id,
                        'army_no' => $assignment->soldier->army_no ?? 'N/A',
                        'full_name' => $assignment->soldier->full_name ?? 'N/A',
                        'rank' => $assignment->soldier->rank->name ?? 'N/A',
                        'company' => $assignment->soldier->company->name ?? 'N/A',
                        'start_time' => $assignment->start_time,
                        'end_time' => $assignment->end_time,
                        'status' => $assignment->status,
                        'remarks' => $assignment->remarks,
                        'assignment_type' => 'roster',
                        'is_on_leave' => $assignment->soldier->is_on_leave ?? false,
                        'assignment_source' => 'soldier_duties_table'
                    ];
                });

            // Get fixed assignments for this duty
            $fixedAssignments = $duty->dutyRanks()
                ->where('duty_type', 'fixed')
                ->whereNotNull('soldier_id')
                ->with([
                    'soldier:id,army_no,full_name,rank_id,company_id',
                    'soldier.rank:id,name',
                    'soldier.company:id,name',
                    'soldier.sickness',
                    'soldier.medicalCategory'
                ])
                ->get()
                ->map(function ($dutyRank) {
                    return [
                        'duty_rank_id' => $dutyRank->id,
                        'soldier_id' => $dutyRank->soldier_id,
                        'army_no' => $dutyRank->soldier->army_no ?? 'N/A',
                        'full_name' => $dutyRank->soldier->full_name ?? 'N/A',
                        'rank' => $dutyRank->soldier->rank->name ?? 'N/A',
                        'company' => $dutyRank->soldier->company->name ?? 'N/A',
                        'priority' => $dutyRank->priority,
                        'remarks' => $dutyRank->remarks,
                        'assignment_type' => 'fixed',
                        'is_on_leave' => $dutyRank->soldier->is_on_leave ?? false,
                        'assignment_source' => 'duty_rank_table'
                    ];
                });

            // Get roster requirements
            $rosterRequirements = $duty->dutyRanks()
                ->where('duty_type', 'roster')
                ->with('rank:id,name')
                ->get()
                ->map(function ($dutyRank) {
                    return [
                        'duty_rank_id' => $dutyRank->id,
                        'rank_id' => $dutyRank->rank_id,
                        'rank_name' => $dutyRank->rank->name ?? 'N/A',
                        'manpower' => $dutyRank->manpower,
                        'group_id' => $dutyRank->group_id,
                        'required_soldiers' => $dutyRank->manpower,
                        'assignment_type' => 'roster_requirement'
                    ];
                });

            // Calculate assignment statistics
            $totalAssigned = $rosterAssignments->count() + $fixedAssignments->count();
            $requiredManpower = $duty->manpower ?? 0;
            $fulfillmentRate = $requiredManpower > 0
                ? round(($totalAssigned / $requiredManpower) * 100, 2)
                : 100;

            // Count by assignment type
            $rosterCount = $rosterAssignments->count();
            $fixedCount = $fixedAssignments->count();

            // Calculate total duty hours - Handle time parsing safely
            $totalHours = 0;
            $isOvernight = false;
            try {
                // Extract time parts for duty times
                $dutyStartTimeString = $extractTimePart($duty->start_time);
                $dutyEndTimeString = $extractTimePart($duty->end_time);

                $startTime = Carbon::parse('2000-01-01 ' . $dutyStartTimeString); // Use a base date
                $endTime = Carbon::parse('2000-01-01 ' . $dutyEndTimeString); // Use a base date

                if ($endTime->lt($startTime)) {
                    $endTime->addDay();
                    $isOvernight = true;
                }
                $totalHours = $startTime->diffInHours($endTime, true);
            } catch (\Exception $e) {
                Log::warning('Error calculating duty hours', [
                    'duty_id' => $dutyId,
                    'start_time' => $duty->start_time,
                    'end_time' => $duty->end_time,
                    'error' => $e->getMessage()
                ]);
                $totalHours = 0;
            }

            // Prepare session times for display
            $sessionTimes = null;
            if ($siteSettings) {
                $sessionTimes = [
                    'pt_time' => $extractTimePart($siteSettings->pt_time),
                    'games_time' => $extractTimePart($siteSettings->games_time),
                    'roll_call_time' => $extractTimePart($siteSettings->roll_call_time),
                    'parade_time' => $extractTimePart($siteSettings->parade_time),
                ];
            }

            // Prepare duty excuse information - Combine duty-specific and session-overlap excuses
            $dutyExcuseInfo = [
                'pt' => [
                    'excused' => ($duty->excused_next_session_pt ?? false) || $dutySessionExcuses['pt'],
                    'description' => 'PT (Physical Training) Excused',
                    'reason' => $dutySessionExcuses['pt'] ?
                        'Duty overlaps with PT time' : ($duty->excused_next_session_pt ? 'Duty provides PT excuse' : 'Not excused')
                ],
                'games' => [
                    'excused' => ($duty->excused_next_session_games ?? false) || $dutySessionExcuses['games'],
                    'description' => 'Games Excused',
                    'reason' => $dutySessionExcuses['games'] ?
                        'Duty overlaps with Games time' : ($duty->excused_next_session_games ? 'Duty provides Games excuse' : 'Not excused')
                ],
                'roll_call' => [
                    'excused' => ($duty->excused_next_session_roll_call ?? false) || $dutySessionExcuses['roll_call'],
                    'description' => 'Roll Call Excused',
                    'reason' => $dutySessionExcuses['roll_call'] ?
                        'Duty overlaps with Roll Call time' : ($duty->excused_next_session_roll_call ? 'Duty provides Roll Call excuse' : 'Not excused')
                ],
                'parade' => [
                    'excused' => ($duty->excused_next_session_parade ?? false) || $dutySessionExcuses['parade'],
                    'description' => 'Parade Excused',
                    'reason' => $dutySessionExcuses['parade'] ?
                        'Duty overlaps with Parade time' : ($duty->excused_next_session_parade ? 'Duty provides Parade excuse' : 'Not excused')
                ]
            ];

            // Count how many duties provide excuses
            $totalDutyExcuses = collect($dutyExcuseInfo)->filter(function ($excuse) {
                return $excuse['excused'];
            })->count();

            // Count session-overlap excuses separately
            $sessionOverlapExcuses = collect($dutySessionExcuses)->filter()->count();

            $data = [
                'duty' => [
                    'id' => $duty->id,
                    'duty_name' => $duty->duty_name,
                    'start_time' => $extractTimePart($duty->start_time), // Show only time part
                    'end_time' => $extractTimePart($duty->end_time), // Show only time part
                    'duration_days' => $duty->duration_days ?? 1,
                    'required_manpower' => $requiredManpower,
                    'status' => $duty->status,
                    'remark' => $duty->remark,
                    'total_hours' => round($totalHours, 1),
                    'is_overnight' => $isOvernight,
                    'excuse_info' => $dutyExcuseInfo,
                    'has_any_excuse' => $totalDutyExcuses > 0,
                    'session_overlap_excuses' => $sessionOverlapExcuses,
                    'session_times' => $sessionTimes
                ],
                'date' => $date,
                'statistics' => [
                    'total_assigned' => $totalAssigned,
                    'roster_count' => $rosterCount,
                    'fixed_count' => $fixedCount,
                    'required_manpower' => $requiredManpower,
                    'fulfillment_rate' => $fulfillmentRate,
                    'shortage' => max(0, $requiredManpower - $totalAssigned),
                    'duty_excuses_count' => $totalDutyExcuses,
                    'session_overlap_excuses' => $sessionOverlapExcuses
                ],
                'assignments' => [
                    'roster' => $rosterAssignments,
                    'fixed' => $fixedAssignments,
                ],
                'requirements' => [
                    'roster' => $rosterRequirements,
                ],
                'all_assigned_soldiers' => $rosterAssignments->concat($fixedAssignments),
                'assignment_summary' => [
                    'roster_source' => 'soldier_duties table',
                    'fixed_source' => 'duty_rank table (duty_type = fixed)',
                    'requirements_source' => 'duty_rank table (duty_type = roster)'
                ]
            ];

            // Return JSON for AJAX or view for direct access
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'html' => view('mpm.page.duty-assignments.partials.dutyDetailsModal', $data)->render()
                ]);
            }

            return view('mpm.page.duty-assignments.partials.dutyDetailsModal', $data);
        } catch (\Exception $e) {
            Log::error('Failed to load duty details', [
                'duty_id' => $dutyId,
                'date' => $request->date,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load duty details: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to load duty details: ' . $e->getMessage());
        }
    }
}
