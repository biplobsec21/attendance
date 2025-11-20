<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Carbon\Carbon;

class UpdateDutyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $dutyId = $this->route('duty') ? $this->route('duty')->id : null;

        return [
            // Basic Duty Information
            'duty_name' => 'required|string|max:255|unique:duties,duty_name,' . $dutyId,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'duration_days' => 'required|integer|min:1|max:30',
            'remark' => 'nullable|string|max:1000',
            'status' => 'required|in:Active,Inactive',
            'manpower' => 'sometimes|integer|min:0',

            // Excused Next Day Options
            'excused_next_session_pt' => 'sometimes|boolean',
            'excused_next_session_games' => 'sometimes|boolean',
            'excused_next_session_roll_call' => 'sometimes|boolean',
            'excused_next_session_parade' => 'sometimes|boolean',

            // Roster Assignments - Individual Ranks
            'rank_manpower' => 'sometimes|array',
            'rank_manpower.*.rank_id' => 'required_with:rank_manpower|exists:ranks,id',
            'rank_manpower.*.manpower' => 'required_with:rank_manpower.*.rank_id|integer|min:1|max:100',

            // Roster Assignments - Rank Groups
            'rank_groups' => 'sometimes|array',
            'rank_groups.*.manpower' => 'required_with:rank_groups|integer|min:1|max:100',
            'rank_groups.*.ranks' => 'required_with:rank_groups.*.manpower|array|min:1',
            'rank_groups.*.ranks.*' => 'required|exists:ranks,id',

            // Fixed Soldier Assignments
            'fixed_soldiers' => 'sometimes|array',
            'fixed_soldiers.*.soldier_id' => 'required_with:fixed_soldiers|exists:soldiers,id',
            'fixed_soldiers.*.priority' => 'nullable|integer|min:1|max:10',
            'fixed_soldiers.*.remarks' => 'nullable|string|max:500',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            $data = $this->all();
            $dutyId = $this->route('duty') ? $this->route('duty')->id : null;

            // Validate at least one type of assignment is provided
            $hasRosterAssignments = !empty($data['rank_manpower']) || !empty($data['rank_groups']);
            $hasFixedAssignments = !empty($data['fixed_soldiers']);

            if (!$hasRosterAssignments && !$hasFixedAssignments) {
                $validator->errors()->add(
                    'base',
                    'Please add at least one roster assignment or fixed soldier assignment.'
                );
            }

            // Validate time requirements
            if (!empty($data['start_time']) && !empty($data['end_time'])) {
                $this->validateTimeRequirements($validator, $data);
            }

            // Validate fixed soldiers availability (exclude current duty)
            if (!empty($data['fixed_soldiers'])) {
                $this->validateFixedSoldiers($validator, $data['fixed_soldiers'], $dutyId);
            }

            // Validate no duplicate ranks in individual assignments
            if (!empty($data['rank_manpower'])) {
                $this->validateDuplicateRanks($validator, $data['rank_manpower']);
            }

            // Validate rank groups don't contain duplicates
            if (!empty($data['rank_groups'])) {
                $this->validateRankGroups($validator, $data['rank_groups']);
            }

            // Validate total manpower limit (optional business rule)
            $this->validateTotalManpower($validator, $data);
        });
    }

    /**
     * Normalize time format to HH:MM (remove seconds if present)
     * This ensures consistent comparison between '17:00' and '17:00:00'
     */
    private function normalizeTime(string $time): string
    {
        // If time includes seconds (HH:MM:SS), remove them
        if (strlen($time) > 5 && substr_count($time, ':') === 2) {
            return substr($time, 0, 5); // Keep only HH:MM
        }
        return $time;
    }

    /**
     * Validate time requirements including duration and conflicts
     * UPDATED: Now supports 24-hour duties (same start and end time)
     */
    private function validateTimeRequirements(Validator $validator, array $data): void
    {
        $startTime = $data['start_time'];
        $endTime = $data['end_time'];
        $durationDays = $data['duration_days'] ?? 1;

        // Normalize times for comparison
        $normalizedStart = $this->normalizeTime($startTime);
        $normalizedEnd = $this->normalizeTime($endTime);

        // Calculate daily duration
        $dailyDuration = $this->calculateDailyDuration($startTime, $endTime);

        // ✅ NEW: Allow 24-hour duties (same start and end time)
        $is24HourDuty = ($normalizedStart === $normalizedEnd);

        if ($is24HourDuty) {
            // For 24-hour duties, duration should be 24
            if ($dailyDuration !== 24.0) {
                $validator->errors()->add(
                    'end_time',
                    'Internal error: 24-hour duty duration calculation failed.'
                );
            }
            // Skip other validations for 24-hour duties
            return;
        }

        // Validate minimum daily duration (for non-24-hour duties)
        if ($dailyDuration < 1) {
            $validator->errors()->add(
                'end_time',
                'Daily duty duration must be at least 1 hour. For 24-hour duties, use the same start and end time.'
            );
        }

        // Validate maximum daily duration
        if ($dailyDuration > 24) {
            $validator->errors()->add(
                'end_time',
                'Daily duty duration cannot exceed 24 hours.'
            );
        }

        // Validate total duration doesn't exceed reasonable limit
        $totalHours = $dailyDuration * $durationDays;
        if ($totalHours > 720) { // 30 days * 24 hours
            $validator->errors()->add(
                'duration_days',
                'Total duty duration cannot exceed 720 hours (30 days).'
            );
        }
    }

    /**
     * Validate fixed soldiers availability and conflicts (updated for edit)
     */
    private function validateFixedSoldiers(Validator $validator, array $fixedSoldiers, ?int $dutyId = null): void
    {
        $dutyService = app(\App\Services\DutyService::class);

        $usedSoldierIds = [];

        foreach ($fixedSoldiers as $index => $soldierData) {
            $soldierId = $soldierData['soldier_id'];

            // Check for duplicate soldier assignments within the same request
            if (in_array($soldierId, $usedSoldierIds)) {
                $validator->errors()->add(
                    "fixed_soldiers.{$index}.soldier_id",
                    "This soldier is already assigned to this duty."
                );
                continue;
            }
            $usedSoldierIds[] = $soldierId;

            $soldier = \App\Models\Soldier::with(['rank', 'company'])->find($soldierId);

            if (!$soldier) {
                $validator->errors()->add(
                    "fixed_soldiers.{$index}.soldier_id",
                    "Selected soldier does not exist."
                );
                continue;
            }

            // Check soldier availability (exclude current duty for updates)
            if (!$dutyService->isSoldierAvailableForDuty($soldier, $dutyId)) {
                $validator->errors()->add(
                    "fixed_soldiers.{$index}.soldier_id",
                    "Soldier {$soldier->full_name} ({$soldier->army_no}) is not available for duty assignment."
                );
            }

            // Check for time conflicts if times are provided (exclude current duty)
            if ($this->has('start_time') && $this->has('end_time')) {
                $hasConflict = $dutyService->hasTimeConflictForSoldier(
                    $soldierId,
                    $this->start_time,
                    $this->end_time,
                    $this->duration_days ?? 1,
                    $dutyId // Exclude current duty from conflict check
                );

                if ($hasConflict) {
                    $validator->errors()->add(
                        "fixed_soldiers.{$index}.soldier_id",
                        "Soldier {$soldier->full_name} has a scheduling conflict with this duty time."
                    );
                }
            }

            // Validate priority if provided
            if (isset($soldierData['priority']) && ($soldierData['priority'] < 1 || $soldierData['priority'] > 10)) {
                $validator->errors()->add(
                    "fixed_soldiers.{$index}.priority",
                    "Priority must be between 1 and 10."
                );
            }
        }
    }

    /**
     * Validate no duplicate ranks in individual assignments
     */
    private function validateDuplicateRanks(Validator $validator, array $rankManpower): void
    {
        $usedRankIds = [];

        foreach ($rankManpower as $index => $rankData) {
            if (empty($rankData['rank_id'])) {
                continue;
            }

            $rankId = $rankData['rank_id'];

            if (in_array($rankId, $usedRankIds)) {
                $validator->errors()->add(
                    "rank_manpower.{$index}.rank_id",
                    "This rank is already added to individual assignments. Please update the manpower instead."
                );
            }

            $usedRankIds[] = $rankId;
        }
    }

    /**
     * Validate rank groups for duplicates and conflicts
     */
    private function validateRankGroups(Validator $validator, array $rankGroups): void
    {
        $allRanksInGroups = [];
        $groupIndex = 0;

        foreach ($rankGroups as $groupId => $groupData) {
            if (empty($groupData['ranks'])) {
                $validator->errors()->add(
                    "rank_groups.{$groupId}.ranks",
                    "Rank group must contain at least one rank."
                );
                continue;
            }

            $ranksInThisGroup = [];

            foreach ($groupData['ranks'] as $rankIndex => $rankId) {
                // Check for duplicates within the same group
                if (in_array($rankId, $ranksInThisGroup)) {
                    $validator->errors()->add(
                        "rank_groups.{$groupId}.ranks.{$rankIndex}",
                        "Duplicate rank in the same group is not allowed."
                    );
                }
                $ranksInThisGroup[] = $rankId;

                // Check if rank appears in multiple groups
                if (in_array($rankId, $allRanksInGroups)) {
                    $validator->errors()->add(
                        "rank_groups.{$groupId}.ranks.{$rankIndex}",
                        "This rank is already used in another rank group. Each rank can only belong to one group."
                    );
                }
                $allRanksInGroups[] = $rankId;
            }

            $groupIndex++;
        }
    }

    /**
     * Validate total manpower doesn't exceed system limits
     */
    private function validateTotalManpower(Validator $validator, array $data): void
    {
        $totalManpower = 0;

        // Calculate roster manpower
        foreach ($data['rank_manpower'] ?? [] as $rankData) {
            if (isset($rankData['manpower'])) {
                $totalManpower += (int) $rankData['manpower'];
            }
        }

        foreach ($data['rank_groups'] ?? [] as $groupData) {
            if (isset($groupData['manpower'])) {
                $totalManpower += (int) $groupData['manpower'];
            }
        }

        // Add fixed soldiers count
        $fixedSoldiersCount = count($data['fixed_soldiers'] ?? []);
        $totalAssignments = $totalManpower + $fixedSoldiersCount;

        // Business rule: Maximum 100 total assignments per duty
        if ($totalAssignments > 100) {
            $validator->errors()->add(
                'base',
                "Total assignments cannot exceed 100. Current total: {$totalAssignments} ({$totalManpower} roster + {$fixedSoldiersCount} fixed)"
            );
        }

        // Business rule: Minimum 1 total assignment
        if ($totalAssignments < 1) {
            $validator->errors()->add(
                'base',
                "Duty must have at least one assignment (roster or fixed)."
            );
        }
    }

    /**
     * Calculate daily duration in hours considering overnight duties and 24-hour duties
     * UPDATED: Now handles 24-hour duties correctly
     */
    private function calculateDailyDuration(string $startTime, string $endTime): float
    {
        // Normalize time formats
        $startTime = $this->normalizeTime($startTime);
        $endTime = $this->normalizeTime($endTime);

        // If start and end times are the same, it's a 24-hour duty
        if ($startTime === $endTime) {
            return 24.0;
        }

        $start = Carbon::createFromTimeString($startTime);
        $end = Carbon::createFromTimeString($endTime);

        // If end time is earlier than start time, it spans to next day
        if ($end->lt($start)) {
            $end->addDay();
        }

        // Calculate the difference in hours with minutes as decimal
        $durationInMinutes = $end->diffInMinutes($start);
        return round($durationInMinutes / 60, 2);
    }

    public function messages(): array
    {
        return [
            // Basic field messages
            'duty_name.required' => 'Duty name is required.',
            'duty_name.unique' => 'A duty with this name already exists.',
            'start_time.required' => 'Start time is required.',
            'end_time.required' => 'End time is required.',
            'duration_days.required' => 'Duration days is required.',
            'status.required' => 'Status is required.',

            // Excused Next Day messages
            'excused_next_session_pt.boolean' => 'The excused next day PT field must be true or false.',
            'excused_next_session_games.boolean' => 'The excused next day Games field must be true or false.',
            'excused_next_session_roll_call.boolean' => 'The excused next day Roll Call field must be true or false.',
            'excused_next_session_parade.boolean' => 'The excused next day Parade field must be true or false.',

            // Roster assignment messages
            'rank_manpower.*.rank_id.required_with' => 'Rank selection is required when adding manpower.',
            'rank_manpower.*.rank_id.exists' => 'Selected rank does not exist.',
            'rank_manpower.*.manpower.required_with' => 'Manpower is required for selected rank.',
            'rank_manpower.*.manpower.min' => 'Manpower must be at least 1.',
            'rank_manpower.*.manpower.max' => 'Manpower cannot exceed 100.',

            // Rank group messages
            'rank_groups.*.manpower.required_with' => 'Manpower is required for rank groups.',
            'rank_groups.*.manpower.min' => 'Group manpower must be at least 1.',
            'rank_groups.*.manpower.max' => 'Group manpower cannot exceed 100.',
            'rank_groups.*.ranks.required_with' => 'At least one rank is required for the group.',
            'rank_groups.*.ranks.min' => 'Rank group must contain at least one rank.',
            'rank_groups.*.ranks.*.required' => 'Rank selection is required.',
            'rank_groups.*.ranks.*.exists' => 'Selected rank does not exist.',

            // Fixed soldier messages
            'fixed_soldiers.*.soldier_id.required_with' => 'Soldier selection is required.',
            'fixed_soldiers.*.soldier_id.exists' => 'Selected soldier does not exist.',
            'fixed_soldiers.*.priority.min' => 'Priority must be at least 1.',
            'fixed_soldiers.*.priority.max' => 'Priority cannot exceed 10.',
            'fixed_soldiers.*.remarks.max' => 'Remarks cannot exceed 500 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'duty_name' => 'duty name',
            'start_time' => 'start time',
            'end_time' => 'end time',
            'duration_days' => 'duration days',
            'remark' => 'remarks',

            // Excused Next Day attributes
            'excused_next_session_pt' => 'excused next day PT',
            'excused_next_session_games' => 'excused next day Games',
            'excused_next_session_roll_call' => 'excused next day Roll Call',
            'excused_next_session_parade' => 'excused next day Parade',

            'rank_manpower.*.rank_id' => 'rank',
            'rank_manpower.*.manpower' => 'manpower',
            'rank_groups.*.manpower' => 'group manpower',
            'rank_groups.*.ranks' => 'group ranks',
            'fixed_soldiers.*.soldier_id' => 'soldier',
            'fixed_soldiers.*.priority' => 'priority',
            'fixed_soldiers.*.remarks' => 'remarks',
        ];
    }

    /**
     * Prepare the data for validation (trim strings, etc.)
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'duration_days' => (int) ($this->duration_days ?? 1),
            'manpower' => (int) ($this->manpower ?? 0),
            // Handle unchecked checkboxes
            'excused_next_session_pt' => $this->has('excused_next_session_pt'),
            'excused_next_session_games' => $this->has('excused_next_session_games'),
            'excused_next_session_roll_call' => $this->has('excused_next_session_roll_call'),
            'excused_next_session_parade' => $this->has('excused_next_session_parade'),
        ]);

        // Trim string inputs
        if ($this->has('duty_name')) {
            $this->merge(['duty_name' => trim($this->duty_name)]);
        }

        if ($this->has('remark')) {
            $this->merge(['remark' => trim($this->remark)]);
        }

        // Handle JSON inputs for complex data structures
        if ($this->has('rank_manpower') && is_string($this->rank_manpower)) {
            $this->merge([
                'rank_manpower' => json_decode($this->rank_manpower, true) ?? []
            ]);
        }

        if ($this->has('rank_groups') && is_string($this->rank_groups)) {
            $this->merge([
                'rank_groups' => json_decode($this->rank_groups, true) ?? []
            ]);
        }

        if ($this->has('fixed_soldiers') && is_string($this->fixed_soldiers)) {
            $this->merge([
                'fixed_soldiers' => json_decode($this->fixed_soldiers, true) ?? []
            ]);
        }
    }
}
