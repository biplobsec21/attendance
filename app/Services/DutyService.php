<?php

namespace App\Services;

use App\Models\Duty;
use App\Models\DutyRank;
use App\Models\Soldier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class DutyService
{
    /**
     * Search and filter duties with optional relationships
     */
    public function searchDuties(
        ?string $search = null,
        ?string $status = null,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        array $with = ['dutyRanks.rank', 'dutyRanks.soldier']
    ): Collection {
        $query = Duty::with($with);

        // Search functionality
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('duty_name', 'like', '%' . $search . '%')
                    ->orWhere('remark', 'like', '%' . $search . '%');
            });
        }

        // Status filter
        if ($status) {
            $query->where('status', $status);
        }

        // Sorting
        if (in_array($sortBy, ['duty_name', 'status', 'created_at', 'manpower'])) {
            $query->orderBy($sortBy, $sortDirection);
        }

        return $query->get();
    }

    /**
     * Create duty with both roster and fixed assignments
     */
    public function createDutyWithAssignments(array $validatedData): Duty
    {
        return DB::transaction(function () use ($validatedData) {
            // Calculate total manpower (roster only - fixed assignments don't count toward total manpower)
            $validatedData['manpower'] = $this->calculateRosterManpower($validatedData);

            // Create duty
            $duty = Duty::create($validatedData);

            // Create duty assignments
            $this->createDutyAssignments($duty, $validatedData);

            Log::info('Duty created successfully', ['duty_id' => $duty->id, 'duty_name' => $duty->duty_name]);

            return $duty;
        });
    }

    /**
     * Update duty with both roster and fixed assignments
     */
    public function updateDutyWithAssignments(Duty $duty, array $validatedData): Duty
    {
        return DB::transaction(function () use ($duty, $validatedData) {
            // Calculate roster manpower only
            $validatedData['manpower'] = $this->calculateRosterManpower($validatedData);
            // dd($validatedData);
            // Update duty
            $duty->update($validatedData);

            // Delete existing duty assignments
            DutyRank::where('duty_id', $duty->id)->delete();

            // Create new duty assignments
            $this->createDutyAssignments($duty, $validatedData);

            Log::info('Duty updated successfully', ['duty_id' => $duty->id, 'duty_name' => $duty->duty_name]);

            return $duty;
        });
    }

    /**
     * Delete duty with its associated assignments
     */
    public function deleteDuty(Duty $duty): bool
    {
        return DB::transaction(function () use ($duty) {
            // Delete associated duty assignments
            DutyRank::where('duty_id', $duty->id)->delete();

            // Delete duty
            $deleted = $duty->delete();

            if ($deleted) {
                Log::info('Duty deleted successfully', ['duty_id' => $duty->id, 'duty_name' => $duty->duty_name]);
            }

            return $deleted;
        });
    }

    /**
     * Calculate roster manpower only (exclude fixed assignments)
     */
    private function calculateRosterManpower(array $data): int
    {
        $totalManpower = 0;

        // Individual ranks (roster)
        foreach ($data['rank_manpower'] ?? [] as $rankData) {
            if (isset($rankData['manpower']) && $rankData['manpower'] > 0) {
                $totalManpower += (int) $rankData['manpower'];
            }
        }

        // Rank groups (roster)
        foreach ($data['rank_groups'] ?? [] as $groupData) {
            if (isset($groupData['manpower']) && $groupData['manpower'] > 0) {
                $totalManpower += (int) $groupData['manpower'];
            }
        }

        return $totalManpower;
    }

    /**
     * Create duty assignments (both roster and fixed)
     */
    private function createDutyAssignments(Duty $duty, array $data): void
    {
        $assignments = [];

        // Process roster assignments (individual ranks)
        $assignments = array_merge($assignments, $this->processRosterAssignments($duty, $data));

        // Process roster assignments (rank groups)
        $assignments = array_merge($assignments, $this->processRankGroupAssignments($duty, $data));

        // Process fixed soldier assignments
        $assignments = array_merge($assignments, $this->processFixedAssignments($duty, $data));

        // Bulk insert for better performance
        if (!empty($assignments)) {
            DutyRank::insert($assignments);
            Log::info('Duty assignments created', [
                'duty_id' => $duty->id,
                'roster_assignments' => count($assignments) - count($this->processFixedAssignments($duty, $data)),
                'fixed_assignments' => count($this->processFixedAssignments($duty, $data))
            ]);
        }
    }

    /**
     * Process roster assignments for individual ranks
     */
    private function processRosterAssignments(Duty $duty, array $data): array
    {
        $assignments = [];
        $rankManpower = $data['rank_manpower'] ?? [];

        foreach ($rankManpower as $rankData) {
            if (empty($rankData['rank_id']) || empty($rankData['manpower']) || $rankData['manpower'] <= 0) {
                continue;
            }

            $assignments[] = [
                'duty_id' => $duty->id,
                'rank_id' => $rankData['rank_id'],
                'soldier_id' => null,
                'assignment_type' => 'roster',
                'duty_type' => 'roster',
                'manpower' => $rankData['manpower'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'duration_days' => $data['duration_days'] ?? 1,
                'group_id' => null,
                'priority' => 1,
                'remarks' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $assignments;
    }

    /**
     * Process roster assignments for rank groups
     */
    private function processRankGroupAssignments(Duty $duty, array $data): array
    {
        $assignments = [];
        $rankGroups = $data['rank_groups'] ?? [];

        foreach ($rankGroups as $groupId => $groupData) {
            $ranks = $groupData['ranks'] ?? [];
            $manpower = $groupData['manpower'] ?? 0;

            // Skip empty groups or invalid manpower
            if (empty($ranks) || $manpower <= 0) {
                continue;
            }

            foreach ($ranks as $rankId) {
                // Handle both array and single value formats
                $rankId = is_array($rankId) ? ($rankId[0] ?? null) : $rankId;

                if ($rankId) {
                    $assignments[] = [
                        'duty_id' => $duty->id,
                        'rank_id' => $rankId,
                        'soldier_id' => null,
                        'assignment_type' => 'roster',
                        'duty_type' => 'roster',
                        'manpower' => $manpower,
                        'start_time' => $data['start_time'],
                        'end_time' => $data['end_time'],
                        'duration_days' => $data['duration_days'] ?? 1,
                        'group_id' => $groupId,
                        'priority' => 1,
                        'remarks' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        return $assignments;
    }

    /**
     * Process fixed soldier assignments
     */
    private function processFixedAssignments(Duty $duty, array $data): array
    {
        $assignments = [];
        $fixedSoldiers = $data['fixed_soldiers'] ?? [];

        foreach ($fixedSoldiers as $soldierData) {
            if (empty($soldierData['soldier_id'])) {
                continue;
            }

            // Verify soldier exists and is available
            $soldier = Soldier::find($soldierData['soldier_id']);
            if (!$soldier || !$this->isSoldierAvailableForDuty($soldier, $duty->id)) {
                Log::warning('Soldier not available for fixed duty assignment', [
                    'soldier_id' => $soldierData['soldier_id'],
                    'duty_id' => $duty->id
                ]);
                continue;
            }

            $assignments[] = [
                'duty_id' => $duty->id,
                'rank_id' => $soldier->rank_id, // Use soldier's actual rank
                'soldier_id' => $soldierData['soldier_id'],
                'assignment_type' => 'fixed',
                'duty_type' => 'fixed',
                'manpower' => 1, // Fixed assignments always count as 1
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'duration_days' => $data['duration_days'] ?? 1,
                'group_id' => null,
                'priority' => $soldierData['priority'] ?? 1,
                'remarks' => $soldierData['remarks'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        return $assignments;
    }

    /**
     * Check if soldier is available for duty assignment
     */
    public function isSoldierAvailableForDuty(Soldier $soldier, ?int $excludeDutyId = null): bool
    {
        // Check if soldier is active
        if (!$soldier->status) {
            return false;
        }

        // Check if soldier is on leave
        if ($soldier->is_on_leave) {
            return false;
        }

        // Check if soldier has conflicting assignments (excluding current duty if updating)
        if ($this->hasConflictingAssignments($soldier, $excludeDutyId)) {
            return false;
        }

        return true;
    }

    /**
     * Check if soldier has conflicting duty assignments
     */
    private function hasConflictingAssignments(Soldier $soldier, ?int $excludeDutyId = null): bool
    {
        $query = DutyRank::where('soldier_id', $soldier->id)
            ->where('assignment_type', 'fixed')
            ->whereHas('duty', function ($q) {
                $q->where('status', 'Active');
            });

        if ($excludeDutyId) {
            $query->where('duty_id', '!=', $excludeDutyId);
        }

        return $query->exists();
    }

    /**
     * Get available soldiers for fixed duty assignment
     */
    public function getAvailableSoldiersForDuty(?string $rankId = null, ?int $excludeDutyId = null): array
    {
        $query = Soldier::with(['rank', 'company', 'currentLeaveApplications'])
            ->where('status', true)
            ->notOnLeave();

        if ($rankId) {
            $query->where('rank_id', $rankId);
        }

        $soldiers = $query->get()
            ->filter(function ($soldier) use ($excludeDutyId) {
                return $this->isSoldierAvailableForDuty($soldier, $excludeDutyId);
            })
            ->map(function ($soldier) {
                return [
                    'id' => $soldier->id,
                    'army_no' => $soldier->army_no,
                    'full_name' => $soldier->full_name,
                    'rank' => $soldier->rank->name,
                    'rank_id' => $soldier->rank_id,
                    'company' => $soldier->company->name ?? 'N/A',
                    'current_assignments' => $soldier->getActiveAssignments(),
                    'is_on_leave' => $soldier->is_on_leave,
                    'current_leave_details' => $soldier->current_leave_details,
                ];
            })
            ->values()
            ->toArray();

        // Log for debugging
        \Log::info('Available soldiers query result:', [
            'total_found' => count($soldiers),
            'rank_filter' => $rankId,
            'exclude_duty' => $excludeDutyId
        ]);

        return $soldiers;
    }

    /**
     * Get soldier's fixed duties
     */
    public function getSoldierFixedDuties(int $soldierId): array
    {
        return DutyRank::with(['duty'])
            ->where('soldier_id', $soldierId)
            ->where('assignment_type', 'fixed')
            ->get()
            ->map(function ($assignment) {
                return [
                    'duty' => $assignment->duty,
                    'assignment' => $assignment,
                    'schedule_description' => $this->getDutyScheduleDescription($assignment->duty),
                ];
            })
            ->toArray();
    }

    /**
     * Get all fixed assignments for a duty
     */
    public function getDutyFixedAssignments(int $dutyId): Collection
    {
        return DutyRank::with(['soldier', 'soldier.rank', 'soldier.company'])
            ->where('duty_id', $dutyId)
            ->where('assignment_type', 'fixed')
            ->get();
    }

    /**
     * Get all roster assignments for a duty
     */
    public function getDutyRosterAssignments(int $dutyId): Collection
    {
        return DutyRank::with(['rank'])
            ->where('duty_id', $dutyId)
            ->where('assignment_type', 'roster')
            ->get();
    }

    /**
     * Calculate duty duration in hours, considering overnight duties
     */
    public function calculateDutyDuration(string $startTime, string $endTime): float
    {
        $start = Carbon::createFromTimeString($startTime);
        $end = Carbon::createFromTimeString($endTime);

        // If end time is earlier than start time, it spans to next day
        if ($end->lt($start)) {
            $end->addDay();
        }

        return $end->diffInHours($start);
    }

    /**
     * Calculate total duty hours including duration days
     */
    public function calculateTotalDutyHours(Duty $duty): float
    {
        $dailyHours = $this->calculateDutyDuration($duty->start_time, $duty->end_time);
        return $dailyHours * ($duty->duration_days ?? 1);
    }

    /**
     * Check if duty is overnight (ends next day)
     */
    public function isOvernightDuty(string $startTime, string $endTime): bool
    {
        $start = Carbon::createFromTimeString($startTime);
        $end = Carbon::createFromTimeString($endTime);

        return $end->lt($start);
    }

    /**
     * Get duty schedule description
     */
    public function getDutyScheduleDescription(Duty $duty): string
    {
        $description = $duty->start_time . ' - ' . $duty->end_time;

        if ($this->isOvernightDuty($duty->start_time, $duty->end_time)) {
            $description .= ' (overnight)';
        }

        if ($duty->duration_days > 1) {
            $description .= ' for ' . $duty->duration_days . ' days';
        }

        return $description;
    }

    /**
     * Get duty time display format
     */
    public function getDutyTimeDisplay(string $startTime, string $endTime, int $durationDays = 1): string
    {
        $isOvernight = $this->isOvernightDuty($startTime, $endTime);
        $duration = $this->calculateDutyDuration($startTime, $endTime);
        $totalHours = $duration * $durationDays;

        $display = $startTime . ' - ' . $endTime;

        if ($isOvernight) {
            $display .= ' (overnight)';
        }

        if ($durationDays > 1) {
            $display .= ' for ' . $durationDays . ' days';
        }

        $display .= ' (' . $totalHours . ' total hours)';

        return $display;
    }

    /**
     * Get duties happening at a specific time (for roster assignment)
     */
    public function getActiveDutiesAtTime(string $time): array
    {
        $timeCarbon = Carbon::createFromTimeString($time);

        return Duty::where('status', 'Active')
            ->get()
            ->filter(function ($duty) use ($timeCarbon) {
                $start = Carbon::createFromTimeString($duty->start_time);
                $end = Carbon::createFromTimeString($duty->end_time);

                // Handle overnight duties
                if ($end->lt($start)) {
                    $end->addDay();
                    $timeCheck = $timeCarbon->copy();
                    if ($timeCheck->lt($start)) {
                        $timeCheck->addDay();
                    }
                    return $timeCheck->between($start, $end);
                }

                return $timeCarbon->between($start, $end);
            })
            ->values()
            ->toArray();
    }

    /**
     * Get soldiers assigned to a duty (both fixed and potential roster soldiers)
     */
    public function getDutySoldiers(int $dutyId): array
    {
        $duty = Duty::with(['dutyRanks.soldier', 'dutyRanks.rank'])->find($dutyId);

        if (!$duty) {
            return [];
        }

        $soldiers = [];

        // Fixed assignments
        $fixedAssignments = $duty->dutyRanks->where('assignment_type', 'fixed');
        foreach ($fixedAssignments as $assignment) {
            if ($assignment->soldier) {
                $soldiers[] = [
                    'soldier' => $assignment->soldier,
                    'assignment_type' => 'fixed',
                    'assignment' => $assignment,
                    'rank' => $assignment->soldier->rank,
                ];
            }
        }

        // Roster assignments (get soldiers by rank)
        $rosterAssignments = $duty->dutyRanks->where('assignment_type', 'roster');
        foreach ($rosterAssignments as $assignment) {
            $rankSoldiers = Soldier::where('rank_id', $assignment->rank_id)
                ->where('status', true)
                ->notOnLeave()
                ->get();

            foreach ($rankSoldiers as $soldier) {
                $soldiers[] = [
                    'soldier' => $soldier,
                    'assignment_type' => 'roster',
                    'assignment' => $assignment,
                    'rank' => $assignment->rank,
                ];
            }
        }

        return $soldiers;
    }

    /**
     * Assign specific soldier to duty (convert roster to fixed)
     */
    public function assignSoldierToDuty(int $dutyId, int $soldierId, array $assignmentData = []): bool
    {
        return DB::transaction(function () use ($dutyId, $soldierId, $assignmentData) {
            $soldier = Soldier::find($soldierId);
            $duty = Duty::find($dutyId);

            if (!$soldier || !$duty) {
                return false;
            }

            // Check if soldier is available
            if (!$this->isSoldierAvailableForDuty($soldier, $dutyId)) {
                return false;
            }

            // Create fixed assignment
            DutyRank::create([
                'duty_id' => $dutyId,
                'rank_id' => $soldier->rank_id,
                'soldier_id' => $soldierId,
                'assignment_type' => 'fixed',
                'duty_type' => 'fixed',
                'manpower' => 1,
                'start_time' => $duty->start_time,
                'end_time' => $duty->end_time,
                'duration_days' => $duty->duration_days,
                'priority' => $assignmentData['priority'] ?? 1,
                'remarks' => $assignmentData['remarks'] ?? null,
            ]);

            Log::info('Soldier assigned to duty', [
                'soldier_id' => $soldierId,
                'duty_id' => $dutyId,
                'assignment_type' => 'fixed'
            ]);

            return true;
        });
    }

    /**
     * Remove soldier from duty assignment
     */
    public function removeSoldierFromDuty(int $dutyId, int $soldierId): bool
    {
        $deleted = DutyRank::where('duty_id', $dutyId)
            ->where('soldier_id', $soldierId)
            ->where('assignment_type', 'fixed')
            ->delete();

        if ($deleted) {
            Log::info('Soldier removed from duty', [
                'soldier_id' => $soldierId,
                'duty_id' => $dutyId
            ]);
        }

        return $deleted > 0;
    }

    /**
     * Get duty statistics
     */
    public function getDutyStatistics(): array
    {
        $totalDuties = Duty::count();
        $activeDuties = Duty::where('status', 'Active')->count();

        $fixedAssignments = DutyRank::where('assignment_type', 'fixed')->count();
        $rosterAssignments = DutyRank::where('assignment_type', 'roster')->count();

        $totalManpower = Duty::sum('manpower');
        $totalFixedSoldiers = DutyRank::where('assignment_type', 'fixed')->count();

        return [
            'total_duties' => $totalDuties,
            'active_duties' => $activeDuties,
            'inactive_duties' => $totalDuties - $activeDuties,
            'fixed_assignments' => $fixedAssignments,
            'roster_assignments' => $rosterAssignments,
            'total_manpower' => $totalManpower,
            'total_fixed_soldiers' => $totalFixedSoldiers,
            'total_assignments' => $fixedAssignments + $rosterAssignments,
        ];
    }

    /**
     * Validate duty time conflicts for a soldier
     */
    public function hasTimeConflictForSoldier(int $soldierId, string $startTime, string $endTime, int $durationDays = 1, ?int $excludeDutyId = null): bool
    {
        $soldierDuties = DutyRank::with(['duty'])
            ->where('soldier_id', $soldierId)
            ->where('assignment_type', 'fixed')
            ->whereHas('duty', function ($q) {
                $q->where('status', 'Active');
            });

        if ($excludeDutyId) {
            $soldierDuties->where('duty_id', '!=', $excludeDutyId);
        }

        $conflictingDuties = $soldierDuties->get()
            ->filter(function ($assignment) use ($startTime, $endTime, $durationDays) {
                // Implement time conflict logic here
                // This is a simplified version - you might want more complex logic
                return $this->doTimeRangesOverlap(
                    $startTime,
                    $endTime,
                    $durationDays,
                    $assignment->duty->start_time,
                    $assignment->duty->end_time,
                    $assignment->duty->duration_days
                );
            });

        return $conflictingDuties->isNotEmpty();
    }

    /**
     * Check if two time ranges overlap
     */
    private function doTimeRangesOverlap(
        string $start1,
        string $end1,
        int $days1,
        string $start2,
        string $end2,
        int $days2
    ): bool {
        // Simplified overlap check - you might want to implement more sophisticated logic
        // This checks if the daily time ranges overlap
        $start1 = Carbon::createFromTimeString($start1);
        $end1 = Carbon::createFromTimeString($end1);
        $start2 = Carbon::createFromTimeString($start2);
        $end2 = Carbon::createFromTimeString($end2);

        // Handle overnight duties
        if ($end1->lt($start1)) $end1->addDay();
        if ($end2->lt($start2)) $end2->addDay();

        return $start1->lt($end2) && $start2->lt($end1);
    }
}
