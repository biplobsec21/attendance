<?php

namespace App\Services;

use App\Models\Absent;
use App\Models\Duty;
use App\Models\DutyRank;
use App\Models\LeaveApplication;
use App\Models\Soldier;
use App\Models\SoldierDuty;
use App\Models\SoldierCadre;
use App\Models\SoldierCourse;
use App\Models\SoldierServices;
use App\Models\SoldierExArea;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class DutyAssignmentService
{
    /**
     * Cache for exclusion lists to avoid repeated queries
     */
    protected $exclusionCache = [];

    /**
     * Minimum break between duties in minutes
     */
    protected $minBreakMinutes = 60; // 1 hour minimum break

    /**
     * Maximum duties per soldier per day
     */
    protected $maxDutiesPerDay = 2;

    /**
     * Assign roster duties for a given date with proper transaction handling.
     */
    public function assignDutiesForDate($date)
    {
        return DB::transaction(function () use ($date) {
            $date = Carbon::parse($date)->toDateString();

            Log::info('Starting duty assignment process', ['date' => $date]);

            // Clear any existing assignments for this date to avoid duplicates
            SoldierDuty::where('assigned_date', $date)->delete();

            // 1. Get all active roster duties with manpower requirements
            $duties = Duty::where('status', 'Active')
                ->whereHas('dutyRanks', function ($q) {
                    $q->where('duty_type', 'roster');
                })
                ->with(['dutyRanks' => function ($q) {
                    $q->where('duty_type', 'roster');
                }])
                ->get()
                ->sortBy(function ($duty) {
                    // Sort duties by start time for better assignment order
                    return $this->parseTimeString($duty->start_time);
                });

            Log::info('Active duties retrieved', [
                'date' => $date,
                'total_duties' => $duties->count(),
                'duty_names' => $duties->pluck('duty_name')->toArray()
            ]);

            if ($duties->isEmpty()) {
                Log::warning('No active roster duties found', ['date' => $date]);
                return;
            }

            // 2. Build exclusion list: soldiers on leave, cadre, course, service, fixed duties
            $excludedSoldierIds = $this->getExcludedSoldierIds($date);

            Log::info('Exclusion list generated', [
                'date' => $date,
                'total_excluded' => count($excludedSoldierIds)
            ]);

            // Track assignments made during this process
            $pendingAssignments = [];
            $soldierDutyCount = [];

            // 3. For each duty, handle group and individual rank assignments
            foreach ($duties as $duty) {
                Log::info('Processing duty', [
                    'duty_id' => $duty->id,
                    'duty_name' => $duty->duty_name,
                    'start_time' => $duty->start_time,
                    'end_time' => $duty->end_time,
                    'duration_days' => $duty->duration_days ?? 1,
                    'total_ranks' => $duty->dutyRanks->count()
                ]);

                $this->assignDutyToSoldiers($duty, $date, $excludedSoldierIds, $pendingAssignments, $soldierDutyCount);
            }

            Log::info('Duty assignment process completed successfully', [
                'date' => $date,
                'duties_processed' => $duties->count(),
                'total_assignments' => count($pendingAssignments)
            ]);

            return [
                'success' => true,
                'assignments_count' => count($pendingAssignments),
                'duties_processed' => $duties->count()
            ];
        });
    }

    /**
     * Assign a specific duty to soldiers, handling both group and individual ranks.
     */
    protected function assignDutyToSoldiers(Duty $duty, string $date, array $excludedSoldierIds, array &$pendingAssignments, array &$soldierDutyCount)
    {
        $processedGroups = [];
        $dutyRanks = $duty->dutyRanks->where('duty_type', 'roster');

        Log::info('Starting duty assignment to soldiers', [
            'duty_id' => $duty->id,
            'duty_name' => $duty->duty_name,
            'total_rank_requirements' => $dutyRanks->count()
        ]);

        // First, identify all unique groups
        $groupedRanks = $dutyRanks->groupBy('group_id');

        foreach ($dutyRanks as $dutyRank) {
            // Handle group duties (e.g., 1 Captain OR 1 Colonel)
            if ($dutyRank->group_id && !in_array($dutyRank->group_id, $processedGroups)) {
                Log::info('Processing group duty requirement', [
                    'duty_id' => $duty->id,
                    'group_id' => $dutyRank->group_id,
                    'rank_id' => $dutyRank->rank_id,
                    'manpower' => $dutyRank->manpower,
                    'ranks_in_group' => $groupedRanks->get($dutyRank->group_id)->pluck('rank_id')->toArray()
                ]);

                $assigned = $this->assignGroupDuty($duty, $dutyRank, $date, $excludedSoldierIds, $pendingAssignments, $soldierDutyCount);

                // Mark group as processed immediately after first attempt
                $processedGroups[] = $dutyRank->group_id;

                if ($assigned) {
                    Log::info('Group duty assigned successfully', [
                        'duty_id' => $duty->id,
                        'group_id' => $dutyRank->group_id
                    ]);
                } else {
                    Log::warning('Group duty assignment failed - no suitable soldiers found in any rank', [
                        'duty_id' => $duty->id,
                        'group_id' => $dutyRank->group_id
                    ]);
                }
            }
            // Handle individual rank duties (no group_id)
            elseif (!$dutyRank->group_id) {
                Log::info('Processing individual rank duty requirement', [
                    'duty_id' => $duty->id,
                    'rank_id' => $dutyRank->rank_id,
                    'manpower' => $dutyRank->manpower
                ]);

                $this->assignIndividualRankDuty($duty, $dutyRank, $date, $excludedSoldierIds, $pendingAssignments, $soldierDutyCount);
            }
            // Skip this rank because its group was already processed
            elseif (in_array($dutyRank->group_id, $processedGroups)) {
                Log::debug('Skipping rank - group already processed', [
                    'duty_id' => $duty->id,
                    'group_id' => $dutyRank->group_id,
                    'rank_id' => $dutyRank->rank_id
                ]);
            }
        }

        Log::info('Completed duty assignment to soldiers', [
            'duty_id' => $duty->id,
            'duty_name' => $duty->duty_name,
            'groups_processed' => $processedGroups
        ]);
    }

    /**
     * Assign group duty - select ONE rank from the group that has available soldiers.
     */
    protected function assignGroupDuty(Duty $duty, DutyRank $dutyRank, string $date, array $excludedSoldierIds, array &$pendingAssignments, array &$soldierDutyCount): bool
    {
        $groupRanks = $duty->dutyRanks
            ->where('duty_type', 'roster')
            ->where('group_id', $dutyRank->group_id);

        Log::info('Attempting group duty assignment', [
            'duty_id' => $duty->id,
            'group_id' => $dutyRank->group_id,
            'ranks_in_group' => $groupRanks->pluck('rank_id')->toArray(),
            'required_manpower' => $dutyRank->manpower
        ]);

        // Try each rank in the group until we find enough soldiers
        foreach ($groupRanks as $groupRank) {
            $eligibleSoldiers = $this->getEligibleSoldiersForDuty(
                $groupRank->rank_id,
                $duty,
                $date,
                $excludedSoldierIds,
                $pendingAssignments,
                $soldierDutyCount
            );

            $manpower = $groupRank->manpower ?? 1;

            Log::info('Checking rank availability for group', [
                'duty_id' => $duty->id,
                'group_id' => $groupRank->group_id,
                'rank_id' => $groupRank->rank_id,
                'eligible_soldiers' => $eligibleSoldiers->count(),
                'required_manpower' => $manpower
            ]);

            // If we have enough soldiers for this rank, assign them
            if ($eligibleSoldiers->count() >= $manpower) {
                $this->createSoldierDutyAssignments(
                    $eligibleSoldiers->take($manpower),
                    $duty,
                    $date,
                    $pendingAssignments,
                    $soldierDutyCount
                );

                Log::info('Group duty assigned successfully', [
                    'duty_id' => $duty->id,
                    'duty_name' => $duty->duty_name,
                    'group_id' => $groupRank->group_id,
                    'selected_rank_id' => $groupRank->rank_id,
                    'soldiers_assigned' => $manpower,
                    'soldier_ids' => $eligibleSoldiers->take($manpower)->pluck('id')->toArray()
                ]);

                return true;
            }
        }

        Log::warning('Group duty could not be assigned - insufficient soldiers in all ranks', [
            'duty_id' => $duty->id,
            'duty_name' => $duty->duty_name,
            'group_id' => $dutyRank->group_id,
            'date' => $date,
            'ranks_tried' => $groupRanks->pluck('rank_id')->toArray()
        ]);

        return false;
    }

    /**
     * Assign individual rank duty.
     */
    protected function assignIndividualRankDuty(Duty $duty, DutyRank $dutyRank, string $date, array $excludedSoldierIds, array &$pendingAssignments, array &$soldierDutyCount)
    {
        $eligibleSoldiers = $this->getEligibleSoldiersForDuty(
            $dutyRank->rank_id,
            $duty,
            $date,
            $excludedSoldierIds,
            $pendingAssignments,
            $soldierDutyCount
        );

        $manpower = $dutyRank->manpower ?? 1;

        Log::info('Individual rank duty assignment attempt', [
            'duty_id' => $duty->id,
            'duty_name' => $duty->duty_name,
            'rank_id' => $dutyRank->rank_id,
            'eligible_soldiers' => $eligibleSoldiers->count(),
            'required_manpower' => $manpower,
            'date' => $date
        ]);

        if ($eligibleSoldiers->count() < $manpower) {
            Log::warning('Insufficient soldiers for duty', [
                'duty_id' => $duty->id,
                'duty_name' => $duty->duty_name,
                'rank_id' => $dutyRank->rank_id,
                'required' => $manpower,
                'available' => $eligibleSoldiers->count(),
                'date' => $date,
                'shortage' => $manpower - $eligibleSoldiers->count()
            ]);
        }

        // Assign as many as possible
        $toAssign = $eligibleSoldiers->take($manpower);

        if ($toAssign->isNotEmpty()) {
            $this->createSoldierDutyAssignments($toAssign, $duty, $date, $pendingAssignments, $soldierDutyCount);

            Log::info('Individual rank duty assigned', [
                'duty_id' => $duty->id,
                'duty_name' => $duty->duty_name,
                'rank_id' => $dutyRank->rank_id,
                'soldiers_assigned' => $toAssign->count(),
                'required' => $manpower,
                'soldier_ids' => $toAssign->pluck('id')->toArray(),
                'fulfillment_rate' => round(($toAssign->count() / $manpower) * 100, 2) . '%'
            ]);
        } else {
            Log::error('No soldiers could be assigned for duty', [
                'duty_id' => $duty->id,
                'duty_name' => $duty->duty_name,
                'rank_id' => $dutyRank->rank_id,
                'required' => $manpower,
                'date' => $date
            ]);
        }
    }

    /**
     * Get eligible soldiers for a duty with all validation checks.
     */
    protected function getEligibleSoldiersForDuty(int $rankId, Duty $duty, string $date, array $excludedSoldierIds, array $pendingAssignments = [], array $soldierDutyCount = [])
    {
        Log::info('Finding eligible soldiers', [
            'rank_id' => $rankId,
            'duty_id' => $duty->id,
            'date' => $date,
            'is_24_hour_duty' => $this->is24HourDuty($duty)
        ]);

        // Get all soldiers of the required rank
        $soldiers = Soldier::where('rank_id', $rankId)
            ->where('status', true)
            ->whereNotIn('id', $excludedSoldierIds)
            ->whereDoesntHave('currentLeaveApplications')
            ->get();

        $initialCount = $soldiers->count();
        Log::info('Initial soldier pool retrieved', [
            'rank_id' => $rankId,
            'total_soldiers' => $initialCount
        ]);

        if ($soldiers->isEmpty()) {
            Log::warning('No soldiers found for rank', ['rank_id' => $rankId]);
            return collect([]);
        }

        // Preload last assignments for fair rotation check (avoid N+1)
        $soldierIds = $soldiers->pluck('id')->toArray();
        $lastAssignments = SoldierDuty::whereIn('soldier_id', $soldierIds)
            ->where('duty_id', $duty->id)
            ->where('assigned_date', '<', $date)
            ->select('soldier_id', 'assigned_date', 'duration_days')
            ->orderByDesc('assigned_date')
            ->get()
            ->groupBy('soldier_id')
            ->map(function ($assignments) {
                return $assignments->first(); // Get the most recent
            });

        Log::info('Last assignments retrieved', [
            'soldiers_with_history' => $lastAssignments->count()
        ]);

        // Preload existing assignments in the date range for time conflict check
        $durationDays = $duty->duration_days ?? 1;
        $endDate = Carbon::parse($date)->addDays($durationDays - 1)->toDateString();

        $existingAssignments = SoldierDuty::with('duty')
            ->whereIn('soldier_id', $soldierIds)
            ->where(function ($q) use ($date, $endDate) {
                $q->whereBetween('assigned_date', [$date, $endDate]);
            })
            ->get()
            ->groupBy('soldier_id');

        Log::info('Existing assignments retrieved for conflict check', [
            'soldiers_with_assignments' => $existingAssignments->count()
        ]);

        $fairRotationFiltered = 0;
        $timeConflictFiltered = 0;
        $maxDutiesFiltered = 0;
        $multiDayConflictFiltered = 0;
        $twentyFourHourFiltered = 0;

        // NEW FILTER: 24-hour duty special handling
        $is24HourDuty = $this->is24HourDuty($duty);

        if ($is24HourDuty) {
            $soldiers = $soldiers->filter(function ($soldier) use ($existingAssignments, $duty, $date, &$twentyFourHourFiltered) {
                // For 24-hour duties, check if soldier already has ANY duty that day
                $hasAnyDuty = $existingAssignments->get($soldier->id, collect())->isNotEmpty();

                if ($hasAnyDuty) {
                    $twentyFourHourFiltered++;
                    Log::debug('SOLDIER FILTERED - 24-hour duty conflict', [
                        'soldier_id' => $soldier->id,
                        'duty_id' => $duty->id,
                        'duty_name' => $duty->duty_name,
                        'reason' => 'Cannot assign 24-hour duty to soldier with existing duties'
                    ]);
                    return false;
                }
                return true;
            });
        } else {
            // For non-24-hour duties, check if soldier has a 24-hour duty that day
            $soldiers = $soldiers->filter(function ($soldier) use ($existingAssignments, &$twentyFourHourFiltered) {
                $soldierAssignments = $existingAssignments->get($soldier->id, collect());

                foreach ($soldierAssignments as $assignment) {
                    if ($this->is24HourDuty($assignment->duty)) {
                        $twentyFourHourFiltered++;
                        Log::debug('SOLDIER FILTERED - Has 24-hour duty', [
                            'soldier_id' => $soldier->id,
                            'existing_24h_duty' => $assignment->duty->duty_name,
                            'reason' => 'Soldier already has 24-hour duty on this date'
                        ]);
                        return false;
                    }
                }
                return true;
            });
        }

        // Filter: Multi-day conflict check
        $soldiers = $soldiers->filter(function ($soldier) use ($existingAssignments, $duty, $date, &$multiDayConflictFiltered) {
            $hasMultiDayConflict = $this->hasMultiDayTimeConflict(
                $soldier->id,
                $duty,
                $date,
                $existingAssignments
            );
            if ($hasMultiDayConflict) {
                $multiDayConflictFiltered++;
                Log::debug('SOLDIER FILTERED - Multi-day duty conflict', [
                    'soldier_id' => $soldier->id,
                    'duty_id' => $duty->id,
                    'duty_name' => $duty->duty_name,
                    'date' => $date,
                    'reason' => 'Conflict with overnight/multi-day duty'
                ]);
            }
            return !$hasMultiDayConflict;
        });

        // Filter: Fair rotation check (no back-to-back same duty)
        $soldiers = $soldiers->filter(function ($soldier) use ($lastAssignments, $date, &$fairRotationFiltered) {
            $lastAssignment = $lastAssignments->get($soldier->id);

            if ($lastAssignment) {
                $lastDate = Carbon::parse($lastAssignment->assigned_date);
                $durationDays = $lastAssignment->duration_days ?? 1;

                // Calculate when the last duty actually ended
                $lastDutyEndDate = $lastDate->copy()->addDays($durationDays - 1);

                // Check if current date is immediately after the last duty ended
                $currentDate = Carbon::parse($date);
                if ($lastDutyEndDate->copy()->addDay()->isSameDay($currentDate)) {
                    $fairRotationFiltered++;
                    Log::debug('Soldier filtered by fair rotation', [
                        'soldier_id' => $soldier->id,
                        'last_duty_date' => $lastDate->toDateString(),
                        'last_duty_duration' => $durationDays,
                        'last_duty_end' => $lastDutyEndDate->toDateString(),
                        'current_date' => $currentDate->toDateString()
                    ]);
                    return false; // Skip - back-to-back same duty
                }
            }

            return true;
        });

        // Filter: Time conflict check with minimum break enforcement
        $soldiers = $soldiers->filter(function ($soldier) use ($existingAssignments, $duty, $date, $pendingAssignments, &$timeConflictFiltered) {
            $hasConflict = $this->hasTimeConflictWithPendingAssignments($soldier->id, $duty, $date, $existingAssignments, $pendingAssignments);
            if ($hasConflict) {
                $timeConflictFiltered++;
                Log::debug('SOLDIER FILTERED - Time conflict with existing duty', [
                    'soldier_id' => $soldier->id,
                    'duty_id' => $duty->id,
                    'duty_name' => $duty->duty_name,
                    'date' => $date,
                    'reason' => 'Cannot be assigned to two duties at same time'
                ]);
            }
            return !$hasConflict;
        });

        // Filter: Maximum duties per day check
        $soldiers = $soldiers->filter(function ($soldier) use ($soldierDutyCount, &$maxDutiesFiltered) {
            $currentDuties = $soldierDutyCount[$soldier->id] ?? 0;
            if ($currentDuties >= $this->maxDutiesPerDay) {
                $maxDutiesFiltered++;
                Log::debug('Soldier filtered by maximum duties limit', [
                    'soldier_id' => $soldier->id,
                    'current_duties' => $currentDuties,
                    'max_duties' => $this->maxDutiesPerDay
                ]);
                return false;
            }
            return true;
        });

        // Sort by: 1. Fewest current duties, 2. Last duty date (fair rotation)
        $finalSoldiers = $soldiers->sortBy(function ($soldier) use ($lastAssignments, $soldierDutyCount) {
            $currentDuties = $soldierDutyCount[$soldier->id] ?? 0;
            $lastAssignment = $lastAssignments->get($soldier->id);
            $lastDutyDate = $lastAssignment ? $lastAssignment->assigned_date : '1900-01-01';

            // Primary sort by current duty count (load balancing)
            // Secondary sort by last duty date (fair rotation)
            return [$currentDuties, $lastDutyDate];
        })->values();

        Log::info('Eligible soldiers filtering completed', [
            'rank_id' => $rankId,
            'duty_id' => $duty->id,
            'is_24_hour_duty' => $is24HourDuty,
            'initial_count' => $initialCount,
            'after_24_hour_filter' => $initialCount - $twentyFourHourFiltered,
            'after_multi_day_conflict' => $initialCount - $twentyFourHourFiltered - $multiDayConflictFiltered,
            'after_fair_rotation' => $initialCount - $twentyFourHourFiltered - $multiDayConflictFiltered - $fairRotationFiltered,
            'after_time_conflict' => $initialCount - $twentyFourHourFiltered - $multiDayConflictFiltered - $fairRotationFiltered - $timeConflictFiltered,
            'after_max_duties' => $initialCount - $twentyFourHourFiltered - $multiDayConflictFiltered - $fairRotationFiltered - $timeConflictFiltered - $maxDutiesFiltered,
            'final_eligible' => $finalSoldiers->count(),
            'filtered_by_24_hour' => $twentyFourHourFiltered,
            'filtered_by_multi_day_conflict' => $multiDayConflictFiltered,
            'filtered_by_fair_rotation' => $fairRotationFiltered,
            'filtered_by_time_conflict' => $timeConflictFiltered,
            'filtered_by_max_duties' => $maxDutiesFiltered
        ]);

        return $finalSoldiers;
    }

    /**
     * Check for time conflicts with pending assignments (IMPROVED VERSION)
     */
    protected function hasTimeConflictWithPendingAssignments(int $soldierId, Duty $duty, string $startDate, Collection $assignmentsByDate, array $pendingAssignments): bool
    {
        // First check for conflicts with existing assignments
        if ($this->hasTimeConflictCached($soldierId, $duty, $startDate, $assignmentsByDate)) {
            return true;
        }

        // Then check for conflicts with pending assignments
        $durationDays = $duty->duration_days ?? 1;

        // Check each day of the duty duration
        for ($day = 0; $day < $durationDays; $day++) {
            $checkDate = Carbon::parse($startDate)->addDays($day)->toDateString();

            // Check pending assignments for this soldier on this date
            foreach ($pendingAssignments as $pendingAssignment) {
                if ($pendingAssignment['soldier_id'] == $soldierId && $pendingAssignment['assigned_date'] == $checkDate) {

                    // Skip if it's the same duty (allow multiple days of same duty)
                    if ($pendingAssignment['duty_id'] === $duty->id) {
                        continue;
                    }

                    // Create temporary duty object for pending assignment
                    $pendingDuty = new Duty([
                        'start_time' => $pendingAssignment['start_time'],
                        'end_time' => $pendingAssignment['end_time'],
                        'duration_days' => $pendingAssignment['duration_days'] ?? 1
                    ]);

                    if ($this->hasTimeOverlapOrInsufficientBreak($duty, (object) $pendingAssignment)) {
                        Log::warning('TIME CONFLICT WITH PENDING ASSIGNMENT - Soldier cannot have two duties at same time', [
                            'soldier_id' => $soldierId,
                            'date' => $checkDate,
                            'new_duty' => $duty->duty_name . ' (' . $duty->start_time . ' - ' . $duty->end_time . ')',
                            'pending_duty' => 'Duty ID: ' . $pendingAssignment['duty_id'] . ' (' . $pendingAssignment['start_time'] . ' - ' . $pendingAssignment['end_time'] . ')',
                            'overlap_period' => 'Same time period assignment'
                        ]);
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check for time conflicts using cached assignments (IMPROVED VERSION)
     */
    protected function hasTimeConflictCached(int $soldierId, Duty $duty, string $startDate, Collection $assignmentsByDate): bool
    {
        $soldierAssignments = $assignmentsByDate->get($soldierId);

        if (!$soldierAssignments || $soldierAssignments->isEmpty()) {
            return false; // No existing assignments, no conflict
        }

        $durationDays = $duty->duration_days ?? 1;

        // Check each day of the duty duration
        for ($day = 0; $day < $durationDays; $day++) {
            $checkDate = Carbon::parse($startDate)->addDays($day)->toDateString();

            // Get assignments for this specific date
            $assignmentsOnDate = $soldierAssignments->filter(function ($assignment) use ($checkDate) {
                return $assignment->assigned_date === $checkDate;
            });

            if ($assignmentsOnDate->isEmpty()) {
                continue; // No assignments on this date, no conflict
            }

            // Check against all existing assignments on this date
            foreach ($assignmentsOnDate as $existing) {
                // Skip if it's the same duty (allow multiple days of same duty)
                if ($existing->duty_id === $duty->id) {
                    continue;
                }

                if ($this->hasTimeOverlapOrInsufficientBreak($duty, $existing)) {
                    Log::warning('TIME CONFLICT DETECTED - Soldier cannot have two duties at same time', [
                        'soldier_id' => $soldierId,
                        'date' => $checkDate,
                        'new_duty' => $duty->duty_name . ' (' . $duty->start_time . ' - ' . $duty->end_time . ')',
                        'existing_duty' => ($existing->duty->duty_name ?? 'Unknown') . ' (' . $existing->start_time . ' - ' . $existing->end_time . ')',
                        'overlap_period' => 'Same time period assignment'
                    ]);
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check for time overlap or insufficient break between duties (IMPROVED METHOD)
     */
    protected function hasTimeOverlapOrInsufficientBreak($newDuty, $existingDuty): bool
    {
        //  FIXED: Normalize time formats first
        $newStartNormalized = $this->normalizeTimeForComparison($newDuty->start_time);
        $newEndNormalized = $this->normalizeTimeForComparison($newDuty->end_time);
        $existingStartNormalized = $this->normalizeTimeForComparison($existingDuty->start_time);
        $existingEndNormalized = $this->normalizeTimeForComparison($existingDuty->end_time);

        // Handle 24-hour duties - they conflict with everything
        $isNew24Hour = ($newStartNormalized === $newEndNormalized);
        $isExisting24Hour = ($existingStartNormalized === $existingEndNormalized);

        if ($isNew24Hour || $isExisting24Hour) {
            Log::debug('24-hour duty conflict detected', [
                'new_duty' => $newDuty->start_time . ' - ' . $newDuty->end_time . ($isNew24Hour ? ' (24-hour)' : ''),
                'existing_duty' => $existingDuty->start_time . ' - ' . $existingDuty->end_time . ($isExisting24Hour ? ' (24-hour)' : '')
            ]);
            return true;
        }

        // Parse times for regular duties
        $newStart = $this->parseTimeString($newDuty->start_time);
        $newEnd = $this->parseTimeString($newDuty->end_time);
        $existingStart = $this->parseTimeString($existingDuty->start_time);
        $existingEnd = $this->parseTimeString($existingDuty->end_time);

        // Handle overnight duties for regular duties
        if ($newEnd->lt($newStart)) $newEnd->addDay();
        if ($existingEnd->lt($existingStart)) $existingEnd->addDay();

        // Check for direct time overlap
        if ($this->timeRangesOverlap($newStart, $newEnd, $existingStart, $existingEnd)) {
            Log::debug('Direct time overlap detected', [
                'new_duty' => $newDuty->start_time . ' - ' . $newDuty->end_time,
                'existing_duty' => $existingDuty->start_time . ' - ' . $existingDuty->end_time
            ]);
            return true;
        }

        // Check for insufficient break (minimum break between duties)
        $breakAfterExisting = $newStart->diffInMinutes($existingEnd);
        $breakBeforeExisting = $existingStart->diffInMinutes($newEnd);

        // If new duty starts after existing duty ends, check break time
        if ($breakAfterExisting > 0 && $breakAfterExisting < $this->minBreakMinutes) {
            Log::debug('Insufficient break after existing duty', [
                'break_minutes' => $breakAfterExisting,
                'required_break' => $this->minBreakMinutes,
                'new_duty' => $newDuty->start_time . ' - ' . $newDuty->end_time,
                'existing_duty' => $existingDuty->start_time . ' - ' . $existingDuty->end_time
            ]);
            return true;
        }

        // If new duty ends before existing duty starts, check break time
        if ($breakBeforeExisting > 0 && $breakBeforeExisting < $this->minBreakMinutes) {
            Log::debug('Insufficient break before existing duty', [
                'break_minutes' => $breakBeforeExisting,
                'required_break' => $this->minBreakMinutes,
                'new_duty' => $newDuty->start_time . ' - ' . $newDuty->end_time,
                'existing_duty' => $existingDuty->start_time . ' - ' . $existingDuty->end_time
            ]);
            return true;
        }

        return false;
    }

    /**
     * Enhanced conflict detection for multi-day duties (NEW METHOD from Version 2)
     */
    protected function hasMultiDayTimeConflict(int $soldierId, Duty $newDuty, string $startDate, Collection $existingAssignments): bool
    {
        $newDurationDays = $newDuty->duration_days ?? 1;

        // Check each day of the new duty duration
        for ($day = 0; $day < $newDurationDays; $day++) {
            $checkDate = Carbon::parse($startDate)->addDays($day)->toDateString();

            // Get ALL assignments for this soldier that could conflict with the new duty
            $allRelevantAssignments = $this->getAllRelevantAssignments($soldierId, $checkDate, $existingAssignments);

            foreach ($allRelevantAssignments as $existingAssignment) {
                // Skip if it's the same duty
                if ($existingAssignment['duty_id'] === $newDuty->id) {
                    continue;
                }

                // Check for actual time overlap considering multi-day spans
                if ($this->hasMultiDayTimeOverlap($newDuty, $existingAssignment, $startDate)) {
                    Log::warning('MULTI-DAY TIME CONFLICT DETECTED', [
                        'soldier_id' => $soldierId,
                        'new_duty' => $newDuty->duty_name . ' (' . $newDuty->start_time . ' - ' . $newDuty->end_time . ')',
                        'new_start_date' => $startDate,
                        'new_duration_days' => $newDurationDays,
                        'existing_duty' => $existingAssignment['duty_name'] . ' (' . $existingAssignment['start_time'] . ' - ' . $existingAssignment['end_time'] . ')',
                        'existing_date' => $existingAssignment['assigned_date'],
                        'conflict_type' => 'multi_day_overlap'
                    ]);
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get all relevant assignments including multi-day duties that could conflict (NEW METHOD from Version 2)
     */
    protected function getAllRelevantAssignments(int $soldierId, string $date, Collection $existingAssignments): Collection
    {
        $relevantAssignments = collect();

        // Get assignments from the current date
        $currentDateAssignments = $existingAssignments->get($soldierId, collect())
            ->filter(function ($assignment) use ($date) {
                return $assignment->assigned_date === $date;
            })
            ->map(function ($assignment) {
                return [
                    'duty_id' => $assignment->duty_id,
                    'duty_name' => $assignment->duty->duty_name ?? 'Unknown',
                    'start_time' => $assignment->start_time,
                    'end_time' => $assignment->end_time,
                    'duration_days' => $assignment->duration_days ?? 1,
                    'assigned_date' => $assignment->assigned_date
                ];
            });

        $relevantAssignments = $relevantAssignments->concat($currentDateAssignments);

        // Also check assignments from previous day that might span into current day
        $previousDate = Carbon::parse($date)->subDay()->toDateString();
        $previousDayAssignments = $existingAssignments->get($soldierId, collect())
            ->filter(function ($assignment) use ($previousDate) {
                return $assignment->assigned_date === $previousDate;
            })
            ->filter(function ($assignment) {
                // Only include if it's an overnight duty that spans into next day
                $startTime = $this->parseTimeString($assignment->start_time);
                $endTime = $this->parseTimeString($assignment->end_time);
                return $endTime->lt($startTime); // Overnight duty
            })
            ->map(function ($assignment) {
                return [
                    'duty_id' => $assignment->duty_id,
                    'duty_name' => $assignment->duty->duty_name ?? 'Unknown',
                    'start_time' => $assignment->start_time,
                    'end_time' => $assignment->end_time,
                    'duration_days' => $assignment->duration_days ?? 1,
                    'assigned_date' => $assignment->assigned_date,
                    'is_overnight' => true
                ];
            });

        return $relevantAssignments->concat($previousDayAssignments);
    }

    /**
     * Check for time overlap considering multi-day duties (NEW METHOD from Version 2)
     */
    protected function hasMultiDayTimeOverlap($newDuty, $existingAssignment, string $newStartDate): bool
    {
        //  Normalize time formats first
        $newStartNormalized = $this->normalizeTimeForComparison($newDuty->start_time);
        $newEndNormalized = $this->normalizeTimeForComparison($newDuty->end_time);
        $existingStartNormalized = $this->normalizeTimeForComparison($existingAssignment['start_time']);
        $existingEndNormalized = $this->normalizeTimeForComparison($existingAssignment['end_time']);

        // Handle 24-hour duties - they conflict with everything
        $isNew24Hour = ($newStartNormalized === $newEndNormalized);
        $isExisting24Hour = ($existingStartNormalized === $existingEndNormalized);

        if ($isNew24Hour || $isExisting24Hour) {
            Log::debug('Multi-day 24-hour duty conflict detected', [
                'new_duty' => $newDuty->start_time . ' - ' . $newDuty->end_time . ($isNew24Hour ? ' (24-hour)' : ''),
                'existing_duty' => $existingAssignment['start_time'] . ' - ' . $existingAssignment['end_time'] . ($isExisting24Hour ? ' (24-hour)' : '')
            ]);
            return true;
        }

        // Parse times for regular duties
        $newStart = $this->parseTimeString($newDuty->start_time);
        $newEnd = $this->parseTimeString($newDuty->end_time);
        $existingStart = $this->parseTimeString($existingAssignment['start_time']);
        $existingEnd = $this->parseTimeString($existingAssignment['end_time']);

        // Create proper datetime objects considering the actual dates
        $newStartDateTime = Carbon::parse($newStartDate . ' ' . $newStart->format('H:i:s'));
        $newEndDateTime = Carbon::parse($newStartDate . ' ' . $newEnd->format('H:i:s'));
        $existingStartDateTime = Carbon::parse($existingAssignment['assigned_date'] . ' ' . $existingStart->format('H:i:s'));
        $existingEndDateTime = Carbon::parse($existingAssignment['assigned_date'] . ' ' . $existingEnd->format('H:i:s'));

        // Handle overnight duties for existing assignment
        if (($existingAssignment['is_overnight'] ?? false) || $existingEnd->lt($existingStart)) {
            $existingEndDateTime->addDay(); // It ends the next day
        }

        // Handle overnight duties for new duty
        if ($newEnd->lt($newStart)) {
            $newEndDateTime->addDay();
        }

        // Check for overlap
        $hasOverlap = $newStartDateTime->lt($existingEndDateTime) && $existingStartDateTime->lt($newEndDateTime);

        return $hasOverlap;
    }
    /**
     * Check if duty is a 24-hour duty (same start and end time)
     */
    protected function is24HourDuty($duty): bool
    {
        // Normalize time formats for comparison
        $startTime = $this->normalizeTimeForComparison($duty->start_time);
        $endTime = $this->normalizeTimeForComparison($duty->end_time);

        return $startTime === $endTime;
    }

    /**
     * Normalize time format for comparison (remove seconds if present)
     */
    protected function normalizeTimeForComparison(string $time): string
    {
        // If time includes seconds (HH:MM:SS), remove them
        if (strlen($time) > 5 && substr_count($time, ':') === 2) {
            return substr($time, 0, 5); // Keep only HH:MM
        }
        return $time;
    }

    /**
     * Parse time string properly without date (FIXED METHOD)
     */
    protected function parseTimeString(string $timeString): Carbon
    {
        // Handle cases where time might already contain date
        if (str_contains($timeString, ' ')) {
            $parts = explode(' ', $timeString);
            // Take the last part which should be the time
            $timeString = end($parts);
        }

        // Ensure time string has seconds
        if (substr_count($timeString, ':') === 1) {
            $timeString .= ':00';
        }

        // Parse as time only, using today's date as base (will be ignored in comparisons)
        return Carbon::createFromFormat('H:i:s', $timeString);
    }

    /**
     * Check if two time ranges overlap (FIXED VERSION)
     */
    protected function timeRangesOverlap(Carbon $start1, Carbon $end1, Carbon $start2, Carbon $end2): bool
    {
        // Two time ranges overlap if:
        // start1 < end2 AND start2 < end1
        return $start1->lt($end2) && $start2->lt($end1);
    }

    /**
     * Create soldier duty assignments for multiple days if duty spans multiple days.
     */
    protected function createSoldierDutyAssignments(Collection $soldiers, Duty $duty, string $startDate, array &$pendingAssignments, array &$soldierDutyCount)
    {
        $durationDays = $duty->duration_days ?? 1;

        Log::info('Creating soldier duty assignments', [
            'duty_id' => $duty->id,
            'duty_name' => $duty->duty_name,
            'soldiers_count' => $soldiers->count(),
            'start_date' => $startDate,
            'duration_days' => $durationDays,
            'total_records_to_create' => $soldiers->count() * $durationDays
        ]);

        foreach ($soldiers as $soldier) {
            // Create a record for each day of the duty duration
            for ($day = 0; $day < $durationDays; $day++) {
                $assignmentDate = Carbon::parse($startDate)->addDays($day)->toDateString();

                // Add to pending assignments
                $pendingAssignment = [
                    'soldier_id' => $soldier->id,
                    'duty_id' => $duty->id,
                    'assigned_date' => $assignmentDate,
                    'start_time' => $duty->start_time,
                    'end_time' => $duty->end_time,
                    'duration_days' => $durationDays,
                    'status' => 'assigned',
                ];
                $pendingAssignments[] = $pendingAssignment;

                // Update soldier duty count
                if (!isset($soldierDutyCount[$soldier->id])) {
                    $soldierDutyCount[$soldier->id] = 0;
                }
                $soldierDutyCount[$soldier->id]++;

                SoldierDuty::create([
                    'soldier_id'    => $soldier->id,
                    'duty_id'       => $duty->id,
                    'assigned_date' => $assignmentDate,
                    'start_time'    => $duty->start_time,
                    'end_time'      => $duty->end_time,
                    'duration_days' => $durationDays,
                    'status'        => 'assigned',
                ]);

                Log::debug('Soldier duty record created', [
                    'soldier_id' => $soldier->id,
                    'duty_id' => $duty->id,
                    'assigned_date' => $assignmentDate,
                    'day_number' => $day + 1,
                    'total_days' => $durationDays,
                    'soldier_duty_count' => $soldierDutyCount[$soldier->id]
                ]);
            }

            Log::info('Soldier duty assignment completed', [
                'soldier_id' => $soldier->id,
                'soldier_name' => $soldier->full_name ?? 'N/A',
                'duty_id' => $duty->id,
                'duty_name' => $duty->duty_name,
                'start_date' => $startDate,
                'end_date' => Carbon::parse($startDate)->addDays($durationDays - 1)->toDateString(),
                'duration_days' => $durationDays,
                'records_created' => $durationDays,
                'total_duties_today' => $soldierDutyCount[$soldier->id] ?? 0
            ]);
        }
    }

    /**
     * Get all soldier IDs who are unavailable for roster assignment on a given date.
     * Includes: active cadres, courses, services, fixed duty assignments, and leave.
     */
    protected function getExcludedSoldierIds($date)
    {
        $date = Carbon::parse($date);
        $cacheKey = $date->toDateString();

        // Return cached result if available
        if (isset($this->exclusionCache[$cacheKey])) {
            Log::debug('Using cached exclusion list', ['date' => $cacheKey]);
            return $this->exclusionCache[$cacheKey];
        }

        Log::info('Building exclusion list', ['date' => $date->toDateString()]);

        // Soldiers in ACTIVE cadres
        $cadreIds = SoldierCadre::where('status', 'active')
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->pluck('soldier_id')
            ->toArray();

        Log::info('Cadre exclusions', [
            'count' => count($cadreIds),
        ]);

        // Soldiers in ACTIVE courses
        $courseIds = SoldierCourse::where('status', 'active')
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->pluck('soldier_id')
            ->toArray();

        Log::info('Course exclusions', [
            'count' => count($courseIds),
        ]);

        // Soldiers with ACTIVE Ex Areas (using model scope with date filtering)
        $exAreaIds = SoldierExArea::active()
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->pluck('soldier_id')
            ->toArray();

        Log::info('Ex Area exclusions', [
            'count' => count($exAreaIds),
        ]);

        // Soldiers in ACTIVE services
        $serviceIds = SoldierServices::where('status', 'active')
            ->whereDate('appointments_from_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('appointments_to_date')
                    ->orWhereDate('appointments_to_date', '>=', $date);
            })
            ->pluck('soldier_id')
            ->toArray();

        Log::info('Service exclusions', [
            'count' => count($serviceIds),
        ]);

        // Soldiers with fixed duty assignments (CRITICAL)
        $fixedDutyIds = DutyRank::where('duty_type', 'fixed')
            ->whereNotNull('soldier_id')
            ->whereHas('duty', function ($q) {
                $q->where('status', 'Active');
            })
            ->pluck('soldier_id')
            ->toArray();

        Log::info('Fixed duty exclusions', [
            'count' => count($fixedDutyIds),
        ]);

        // Soldiers on LEAVE (approved and active for the specific date)
        $leaveIds = LeaveApplication::approved()
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->pluck('soldier_id')
            ->toArray();

        Log::info('Leave exclusions', [
            'count' => count($leaveIds),
        ]);

        // Soldiers with ACTIVE CMD (Command) - following same pattern
        $cmdIds = Soldier::whereHas('cmds', function ($query) use ($date) {
            $query->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
                ->whereDate('start_date', '<=', $date);
        })->pluck('id')->toArray();

        Log::info('CMD exclusions', [
            'count' => count($cmdIds),
        ]);

        // Soldiers with ACTIVE ATT (Annual Training) - following same pattern
        $attIds = Soldier::whereHas('att', function ($query) use ($date) {
            $query->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
                ->whereDate('start_date', '<=', $date);
        })->pluck('id')->toArray();

        Log::info('ATT exclusions', [
            'count' => count($attIds),
        ]);

        // Soldiers with ACTIVE ERE - following same pattern
        $ereIds = Soldier::whereHas('ere', function ($query) use ($date) {
            $query->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
                ->whereDate('start_date', '<=', $date);
        })->pluck('id')->toArray();

        Log::info('ERE exclusions', [
            'count' => count($ereIds),
        ]);

        // Soldiers with ACTIVE Absent records (using model scopes)
        $absentIds = Absent::approved()
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->pluck('soldier_id')
            ->toArray();

        Log::info('Absent exclusions', [
            'count' => count($absentIds),
        ]);

        // Combine all exclusion lists
        $excluded = array_unique(array_merge(
            $cadreIds,
            $courseIds,
            $serviceIds,
            $fixedDutyIds,
            $leaveIds,
            $exAreaIds,
            $cmdIds,
            $attIds,
            $ereIds,
            $absentIds
        ));

        Log::info('Exclusion list finalized', [
            'date' => $date->toDateString(),
            'total_excluded' => count($excluded),
            'breakdown' => [
                'cadres' => count($cadreIds),
                'courses' => count($courseIds),
                'services' => count($serviceIds),
                'fixed_duties' => count($fixedDutyIds),
                'leave' => count($leaveIds),
                'ex_areas' => count($exAreaIds),
                'cmd' => count($cmdIds),
                'att' => count($attIds),
                'ere' => count($ereIds),
                'absent' => count($absentIds)
            ],
        ]);

        // Cache the result
        $this->exclusionCache[$cacheKey] = $excluded;

        return $excluded;
    }

    /**
     * Get detailed duty assignments for a specific date
     */
    public function getDutyDetailsForDate(string $date): array
    {
        $date = Carbon::parse($date)->toDateString();

        Log::info('Getting duty details for date', ['date' => $date]);

        // Helper function to convert UTC times to local time (UTC+6)
        $convertToLocalTime = function ($datetime) {
            if (!$datetime) return 'N/A';

            if ($datetime instanceof \Carbon\Carbon) {
                return $datetime->copy()->setTimezone('Asia/Dhaka')->format('H:i');
            }

            if (is_string($datetime)) {
                try {
                    $carbon = Carbon::parse($datetime);
                    return $carbon->setTimezone('Asia/Dhaka')->format('H:i');
                } catch (\Exception $e) {
                    return $datetime; // Return original if parsing fails
                }
            }

            return 'N/A';
        };

        // Get all assignments for the date with related data
        $assignments = SoldierDuty::with([
            'soldier:id,army_no,full_name,rank_id,company_id',
            'soldier.rank:id,name',
            'soldier.company:id,name',
            'duty:id,duty_name,start_time,end_time,duration_days,manpower'
        ])
            ->where('assigned_date', $date)
            ->orderBy('duty_id')
            ->orderBy('soldier_id')
            ->get();

        // Group assignments by duty
        $duties = Duty::where('status', 'Active')
            ->whereHas('dutyRanks', function ($q) {
                $q->where('duty_type', 'roster');
            })
            ->with(['dutyRanks' => function ($q) {
                $q->where('duty_type', 'roster')->with('rank');
            }])
            ->get();

        $dutyDetails = [];

        foreach ($duties as $duty) {
            $dutyAssignments = $assignments->where('duty_id', $duty->id);

            $dutyDetails[] = [
                'duty_id' => $duty->id,
                'duty_name' => $duty->duty_name,
                'start_time' => $convertToLocalTime($duty->start_time), // Convert to local time
                'end_time' => $convertToLocalTime($duty->end_time), // Convert to local time
                'duration_days' => $duty->duration_days,
                'required_manpower' => $duty->manpower,
                'assigned_soldiers' => $dutyAssignments->map(function ($assignment) use ($convertToLocalTime) {
                    return [
                        'soldier_id' => $assignment->soldier_id,
                        'army_no' => $assignment->soldier->army_no ?? 'N/A',
                        'full_name' => $assignment->soldier->full_name ?? 'N/A',
                        'rank' => $assignment->soldier->rank->name ?? 'N/A',
                        'company' => $assignment->soldier->company->name ?? 'N/A',
                        'start_time' => $convertToLocalTime($assignment->start_time), // Convert to local time
                        'end_time' => $convertToLocalTime($assignment->end_time), // Convert to local time
                        'remarks' => $assignment->remarks
                    ];
                })->toArray(),
                'rank_requirements' => $duty->dutyRanks->map(function ($dutyRank) {
                    return [
                        'rank_id' => $dutyRank->rank_id,
                        'rank_name' => $dutyRank->rank->name ?? 'N/A',
                        'manpower' => $dutyRank->manpower,
                        'group_id' => $dutyRank->group_id,
                        'assignment_type' => $dutyRank->duty_type
                    ];
                })->toArray(),
                'fulfillment_rate' => $duty->manpower > 0
                    ? round(($dutyAssignments->count() / $duty->manpower) * 100, 2)
                    : 0,
                'assigned_count' => $dutyAssignments->count(),
                'shortage' => max(0, $duty->manpower - $dutyAssignments->count())
            ];
        }

        // Get fixed duties for the date
        $fixedDuties = DutyRank::with([
            'duty:id,duty_name,start_time,end_time',
            'soldier:id,army_no,full_name,rank_id,company_id',
            'soldier.rank:id,name',
            'soldier.company:id,name'
        ])
            ->where('duty_type', 'fixed')
            ->whereHas('duty', function ($q) {
                $q->where('status', 'Active');
            })
            ->get()
            ->map(function ($fixedDuty) use ($convertToLocalTime) {
                return [
                    'duty_id' => $fixedDuty->duty_id,
                    'duty_name' => $fixedDuty->duty->duty_name ?? 'N/A',
                    'soldier_id' => $fixedDuty->soldier_id,
                    'army_no' => $fixedDuty->soldier->army_no ?? 'N/A',
                    'full_name' => $fixedDuty->soldier->full_name ?? 'N/A',
                    'rank' => $fixedDuty->soldier->rank->name ?? 'N/A',
                    'company' => $fixedDuty->soldier->company->name ?? 'N/A',
                    'start_time' => $convertToLocalTime($fixedDuty->duty->start_time), // Convert to local time
                    'end_time' => $convertToLocalTime($fixedDuty->duty->end_time), // Convert to local time
                    'assignment_type' => 'fixed'
                ];
            });

        $statistics = $this->getAssignmentStatistics($date);
        $unfulfilled = $this->getUnfulfilledDuties($date);

        $result = [
            'date' => $date,
            'summary' => [
                'total_duties' => count($dutyDetails),
                'total_assignments' => $statistics['total_assignments'],
                'unique_soldiers' => $statistics['unique_soldiers'],
                'unfulfilled_duties' => count($unfulfilled),
                'average_duties_per_soldier' => $statistics['average_duties_per_soldier']
            ],
            'roster_duties' => $dutyDetails,
            'fixed_duties' => $fixedDuties,
            'unfulfilled_duties' => $unfulfilled,
            'statistics' => $statistics
        ];

        Log::info('Duty details retrieved successfully', [
            'date' => $date,
            'total_roster_duties' => count($dutyDetails),
            'total_fixed_duties' => $fixedDuties->count(),
            'total_assignments' => $statistics['total_assignments']
        ]);

        return $result;
    }

    /**
     * Assign duties for a date range.
     */
    public function assignDutiesForDateRange(string $startDate, string $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        Log::info('Starting duty assignment for date range', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $start->diffInDays($end) + 1
        ]);

        $assignedDates = [];
        $errors = [];

        while ($start->lte($end)) {
            try {
                $result = $this->assignDutiesForDate($start->toDateString());
                $assignedDates[] = [
                    'date' => $start->toDateString(),
                    'assignments' => $result['assignments_count'] ?? 0,
                    'duties_processed' => $result['duties_processed'] ?? 0
                ];

                Log::info('Successfully assigned duties for date', [
                    'date' => $start->toDateString(),
                    'assignments' => $result['assignments_count'] ?? 0
                ]);
            } catch (\Exception $e) {
                $errors[] = [
                    'date' => $start->toDateString(),
                    'error' => $e->getMessage()
                ];

                Log::error('Failed to assign duties for date', [
                    'date' => $start->toDateString(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            $start->addDay();
        }

        Log::info('Duty assignment for date range completed', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'successful_dates' => count($assignedDates),
            'failed_dates' => count($errors),
            'success_rate' => round((count($assignedDates) / (count($assignedDates) + count($errors))) * 100, 2) . '%'
        ]);

        if (!empty($errors)) {
            Log::warning('Some dates had assignment errors', [
                'errors' => $errors
            ]);
        }

        return [
            'assigned_dates' => $assignedDates,
            'errors' => $errors
        ];
    }

    /**
     * Get soldier's roster duty history for fairness analysis.
     */
    public function getSoldierDutyHistory(int $soldierId, int $days = 30)
    {
        $startDate = Carbon::now()->subDays($days)->toDateString();

        $history = SoldierDuty::with('duty')
            ->where('soldier_id', $soldierId)
            ->where('assigned_date', '>=', $startDate)
            ->orderBy('assigned_date', 'desc')
            ->get();

        Log::info('Soldier duty history retrieved', [
            'soldier_id' => $soldierId,
            'days_lookback' => $days,
            'total_duties' => $history->count()
        ]);

        return $history;
    }

    /**
     * Check if a soldier can be assigned to a duty on a specific date.
     */
    public function canAssignSoldierToDuty(int $soldierId, int $dutyId, string $date): array
    {
        $soldier = Soldier::find($soldierId);
        $duty = Duty::find($dutyId);
        $date = Carbon::parse($date)->toDateString();

        $reasons = [];
        $canAssign = true;

        Log::info('Checking soldier eligibility for duty', [
            'soldier_id' => $soldierId,
            'duty_id' => $dutyId,
            'date' => $date
        ]);

        if (!$soldier || !$duty) {
            Log::warning('Soldier or duty not found', [
                'soldier_id' => $soldierId,
                'duty_id' => $dutyId
            ]);
            return ['can_assign' => false, 'reasons' => ['Soldier or duty not found']];
        }

        // Check if soldier is active
        if (!$soldier->status) {
            $canAssign = false;
            $reasons[] = 'Soldier is inactive';
            Log::debug('Soldier failed: inactive', ['soldier_id' => $soldierId]);
        }

        // Check rank match
        $hasRankMatch = $duty->dutyRanks()
            ->where('duty_type', 'roster')
            ->where('rank_id', $soldier->rank_id)
            ->exists();

        if (!$hasRankMatch) {
            $canAssign = false;
            $reasons[] = 'Soldier rank does not match duty requirements';
            Log::debug('Soldier failed: rank mismatch', [
                'soldier_id' => $soldierId,
                'soldier_rank_id' => $soldier->rank_id,
                'duty_id' => $dutyId
            ]);
        }

        // Check exclusions
        $excludedIds = $this->getExcludedSoldierIds($date);
        if (in_array($soldierId, $excludedIds)) {
            $canAssign = false;
            $reasons[] = 'Soldier has conflicting commitment (cadre/course/service/fixed duty)';
            Log::debug('Soldier failed: in exclusion list', ['soldier_id' => $soldierId]);
        }

        // Check leave
        if ($soldier->currentLeaveApplications()->exists()) {
            $canAssign = false;
            $reasons[] = 'Soldier is on leave';
            Log::debug('Soldier failed: on leave', ['soldier_id' => $soldierId]);
        }

        // Check for time conflicts
        $assignmentsByDate = SoldierDuty::with('duty')
            ->where('soldier_id', $soldierId)
            ->where('assigned_date', $date)
            ->get()
            ->groupBy('soldier_id');

        if ($this->hasTimeConflictCached($soldierId, $duty, $date, $assignmentsByDate)) {
            $canAssign = false;
            $reasons[] = 'Soldier has time conflict with existing duty on this date';
            Log::debug('Soldier failed: time conflict', [
                'soldier_id' => $soldierId,
                'date' => $date
            ]);
        }

        // Check fair rotation
        $lastAssignment = SoldierDuty::where('soldier_id', $soldierId)
            ->where('duty_id', $dutyId)
            ->where('assigned_date', '<', $date)
            ->orderByDesc('assigned_date')
            ->first();

        if ($lastAssignment) {
            $lastDate = Carbon::parse($lastAssignment->assigned_date);
            $durationDays = $lastAssignment->duration_days ?? 1;
            $lastDutyEndDate = $lastDate->copy()->addDays($durationDays - 1);
            $currentDate = Carbon::parse($date);

            if ($lastDutyEndDate->copy()->addDay()->isSameDay($currentDate)) {
                $canAssign = false;
                $reasons[] = 'Fair rotation violation: soldier just completed this duty';
                Log::debug('Soldier failed: fair rotation violation', [
                    'soldier_id' => $soldierId,
                    'last_duty_date' => $lastDate->toDateString(),
                    'last_duty_end' => $lastDutyEndDate->toDateString(),
                    'current_date' => $currentDate->toDateString()
                ]);
            }
        }

        // Check maximum duties per day
        $currentDuties = SoldierDuty::where('soldier_id', $soldierId)
            ->where('assigned_date', $date)
            ->count();

        if ($currentDuties >= $this->maxDutiesPerDay) {
            $canAssign = false;
            $reasons[] = "Soldier has reached maximum duties per day ({$this->maxDutiesPerDay})";
            Log::debug('Soldier failed: maximum duties reached', [
                'soldier_id' => $soldierId,
                'current_duties' => $currentDuties,
                'max_duties' => $this->maxDutiesPerDay
            ]);
        }

        Log::info('Soldier eligibility check completed', [
            'soldier_id' => $soldierId,
            'duty_id' => $dutyId,
            'date' => $date,
            'can_assign' => $canAssign,
            'reasons_count' => count($reasons)
        ]);

        return [
            'can_assign' => $canAssign,
            'reasons' => $reasons
        ];
    }

    /**
     * Get assignment statistics for a date.
     */
    public function getAssignmentStatistics(string $date): array
    {
        $date = Carbon::parse($date)->toDateString();

        $totalAssignments = SoldierDuty::where('assigned_date', $date)->count();
        $uniqueSoldiers = SoldierDuty::where('assigned_date', $date)
            ->distinct('soldier_id')
            ->count('soldier_id');
        $uniqueDuties = SoldierDuty::where('assigned_date', $date)
            ->distinct('duty_id')
            ->count('duty_id');

        $assignmentsByDuty = SoldierDuty::with('duty')
            ->where('assigned_date', $date)
            ->get()
            ->groupBy('duty_id')
            ->map(function ($assignments, $dutyId) {
                $duty = $assignments->first()->duty;
                return [
                    'duty_id' => $dutyId,
                    'duty_name' => $duty->duty_name ?? 'Unknown',
                    'soldiers_assigned' => $assignments->count(),
                    'required_manpower' => $duty->manpower ?? 0,
                    'fulfillment_rate' => $duty->manpower
                        ? round(($assignments->count() / $duty->manpower) * 100, 2)
                        : 0
                ];
            })->values();

        // Get soldier duty distribution
        $soldierDutyDistribution = SoldierDuty::where('assigned_date', $date)
            ->selectRaw('soldier_id, COUNT(*) as duty_count')
            ->groupBy('soldier_id')
            ->get()
            ->groupBy('duty_count')
            ->map(function ($group, $dutyCount) {
                return $group->count();
            })->toArray();

        $statistics = [
            'date' => $date,
            'total_assignments' => $totalAssignments,
            'unique_soldiers' => $uniqueSoldiers,
            'unique_duties' => $uniqueDuties,
            'assignments_by_duty' => $assignmentsByDuty,
            'average_duties_per_soldier' => $uniqueSoldiers > 0
                ? round($totalAssignments / $uniqueSoldiers, 2)
                : 0,
            'soldier_duty_distribution' => $soldierDutyDistribution,
            'max_duties_per_soldier' => $soldierDutyDistribution ? max(array_keys($soldierDutyDistribution)) : 0
        ];

        Log::info('Assignment statistics generated', $statistics);

        return $statistics;
    }

    /**
     * Get unfulfilled duties for a date (duties with insufficient soldiers).
     */
    public function getUnfulfilledDuties(string $date): array
    {
        $date = Carbon::parse($date)->toDateString();

        $activeDuties = Duty::where('status', 'Active')
            ->whereHas('dutyRanks', function ($q) {
                $q->where('duty_type', 'roster');
            })
            ->with('dutyRanks')
            ->get();

        $unfulfilled = [];

        foreach ($activeDuties as $duty) {
            $assigned = SoldierDuty::where('duty_id', $duty->id)
                ->where('assigned_date', $date)
                ->count();

            $required = $duty->manpower ?? 0;

            if ($assigned < $required) {
                $unfulfilled[] = [
                    'duty_id' => $duty->id,
                    'duty_name' => $duty->duty_name,
                    'required' => $required,
                    'assigned' => $assigned,
                    'shortage' => $required - $assigned,
                    'fulfillment_rate' => $required > 0
                        ? round(($assigned / $required) * 100, 2)
                        : 0
                ];
            }
        }

        Log::info('Unfulfilled duties retrieved', [
            'date' => $date,
            'total_unfulfilled' => count($unfulfilled)
        ]);

        return $unfulfilled;
    }

    /**
     * Reassign a soldier from one duty to another on a specific date.
     */
    public function reassignSoldier(int $soldierId, int $fromDutyId, int $toDutyId, string $date): array
    {
        return DB::transaction(function () use ($soldierId, $fromDutyId, $toDutyId, $date) {
            $date = Carbon::parse($date)->toDateString();

            Log::info('Starting soldier reassignment', [
                'soldier_id' => $soldierId,
                'from_duty_id' => $fromDutyId,
                'to_duty_id' => $toDutyId,
                'date' => $date
            ]);

            // Verify soldier is actually assigned to the from duty
            $existingAssignment = SoldierDuty::where('soldier_id', $soldierId)
                ->where('duty_id', $fromDutyId)
                ->where('assigned_date', $date)
                ->first();

            if (!$existingAssignment) {
                Log::warning('Reassignment failed: soldier not assigned to original duty', [
                    'soldier_id' => $soldierId,
                    'from_duty_id' => $fromDutyId,
                    'date' => $date
                ]);
                return [
                    'success' => false,
                    'message' => 'Soldier is not assigned to the original duty'
                ];
            }

            // Check if soldier can be assigned to new duty
            $eligibility = $this->canAssignSoldierToDuty($soldierId, $toDutyId, $date);

            if (!$eligibility['can_assign']) {
                Log::warning('Reassignment failed: soldier not eligible', [
                    'soldier_id' => $soldierId,
                    'to_duty_id' => $toDutyId,
                    'reasons' => $eligibility['reasons']
                ]);

                return [
                    'success' => false,
                    'message' => 'Soldier cannot be assigned to new duty',
                    'reasons' => $eligibility['reasons']
                ];
            }

            // Get the from duty to handle duration
            $fromDuty = Duty::find($fromDutyId);
            $toDuty = Duty::find($toDutyId);

            if (!$fromDuty || !$toDuty) {
                return [
                    'success' => false,
                    'message' => 'Duty not found'
                ];
            }

            // Remove from old duty (all days)
            $fromDuration = $fromDuty->duration_days ?? 1;
            for ($day = 0; $day < $fromDuration; $day++) {
                $removeDate = Carbon::parse($date)->addDays($day)->toDateString();
                SoldierDuty::where('soldier_id', $soldierId)
                    ->where('duty_id', $fromDutyId)
                    ->where('assigned_date', $removeDate)
                    ->delete();
            }

            Log::info('Soldier removed from old duty', [
                'soldier_id' => $soldierId,
                'from_duty_id' => $fromDutyId,
                'records_deleted' => $fromDuration
            ]);

            // Assign to new duty (all days)
            $toDuration = $toDuty->duration_days ?? 1;
            for ($day = 0; $day < $toDuration; $day++) {
                $assignDate = Carbon::parse($date)->addDays($day)->toDateString();
                SoldierDuty::create([
                    'soldier_id' => $soldierId,
                    'duty_id' => $toDutyId,
                    'assigned_date' => $assignDate,
                    'start_time' => $toDuty->start_time,
                    'end_time' => $toDuty->end_time,
                    'duration_days' => $toDuration,
                    'status' => 'assigned'
                ]);
            }

            Log::info('Soldier reassignment completed successfully', [
                'soldier_id' => $soldierId,
                'from_duty_id' => $fromDutyId,
                'to_duty_id' => $toDutyId,
                'date' => $date,
                'records_created' => $toDuration
            ]);

            return [
                'success' => true,
                'message' => 'Soldier reassigned successfully'
            ];
        });
    }

    /**
     * Cancel a soldier's duty assignment.
     */
    public function cancelDutyAssignment(int $soldierId, int $dutyId, string $date): bool
    {
        return DB::transaction(function () use ($soldierId, $dutyId, $date) {
            $date = Carbon::parse($date)->toDateString();

            Log::info('Cancelling duty assignment', [
                'soldier_id' => $soldierId,
                'duty_id' => $dutyId,
                'date' => $date
            ]);

            $duty = Duty::find($dutyId);
            if (!$duty) {
                Log::error('Duty not found for cancellation', ['duty_id' => $dutyId]);
                return false;
            }

            $durationDays = $duty->duration_days ?? 1;
            $deletedCount = 0;

            // Delete all records for the duty duration
            for ($day = 0; $day < $durationDays; $day++) {
                $deleteDate = Carbon::parse($date)->addDays($day)->toDateString();
                $deleted = SoldierDuty::where('soldier_id', $soldierId)
                    ->where('duty_id', $dutyId)
                    ->where('assigned_date', $deleteDate)
                    ->delete();
                $deletedCount += $deleted;
            }

            Log::info('Duty assignment cancelled', [
                'soldier_id' => $soldierId,
                'duty_id' => $dutyId,
                'date' => $date,
                'records_deleted' => $deletedCount
            ]);

            return $deletedCount > 0;
        });
    }

    /**
     * Assign a specific soldier to a duty on a specific date with validation.
     */
    public function assignSoldierToDuty(int $soldierId, int $dutyId, string $date): array
    {
        return DB::transaction(function () use ($soldierId, $dutyId, $date) {
            $date = Carbon::parse($date)->toDateString();

            Log::info('Manual soldier duty assignment attempt', [
                'soldier_id' => $soldierId,
                'duty_id' => $dutyId,
                'date' => $date
            ]);

            // 1. Validate soldier and duty exist
            $soldier = Soldier::find($soldierId);
            $duty = Duty::find($dutyId);

            if (!$soldier) {
                Log::warning('Soldier not found for assignment', ['soldier_id' => $soldierId]);
                return [
                    'success' => false,
                    'message' => 'Soldier not found'
                ];
            }

            if (!$duty) {
                Log::warning('Duty not found for assignment', ['duty_id' => $dutyId]);
                return [
                    'success' => false,
                    'message' => 'Duty not found'
                ];
            }

            // 2. Check if duty is active and roster type
            $isRosterDuty = $duty->dutyRanks()
                ->where('duty_type', 'roster')
                ->exists();

            if (!$isRosterDuty) {
                Log::warning('Duty is not a roster duty', [
                    'duty_id' => $dutyId,
                    'duty_name' => $duty->duty_name
                ]);
                return [
                    'success' => false,
                    'message' => 'Cannot assign to non-roster duty'
                ];
            }

            // 3. Check rank compatibility
            $rankCompatible = $duty->dutyRanks()
                ->where('duty_type', 'roster')
                ->where('rank_id', $soldier->rank_id)
                ->exists();

            if (!$rankCompatible) {
                Log::warning('Soldier rank does not match duty requirements', [
                    'soldier_id' => $soldierId,
                    'soldier_rank_id' => $soldier->rank_id,
                    'duty_id' => $dutyId
                ]);
                return [
                    'success' => false,
                    'message' => 'Soldier rank does not match duty requirements'
                ];
            }

            // 4. Check eligibility using existing method
            $eligibility = $this->canAssignSoldierToDuty($soldierId, $dutyId, $date);

            if (!$eligibility['can_assign']) {
                Log::warning('Soldier not eligible for duty assignment', [
                    'soldier_id' => $soldierId,
                    'duty_id' => $dutyId,
                    'date' => $date,
                    'reasons' => $eligibility['reasons']
                ]);
                return [
                    'success' => false,
                    'message' => 'Soldier is not eligible for this duty',
                    'reasons' => $eligibility['reasons']
                ];
            }

            // 5. Check manpower limits
            $currentAssignments = SoldierDuty::where('duty_id', $dutyId)
                ->where('assigned_date', $date)
                ->count();

            $requiredManpower = $duty->manpower ?? 0;

            if ($requiredManpower > 0 && $currentAssignments >= $requiredManpower) {
                Log::warning('Duty manpower limit reached', [
                    'duty_id' => $dutyId,
                    'current_assignments' => $currentAssignments,
                    'required_manpower' => $requiredManpower
                ]);
                return [
                    'success' => false,
                    'message' => 'Duty manpower limit reached'
                ];
            }

            // 6. Create the assignment
            $durationDays = $duty->duration_days ?? 1;
            $createdCount = 0;

            for ($day = 0; $day < $durationDays; $day++) {
                $assignmentDate = Carbon::parse($date)->addDays($day)->toDateString();

                // Check if assignment already exists
                $existingAssignment = SoldierDuty::where('soldier_id', $soldierId)
                    ->where('duty_id', $dutyId)
                    ->where('assigned_date', $assignmentDate)
                    ->first();

                if (!$existingAssignment) {
                    SoldierDuty::create([
                        'soldier_id' => $soldierId,
                        'duty_id' => $dutyId,
                        'assigned_date' => $assignmentDate,
                        'start_time' => $duty->start_time,
                        'end_time' => $duty->end_time,
                        'duration_days' => $durationDays,
                        'status' => 'assigned',
                        'remarks' => 'manual'
                    ]);
                    $createdCount++;
                }
            }

            Log::info('Manual soldier duty assignment completed', [
                'soldier_id' => $soldierId,
                'duty_id' => $dutyId,
                'date' => $date,
                'duration_days' => $durationDays,
                'records_created' => $createdCount,
                'soldier_name' => $soldier->full_name,
                'duty_name' => $duty->duty_name
            ]);

            return [
                'success' => true,
                'message' => 'Soldier assigned to duty successfully',
                'assignment_details' => [
                    'soldier_id' => $soldierId,
                    'soldier_name' => $soldier->full_name,
                    'duty_id' => $dutyId,
                    'duty_name' => $duty->duty_name,
                    'start_date' => $date,
                    'end_date' => Carbon::parse($date)->addDays($durationDays - 1)->toDateString(),
                    'duration_days' => $durationDays,
                    'records_created' => $createdCount
                ]
            ];
        });
    }

    /**
     * Get available soldiers for a specific duty on a date
     */
    public function getAvailableSoldiersForDuty(int $dutyId, string $date): array
    {
        $duty = Duty::find($dutyId);
        $date = Carbon::parse($date)->toDateString();

        if (!$duty) {
            Log::warning('Duty not found for available soldiers query', ['duty_id' => $dutyId]);
            return [];
        }

        Log::info('Getting available soldiers for duty', [
            'duty_id' => $dutyId,
            'duty_name' => $duty->duty_name,
            'date' => $date
        ]);

        $excludedSoldierIds = $this->getExcludedSoldierIds($date);
        $availableSoldiers = [];
        $processedSoldierIds = [];

        // Get required ranks for this duty
        $requiredRankIds = $duty->dutyRanks()
            ->where('duty_type', 'roster')
            ->pluck('rank_id')
            ->unique()
            ->toArray();

        Log::info('Required ranks for duty', [
            'duty_id' => $dutyId,
            'required_rank_ids' => $requiredRankIds
        ]);

        // Get soldiers matching required ranks
        $soldiers = Soldier::whereIn('rank_id', $requiredRankIds)
            ->where('status', true)
            ->whereNotIn('id', $excludedSoldierIds)
            ->with(['rank', 'company'])
            ->get();

        Log::info('Eligible soldiers pool', [
            'total_soldiers' => $soldiers->count()
        ]);

        foreach ($soldiers as $soldier) {
            if (in_array($soldier->id, $processedSoldierIds)) {
                continue;
            }

            $availabilityCheck = $this->canAssignSoldierToDuty($soldier->id, $dutyId, $date);

            $availableSoldiers[] = [
                'id' => $soldier->id,
                'army_no' => $soldier->army_no,
                'name' => $soldier->full_name,
                'rank' => $soldier->rank->name ?? 'N/A',
                'company' => $soldier->company->name ?? 'N/A',
                'is_available' => $availabilityCheck['can_assign'],
                'availability_reason' => $availabilityCheck['can_assign']
                    ? 'Available'
                    : implode(', ', $availabilityCheck['reasons'])
            ];

            $processedSoldierIds[] = $soldier->id;
        }

        // Sort by availability and then by name
        usort($availableSoldiers, function ($a, $b) {
            if ($a['is_available'] === $b['is_available']) {
                return strcmp($a['name'], $b['name']);
            }
            return $b['is_available'] - $a['is_available'];
        });

        Log::info('Available soldiers list generated', [
            'duty_id' => $dutyId,
            'total_soldiers' => count($availableSoldiers),
            'available_count' => count(array_filter($availableSoldiers, fn($s) => $s['is_available']))
        ]);

        return $availableSoldiers;
    }

    /**
     * Get available duties for a soldier on a specific date
     */
    public function getAvailableDutiesForSoldier(int $soldierId, string $date, ?int $excludeDutyId = null): array
    {
        $soldier = Soldier::find($soldierId);
        $date = Carbon::parse($date)->toDateString();

        if (!$soldier) {
            Log::warning('Soldier not found for available duties query', ['soldier_id' => $soldierId]);
            return [];
        }

        Log::info('Getting available duties for soldier', [
            'soldier_id' => $soldierId,
            'soldier_name' => $soldier->full_name,
            'date' => $date,
            'exclude_duty_id' => $excludeDutyId
        ]);

        // Get all active roster duties
        $duties = Duty::where('status', 'Active')
            ->whereHas('dutyRanks', function ($q) {
                $q->where('duty_type', 'roster');
            })
            ->with(['dutyRanks' => function ($q) {
                $q->where('duty_type', 'roster');
            }])
            ->get();

        $availableDuties = [];

        foreach ($duties as $duty) {
            // Skip excluded duty
            if ($excludeDutyId && $duty->id === $excludeDutyId) {
                continue;
            }

            // Check rank compatibility
            $rankCompatible = $duty->dutyRanks()
                ->where('duty_type', 'roster')
                ->where('rank_id', $soldier->rank_id)
                ->exists();

            if (!$rankCompatible) {
                continue;
            }

            // Check eligibility
            $eligibility = $this->canAssignSoldierToDuty($soldierId, $duty->id, $date);

            // Get current assignments for this duty
            $currentAssignments = SoldierDuty::where('duty_id', $duty->id)
                ->where('assigned_date', $date)
                ->count();

            $requiredManpower = $duty->manpower ?? 0;
            $hasSpace = $requiredManpower === 0 || $currentAssignments < $requiredManpower;

            $availableDuties[] = [
                'id' => $duty->id,
                'name' => $duty->duty_name,
                'start_time' => $duty->start_time,
                'end_time' => $duty->end_time,
                'duration_days' => $duty->duration_days ?? 1,
                'assigned_count' => $currentAssignments,
                'required_manpower' => $requiredManpower,
                'has_space' => $hasSpace,
                'is_available' => $eligibility['can_assign'] && $hasSpace,
                'availability_reason' => $eligibility['can_assign'] ?
                    ($hasSpace ? 'Available' : 'Duty is full') :
                    implode(', ', $eligibility['reasons'])
            ];
        }

        // Sort by availability and then by name
        usort($availableDuties, function ($a, $b) {
            if ($a['is_available'] === $b['is_available']) {
                return strcmp($a['name'], $b['name']);
            }
            return $b['is_available'] - $a['is_available'];
        });

        Log::info('Available duties list generated', [
            'soldier_id' => $soldierId,
            'total_duties' => count($availableDuties),
            'available_count' => count(array_filter($availableDuties, fn($d) => $d['is_available']))
        ]);

        return $availableDuties;
    }

    /**
     * Batch check eligibility for multiple soldiers
     */
    public function checkMultipleSoldiersEligibility(array $soldierIds, int $dutyId, string $date): array
    {
        Log::info('Checking eligibility for multiple soldiers', [
            'duty_id' => $dutyId,
            'date' => $date,
            'soldier_count' => count($soldierIds)
        ]);

        $results = [];

        foreach ($soldierIds as $soldierId) {
            $results[$soldierId] = $this->canAssignSoldierToDuty($soldierId, $dutyId, $date);
        }

        Log::info('Batch eligibility check completed', [
            'duty_id' => $dutyId,
            'date' => $date,
            'total_checked' => count($results),
            'eligible_count' => count(array_filter($results, fn($r) => $r['can_assign']))
        ]);

        return $results;
    }

    /**
     * Clear the exclusion cache (useful when testing or after bulk operations)
     */
    public function clearCache(): void
    {
        $this->exclusionCache = [];
        Log::info('Exclusion cache cleared');
    }

    /**
     * Get soldiers with their duty load for a date range (for fairness analysis)
     */
    public function getSoldierDutyLoad(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        Log::info('Calculating soldier duty load', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days' => $start->diffInDays($end) + 1
        ]);

        $dutyLoad = SoldierDuty::with(['soldier:id,army_no,full_name,rank_id', 'soldier.rank:id,name'])
            ->whereBetween('assigned_date', [$startDate, $endDate])
            ->get()
            ->groupBy('soldier_id')
            ->map(function ($assignments, $soldierId) {
                $soldier = $assignments->first()->soldier;
                return [
                    'soldier_id' => $soldierId,
                    'army_no' => $soldier->army_no ?? 'N/A',
                    'full_name' => $soldier->full_name ?? 'N/A',
                    'rank' => $soldier->rank->name ?? 'N/A',
                    'total_duties' => $assignments->count(),
                    'unique_duties' => $assignments->unique('duty_id')->count(),
                    'assignments' => $assignments->map(function ($a) {
                        return [
                            'duty_id' => $a->duty_id,
                            'assigned_date' => $a->assigned_date,
                            'start_time' => $a->start_time,
                            'end_time' => $a->end_time
                        ];
                    })->toArray()
                ];
            })
            ->sortByDesc('total_duties')
            ->values()
            ->toArray();

        Log::info('Soldier duty load calculated', [
            'total_soldiers' => count($dutyLoad),
            'date_range' => $startDate . ' to ' . $endDate
        ]);

        return $dutyLoad;
    }

    /**
     * Get duty fulfillment report for a date range
     */
    public function getDutyFulfillmentReport(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        Log::info('Generating duty fulfillment report', [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $report = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $dateString = $current->toDateString();
            $statistics = $this->getAssignmentStatistics($dateString);
            $unfulfilled = $this->getUnfulfilledDuties($dateString);

            $report[$dateString] = [
                'total_assignments' => $statistics['total_assignments'],
                'unique_soldiers' => $statistics['unique_soldiers'],
                'unique_duties' => $statistics['unique_duties'],
                'unfulfilled_count' => count($unfulfilled),
                'unfulfilled_duties' => $unfulfilled,
                'average_duties_per_soldier' => $statistics['average_duties_per_soldier']
            ];

            $current->addDay();
        }

        Log::info('Duty fulfillment report generated', [
            'date_range' => $startDate . ' to ' . $endDate,
            'total_days' => count($report)
        ]);

        return $report;
    }

    /**
     * Get conflict analysis for a specific date
     */
    public function getConflictAnalysis(string $date): array
    {
        $date = Carbon::parse($date)->toDateString();

        Log::info('Analyzing conflicts for date', ['date' => $date]);

        $assignments = SoldierDuty::with(['soldier:id,army_no,full_name', 'duty:id,duty_name,start_time,end_time'])
            ->where('assigned_date', $date)
            ->get();

        $conflicts = [];
        $soldiers = $assignments->groupBy('soldier_id');

        foreach ($soldiers as $soldierId => $soldierAssignments) {
            if ($soldierAssignments->count() > 1) {
                $soldier = $soldierAssignments->first()->soldier;

                // Check for time overlaps
                foreach ($soldierAssignments as $i => $assignment1) {
                    foreach ($soldierAssignments->slice($i + 1) as $assignment2) {
                        // Use the fixed time parsing method
                        $start1 = $this->parseTimeString($assignment1->start_time);
                        $end1 = $this->parseTimeString($assignment1->end_time);
                        $start2 = $this->parseTimeString($assignment2->start_time);
                        $end2 = $this->parseTimeString($assignment2->end_time);

                        // Handle overnight duties
                        if ($end1->lt($start1)) $end1->addDay();
                        if ($end2->lt($start2)) $end2->addDay();

                        if ($this->timeRangesOverlap($start1, $end1, $start2, $end2)) {
                            $conflicts[] = [
                                'soldier_id' => $soldierId,
                                'army_no' => $soldier->army_no ?? 'N/A',
                                'full_name' => $soldier->full_name ?? 'N/A',
                                'duty1_id' => $assignment1->duty_id,
                                'duty1_name' => $assignment1->duty->duty_name ?? 'N/A',
                                'duty1_time' => $assignment1->start_time . ' - ' . $assignment1->end_time,
                                'duty2_id' => $assignment2->duty_id,
                                'duty2_name' => $assignment2->duty->duty_name ?? 'N/A',
                                'duty2_time' => $assignment2->start_time . ' - ' . $assignment2->end_time,
                                'conflict_type' => 'time_overlap'
                            ];
                        }
                    }
                }
            }
        }

        Log::info('Conflict analysis completed', [
            'date' => $date,
            'conflicts_found' => count($conflicts)
        ]);

        return [
            'date' => $date,
            'total_conflicts' => count($conflicts),
            'conflicts' => $conflicts
        ];
    }

    /**
     * Validate duty assignments for a date range
     */
    public function validateAssignments(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        Log::info('Validating assignments for date range', [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        $validationResults = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $dateString = $current->toDateString();
            $conflicts = $this->getConflictAnalysis($dateString);
            $unfulfilled = $this->getUnfulfilledDuties($dateString);
            $statistics = $this->getAssignmentStatistics($dateString);

            // Check for soldiers with too many duties
            $overloadedSoldiers = [];
            if (isset($statistics['soldier_duty_distribution'])) {
                foreach ($statistics['soldier_duty_distribution'] as $dutyCount => $soldierCount) {
                    if ($dutyCount > $this->maxDutiesPerDay) {
                        $overloadedSoldiers[] = [
                            'duty_count' => $dutyCount,
                            'soldier_count' => $soldierCount
                        ];
                    }
                }
            }

            $validationResults[$dateString] = [
                'has_conflicts' => $conflicts['total_conflicts'] > 0,
                'conflicts' => $conflicts['conflicts'],
                'has_unfulfilled' => count($unfulfilled) > 0,
                'unfulfilled_duties' => $unfulfilled,
                'has_overloaded' => !empty($overloadedSoldiers),
                'overloaded_soldiers' => $overloadedSoldiers,
                'is_valid' => $conflicts['total_conflicts'] === 0 && count($unfulfilled) === 0 && empty($overloadedSoldiers)
            ];

            $current->addDay();
        }

        $totalInvalid = count(array_filter($validationResults, fn($r) => !$r['is_valid']));

        Log::info('Assignment validation completed', [
            'date_range' => $startDate . ' to ' . $endDate,
            'total_days_checked' => count($validationResults),
            'invalid_days' => $totalInvalid
        ]);

        return [
            'date_range' => ['start' => $startDate, 'end' => $endDate],
            'total_days' => count($validationResults),
            'valid_days' => count($validationResults) - $totalInvalid,
            'invalid_days' => $totalInvalid,
            'validation_details' => $validationResults
        ];
    }

    /**
     * Test method to verify the fixed time conflict logic (from Version 2)
     */
    public function testTimeConflictLogic()
    {
        Log::info('Testing time conflict logic');

        // Test cases that should NOT conflict
        $testCases = [
            [
                'duty1' => (object) ['start_time' => '06:00', 'end_time' => '09:00'],
                'duty2' => (object) ['start_time' => '18:00', 'end_time' => '20:00'],
                'should_conflict' => false,
                'description' => 'Morning + Evening duties'
            ],
            [
                'duty1' => (object) ['start_time' => '09:00', 'end_time' => '12:00'],
                'duty2' => (object) ['start_time' => '22:00', 'end_time' => '00:00'],
                'should_conflict' => false,
                'description' => 'Day + Night duties'
            ],
            [
                'duty1' => (object) ['start_time' => '06:00', 'end_time' => '09:00'],
                'duty2' => (object) ['start_time' => '08:00', 'end_time' => '10:00'],
                'should_conflict' => true,
                'description' => 'Overlapping morning duties'
            ]
        ];

        foreach ($testCases as $testCase) {
            $result = $this->hasActualTimeOverlap($testCase['duty1'], ['start_time' => $testCase['duty2']->start_time, 'end_time' => $testCase['duty2']->end_time]);
            $status = $result === $testCase['should_conflict'] ? 'PASS' : 'FAIL';
            Log::info("Time Conflict Test: {$status}", [
                'description' => $testCase['description'],
                'duty1' => $testCase['duty1']->start_time . ' - ' . $testCase['duty1']->end_time,
                'duty2' => $testCase['duty2']->start_time . ' - ' . $testCase['duty2']->end_time,
                'expected' => $testCase['should_conflict'] ? 'CONFLICT' : 'NO CONFLICT',
                'actual' => $result ? 'CONFLICT' : 'NO CONFLICT',
                'status' => $status
            ]);
        }
    }

    /**
     * Check for ACTUAL time overlap (only when time ranges physically overlap) (from Version 2)
     */
    protected function hasActualTimeOverlap($duty1, $duty2): bool
    {
        $start1 = $this->parseTimeString($duty1->start_time);
        $end1 = $this->parseTimeString($duty1->end_time);
        $start2 = $this->parseTimeString($duty2['start_time']);
        $end2 = $this->parseTimeString($duty2['end_time']);

        // Handle overnight duties
        if ($end1->lt($start1)) $end1->addDay();
        if ($end2->lt($start2)) $end2->addDay();

        // Only conflict if time ranges actually overlap
        // Duty1 starts before Duty2 ends AND Duty2 starts before Duty1 ends
        $hasOverlap = $start1->lt($end2) && $start2->lt($end1);
        return $hasOverlap;
    }
}
