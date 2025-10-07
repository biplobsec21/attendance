<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDutyRequest;
use App\Http\Requests\UpdateDutyRequest;
use App\Models\Duty;
use App\Models\Rank;
use App\Models\Soldier;
use App\Services\DutyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use DB;

class DutyController extends Controller
{
    public function __construct(
        private DutyService $dutyService
    ) {}

    /**
     * Display a listing of the resource with search, sort, and pagination.
     */
    public function index(Request $request): View
    {
        try {
            $duties = $this->dutyService->searchDuties(
                search: $request->search,
                status: $request->status,
                sortBy: $request->get('sort_by', 'created_at'),
                sortDirection: $request->get('sort_direction', 'desc')
            );

            $ranks = Rank::orderBy('name')->get();
            $statistics = $this->dutyService->getDutyStatistics();

            return view('mpm.page.duty.index', compact('duties', 'ranks', 'statistics'));
        } catch (\Exception $e) {
            return view('mpm.page.duty.index')
                ->with('duties', collect())
                ->with('ranks', collect())
                ->with('statistics', [])
                ->with('error', 'Failed to load duties: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $ranks = Rank::orderBy('name')->get();

            // Get all available soldiers without any filters for create view
            $availableSoldiers = $this->dutyService->getAvailableSoldiersForDuty();

            // Debug in controller
            \Log::info('Available soldiers in create:', ['count' => count($availableSoldiers)]);

            return view('mpm.page.duty.create', compact('ranks', 'availableSoldiers'));
        } catch (\Exception $e) {
            \Log::error('Error in duty create:', ['error' => $e->getMessage()]);
            return redirect()->route('duty.index')
                ->with('error', 'Failed to load create form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDutyRequest $request): RedirectResponse
    {
        // dd($request->validated());
        try {
            $this->dutyService->createDutyWithAssignments($request->validated());

            return redirect()
                ->route('duty.index')
                ->with('success', 'Duty record created successfully.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Failed to create duty: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Duty $duty)
    {
        try {
            $duty->load([
                'dutyRanks.rank',
                'dutyRanks.soldier',
                'dutyRanks.soldier.rank',
                'dutyRanks.soldier.company'
            ]);

            $fixedAssignments = $this->dutyService->getDutyFixedAssignments($duty->id);
            $rosterAssignments = $this->dutyService->getDutyRosterAssignments($duty->id);
            $totalHours = $this->dutyService->calculateTotalDutyHours($duty);
            $scheduleDescription = $this->dutyService->getDutyScheduleDescription($duty);

            return view('mpm.page.duty.show', compact(
                'duty',
                'fixedAssignments',
                'rosterAssignments',
                'totalHours',
                'scheduleDescription'
            ));
        } catch (\Exception $e) {
            return redirect()->route('duty.index')
                ->with('error', 'Failed to load duty details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Duty $duty)
    {
        try {
            $duty->load([
                'dutyRanks.rank',
                'dutyRanks.soldier',
                'dutyRanks.soldier.rank',
                'dutyRanks.soldier.company'
            ]);

            $ranks = Rank::orderBy('name')->get();
            $availableSoldiers = $this->dutyService->getAvailableSoldiersForDuty(excludeDutyId: $duty->id);

            // Prepare data for the form - FIXED VERSION
            $individualRanks = [];
            $rankGroups = [];
            $fixedSoldiers = [];

            foreach ($duty->dutyRanks as $assignment) {
                if ($assignment->assignment_type === 'roster' && !$assignment->group_id) {
                    // Individual roster assignment
                    $individualRanks[$assignment->rank_id] = [
                        'id' => $assignment->rank_id,
                        'name' => $assignment->rank->name,
                        'manpower' => $assignment->manpower
                    ];
                } elseif ($assignment->assignment_type === 'roster' && $assignment->group_id) {
                    // Rank group assignment
                    if (!isset($rankGroups[$assignment->group_id])) {
                        $rankGroups[$assignment->group_id] = [
                            'id' => $assignment->group_id,
                            'manpower' => $assignment->manpower,
                            'ranks' => []
                        ];
                    }
                    $rankGroups[$assignment->group_id]['ranks'][] = $assignment->rank_id;
                } elseif ($assignment->assignment_type === 'fixed') {
                    // Fixed soldier assignment
                    $fixedSoldiers[$assignment->soldier_id] = [
                        'id' => $assignment->soldier_id,
                        'soldier' => [
                            'id' => $assignment->soldier->id,
                            'full_name' => $assignment->soldier->full_name,
                            'army_no' => $assignment->soldier->army_no,
                            'rank' => $assignment->soldier->rank->name,
                            'company' => $assignment->soldier->company->name ?? 'N/A'
                        ],
                        'priority' => $assignment->priority,
                        'remarks' => $assignment->remarks
                    ];
                }
            }

            // Convert associative array to indexed array for groups
            $rankGroups = array_values($rankGroups);

            $totalHours = $this->dutyService->calculateTotalDutyHours($duty);
            $scheduleDescription = $this->dutyService->getDutyScheduleDescription($duty);

            return view('mpm.page.duty.edit', compact(
                'duty',
                'ranks',
                'availableSoldiers',
                'individualRanks',
                'rankGroups',
                'fixedSoldiers',
                'totalHours',
                'scheduleDescription'
            ));
        } catch (\Exception $e) {
            return redirect()->route('duty.index')
                ->with('error', 'Failed to load edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDutyRequest $request, Duty $duty): RedirectResponse
    {
        // dd($request->validated());
        try {
            $this->dutyService->updateDutyWithAssignments($duty, $request->validated());

            return redirect()
                ->route('duty.index')
                ->with('success', 'Duty record updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update duty: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Duty $duty): RedirectResponse
    {
        try {
            $this->dutyService->deleteDuty($duty);

            return redirect()
                ->route('duty.index')
                ->with('success', 'Duty record deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete duty: ' . $e->getMessage());
        }
    }

    /**
     * Get available soldiers for fixed duty assignment (AJAX endpoint)
     */
    public function getAvailableSoldiers(Request $request): JsonResponse
    {
        try {
            $rankId = $request->get('rank_id');
            $excludeDutyId = $request->get('exclude_duty_id');

            $soldiers = $this->dutyService->getAvailableSoldiersForDuty($rankId, $excludeDutyId);

            return response()->json([
                'success' => true,
                'soldiers' => $soldiers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load available soldiers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get soldier details for fixed assignment (AJAX endpoint)
     */
    public function getSoldierDetails(Soldier $soldier): JsonResponse
    {
        try {
            $soldier->load(['rank', 'company', 'currentLeaveApplications']);

            $isAvailable = $this->dutyService->isSoldierAvailableForDuty($soldier);
            $activeAssignments = $soldier->getActiveAssignments();
            $fixedDuties = $this->dutyService->getSoldierFixedDuties($soldier->id);

            return response()->json([
                'success' => true,
                'soldier' => [
                    'id' => $soldier->id,
                    'army_no' => $soldier->army_no,
                    'full_name' => $soldier->full_name,
                    'rank' => $soldier->rank->name,
                    'company' => $soldier->company->name ?? 'N/A',
                    'is_available' => $isAvailable,
                    'is_on_leave' => $soldier->is_on_leave,
                    'current_leave_details' => $soldier->current_leave_details,
                    'active_assignments' => $activeAssignments,
                    'fixed_duties' => $fixedDuties,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load soldier details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign specific soldier to duty (AJAX endpoint)
     */
    public function assignSoldier(Request $request, Duty $duty): JsonResponse
    {
        try {
            $request->validate([
                'soldier_id' => 'required|exists:soldiers,id',
                'priority' => 'nullable|integer|min:1|max:10',
                'remarks' => 'nullable|string|max:500'
            ]);

            $success = $this->dutyService->assignSoldierToDuty(
                $duty->id,
                $request->soldier_id,
                [
                    'priority' => $request->priority,
                    'remarks' => $request->remarks
                ]
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Soldier assigned to duty successfully.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to assign soldier to duty. Soldier may not be available.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign soldier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove soldier from duty assignment (AJAX endpoint)
     */
    public function removeSoldier(Request $request, Duty $duty): JsonResponse
    {
        try {
            $request->validate([
                'soldier_id' => 'required|exists:soldiers,id'
            ]);

            $success = $this->dutyService->removeSoldierFromDuty($duty->id, $request->soldier_id);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Soldier removed from duty successfully.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove soldier from duty.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove soldier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get duty statistics (AJAX endpoint)
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->dutyService->getDutyStatistics();

            return response()->json([
                'success' => true,
                'statistics' => $statistics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check soldier availability for duty (AJAX endpoint)
     */
    public function checkSoldierAvailability(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'soldier_id' => 'required|exists:soldiers,id',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
                'duration_days' => 'required|integer|min:1|max:30',
                'exclude_duty_id' => 'nullable|exists:duties,id'
            ]);

            $soldier = Soldier::find($request->soldier_id);

            if (!$soldier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Soldier not found.'
                ], 404);
            }

            $isAvailable = $this->dutyService->isSoldierAvailableForDuty($soldier, $request->exclude_duty_id);

            $hasTimeConflict = false;
            if ($isAvailable) {
                $hasTimeConflict = $this->dutyService->hasTimeConflictForSoldier(
                    $soldier->id,
                    $request->start_time,
                    $request->end_time,
                    $request->duration_days,
                    $request->exclude_duty_id
                );
            }

            $activeAssignments = $soldier->getActiveAssignments();

            return response()->json([
                'success' => true,
                'is_available' => $isAvailable && !$hasTimeConflict,
                'has_time_conflict' => $hasTimeConflict,
                'is_on_leave' => $soldier->is_on_leave,
                'active_assignments' => $activeAssignments,
                'soldier_details' => [
                    'full_name' => $soldier->full_name,
                    'army_no' => $soldier->army_no,
                    'rank' => $soldier->rank->name,
                    'company' => $soldier->company->name ?? 'N/A'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get duty assignments breakdown (AJAX endpoint)
     */
    public function getDutyAssignments(Duty $duty): JsonResponse
    {
        try {
            $fixedAssignments = $this->dutyService->getDutyFixedAssignments($duty->id);
            $rosterAssignments = $this->dutyService->getDutyRosterAssignments($duty->id);
            $allSoldiers = $this->dutyService->getDutySoldiers($duty->id);

            $breakdown = [
                'fixed' => [
                    'count' => $fixedAssignments->count(),
                    'assignments' => $fixedAssignments->map(function ($assignment) {
                        return [
                            'soldier_name' => $assignment->soldier->full_name,
                            'army_no' => $assignment->soldier->army_no,
                            'rank' => $assignment->soldier->rank->name,
                            'priority' => $assignment->priority,
                            'remarks' => $assignment->remarks
                        ];
                    })
                ],
                'roster' => [
                    'count' => $rosterAssignments->count(),
                    'assignments' => $rosterAssignments->groupBy('rank_id')->map(function ($assignments, $rankId) {
                        $first = $assignments->first();
                        return [
                            'rank_name' => $first->rank->name,
                            'manpower' => $first->manpower,
                            'group_id' => $first->group_id,
                            'potential_soldiers' => Soldier::where('rank_id', $rankId)
                                ->where('status', true)
                                ->notOnLeave()
                                ->count()
                        ];
                    })->values()
                ],
                'total_soldiers' => count($allSoldiers)
            ];

            return response()->json([
                'success' => true,
                'breakdown' => $breakdown
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load duty assignments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk update duty status
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'duty_ids' => 'required|array',
                'duty_ids.*' => 'exists:duties,id',
                'status' => 'required|in:Active,Inactive'
            ]);

            $updatedCount = Duty::whereIn('id', $request->duty_ids)
                ->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} duties to {$request->status} status.",
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update duty status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export duties to various formats
     */
    public function export(Request $request)
    {
        try {
            $duties = $this->dutyService->searchDuties(
                search: $request->search,
                status: $request->status,
                sortBy: $request->get('sort_by', 'duty_name'),
                sortDirection: 'asc'
            );

            $format = $request->get('format', 'pdf');

            // You can implement export logic here for PDF, Excel, etc.
            // This is a placeholder for export functionality

            return response()->json([
                'success' => false,
                'message' => 'Export functionality not implemented yet.'
            ], 501);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export duties: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicate an existing duty
     */
    public function duplicate(Duty $duty): RedirectResponse
    {
        try {
            DB::transaction(function () use ($duty) {
                // Create new duty with similar data
                $newDuty = $duty->replicate();
                $newDuty->duty_name = $duty->duty_name . ' (Copy)';
                $newDuty->created_at = now();
                $newDuty->updated_at = now();
                $newDuty->save();

                // Copy assignments (only roster assignments, not fixed soldiers)
                foreach ($duty->dutyRanks as $assignment) {
                    if ($assignment->assignment_type === 'roster') {
                        $newAssignment = $assignment->replicate();
                        $newAssignment->duty_id = $newDuty->id;
                        $newAssignment->created_at = now();
                        $newAssignment->updated_at = now();
                        $newAssignment->save();
                    }
                }
            });

            return redirect()
                ->route('duty.index')
                ->with('success', 'Duty duplicated successfully. Please update fixed soldier assignments.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to duplicate duty: ' . $e->getMessage());
        }
    }
}
