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
use Illuminate\Support\Facades\Log;

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
    // In DutyController.php, update the store method

    // In DutyController.php, update the store method

    // In DutyController.php, update the store method

    public function store(StoreDutyRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $validated = $request->validated();

                // Log the raw request data for debugging
                Log::info('Raw request data:', $validated);

                // Create the duty
                $duty = Duty::create([
                    'duty_name' => $validated['duty_name'],
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'duration_days' => $validated['duration_days'],
                    'remark' => $validated['remark'] ?? null,
                    'status' => $validated['status'],
                    'manpower' => $validated['manpower'] ?? 0,
                ]);

                // Create a lookup array for individual rank manpower (from rank_manpower array)
                $individualRankManpowerLookup = [];
                if (!empty($validated['rank_manpower']) && is_array($validated['rank_manpower'])) {
                    foreach ($validated['rank_manpower'] as $rankId => $rankData) {
                        // Handle both indexed (rank_id as key) and sequential array formats
                        if (is_array($rankData) && isset($rankData['rank_id'])) {
                            $individualRankManpowerLookup[$rankData['rank_id']] = $rankData['manpower'];
                        } else {
                            // If the key itself is the rank_id
                            $individualRankManpowerLookup[$rankId] = $rankData['manpower'] ?? 0;
                        }
                    }
                }

                Log::info("Individual rank manpower lookup:", $individualRankManpowerLookup);

                // Track which ranks have been assigned to groups
                $groupAssignedRanks = [];

                // Process rank group assignments FIRST
                if (!empty($validated['rank_groups'])) {
                    foreach ($validated['rank_groups'] as $groupKey => $groupData) {
                        Log::info("Processing group {$groupKey}:", $groupData);

                        $groupId = $groupData['id'] ?? $groupKey;
                        $groupManpower = $groupData['manpower'] ?? 1;
                        $ranks = $groupData['ranks'] ?? [];
                        $groupRankManpower = $groupData['rank_manpower'] ?? [];

                        Log::info("Group {$groupId} data:", [
                            'group_manpower' => $groupManpower,
                            'ranks' => $ranks,
                            'rank_manpower' => $groupRankManpower
                        ]);

                        foreach ($ranks as $rankId) {
                            // Convert rankId to string for array key lookup
                            $rankIdStr = (string)$rankId;

                            // Priority: group's rank_manpower > individual rank_manpower > group manpower
                            $manpower = $groupRankManpower[$rankIdStr]
                                ?? $groupRankManpower[$rankId]
                                ?? $individualRankManpowerLookup[$rankIdStr]
                                ?? $individualRankManpowerLookup[$rankId]
                                ?? $groupManpower;

                            Log::info("Creating group rank assignment", [
                                'rank_id' => $rankId,
                                'manpower' => $manpower,
                                'group_id' => $groupId,
                                'source' => isset($groupRankManpower[$rankIdStr]) ? 'group_rank_manpower' : (isset($individualRankManpowerLookup[$rankIdStr]) ? 'individual_rank_manpower' : 'group_manpower')
                            ]);

                            $duty->dutyRanks()->create([
                                'rank_id' => $rankId,
                                'manpower' => $manpower,
                                'assignment_type' => 'roster',
                                'group_id' => $groupId,
                            ]);

                            // Mark this rank as assigned to a group
                            $groupAssignedRanks[] = $rankIdStr;
                        }
                    }
                }

                // Process individual rank assignments (only those NOT in groups)
                if (!empty($validated['rank_manpower'])) {
                    foreach ($validated['rank_manpower'] as $rankId => $rankData) {
                        // Extract rank_id and manpower
                        if (is_array($rankData) && isset($rankData['rank_id'])) {
                            $actualRankId = $rankData['rank_id'];
                            $manpower = $rankData['manpower'] ?? 0;
                        } else {
                            $actualRankId = $rankId;
                            $manpower = $rankData['manpower'] ?? 0;
                        }

                        // Skip if this rank is already assigned to a group
                        if (in_array((string)$actualRankId, $groupAssignedRanks)) {
                            Log::info("Skipping rank {$actualRankId} - already in group");
                            continue;
                        }

                        Log::info("Creating individual rank assignment for rank {$actualRankId} with manpower {$manpower}");

                        $duty->dutyRanks()->create([
                            'rank_id' => $actualRankId,
                            'manpower' => $manpower,
                            'assignment_type' => 'roster',
                            'group_id' => null,
                        ]);
                    }
                }

                // Process fixed soldier assignments
                if (!empty($validated['fixed_soldiers'])) {
                    foreach ($validated['fixed_soldiers'] as $soldierAssignment) {
                        $duty->dutyRanks()->create([
                            'soldier_id' => $soldierAssignment['soldier_id'],
                            'assignment_type' => 'fixed',
                            'priority' => $soldierAssignment['priority'] ?? null,
                            'remarks' => $soldierAssignment['remarks'] ?? null,
                        ]);
                    }
                }
            });

            return redirect()
                ->route('duty.index')
                ->with('success', 'Duty record created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating duty: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
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

            // Prepare data for the form
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
                    $rankGroups[$assignment->group_id]['ranks'][] = [
                        'id' => $assignment->rank_id,
                        'name' => $assignment->rank->name,
                        'manpower' => $assignment->manpower
                    ];
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
        try {
            DB::transaction(function () use ($request, $duty) {
                $validated = $request->validated();

                // Update the duty
                $duty->update([
                    'duty_name' => $validated['duty_name'],
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'duration_days' => $validated['duration_days'],
                    'remark' => $validated['remark'] ?? null,
                    'status' => $validated['status'],
                    'manpower' => $validated['manpower'] ?? 0,
                ]);

                // Remove all existing assignments
                $duty->dutyRanks()->delete();

                // Process individual rank assignments
                if (!empty($validated['rank_manpower'])) {
                    foreach ($validated['rank_manpower'] as $rankAssignment) {
                        $duty->dutyRanks()->create([
                            'rank_id' => $rankAssignment['rank_id'],
                            'manpower' => $rankAssignment['manpower'],
                            'assignment_type' => 'roster',
                            'group_id' => null,
                        ]);
                    }
                }

                // Process rank group assignments
                if (!empty($validated['rank_groups'])) {
                    foreach ($validated['rank_groups'] as $groupAssignment) {
                        // Generate a unique group ID if not provided
                        $groupId = $groupAssignment['id'] ?? 'group_' . uniqid();

                        foreach ($groupAssignment['ranks'] as $rankId) {
                            // Use the individual rank manpower if available, otherwise fall back to group manpower
                            $manpower = $groupAssignment['rank_manpower'][$rankId] ?? $groupAssignment['manpower'];

                            $duty->dutyRanks()->create([
                                'rank_id' => $rankId,
                                'manpower' => $manpower,
                                'assignment_type' => 'roster',
                                'group_id' => $groupId,
                            ]);
                        }
                    }
                }

                // Process fixed soldier assignments
                if (!empty($validated['fixed_soldiers'])) {
                    foreach ($validated['fixed_soldiers'] as $soldierAssignment) {
                        $duty->dutyRanks()->create([
                            'soldier_id' => $soldierAssignment['soldier_id'],
                            'assignment_type' => 'fixed',
                            'priority' => $soldierAssignment['priority'] ?? null,
                            'remarks' => $soldierAssignment['remarks'] ?? null,
                        ]);
                    }
                }
            });

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
