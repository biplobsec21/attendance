<?php

namespace App\Services;

use App\Models\Soldier;
use App\Models\LeaveApplication;
use App\Models\SoldierServices;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SoldierDataFormatter
{
    /**
     * Cache for Carbon instances to avoid repeated parsing
     */
    private array $carbonCache = [];

    public function formatCollection(Collection $profiles): Collection
    {
        return $profiles->map(fn($profile) => $this->format($profile));
    }

    /**
     * Get optimized query with all necessary eager loading
     * Call this BEFORE passing soldiers to formatCollection
     */
    public static function getOptimizedQuery($query)
    {
        return $query->with([
            'rank:id,name',
            'company:id,name',
            'district:id,name',
            'services' => function ($q) {
                $q->select(
                    'id',
                    'soldier_id',
                    'appointments_name',
                    'appointment_type',
                    'appointment_id',
                    'appointments_from_date',
                    'appointments_to_date',
                    'status',
                    'note'
                )
                    ->orderBy('appointments_from_date', 'desc');
            },
            'educations:id,name',
            'courses:id,name',
            'cadres:id,name',
            'skills:id,name',
            'att:id,name',
            'ere:id,name',
            'medicalCategory:id,name',
            'sickness:id,name',
            'goodDiscipline:id,discipline_name,remarks',
            'punishmentDiscipline:id,discipline_name,start_date,remarks',
            'dutyRanks' => function ($q) {
                $q->where('assignment_type', 'fixed')
                    ->with(['duty:id,duty_name,start_time,end_time,duration_days,status'])
                    ->select('id', 'soldier_id', 'duty_id', 'assignment_type', 'priority', 'remarks', 'created_at');
            },
            'leaveApplications' => function ($q) {
                $q->with('leaveType:id,name')
                    ->orderBy('start_date', 'desc')
                    ->select(
                        'id',
                        'soldier_id',
                        'leave_type_id',
                        'reason',
                        'start_date',
                        'end_date',
                        'application_current_status',
                        'hard_copy',
                        'created_at'
                    );
            }
        ]);
    }

    public function format($profile): array
    {
        // Pre-filter services for better performance
        $services = $profile->services ?? collect();
        $current = $services->where('appointment_type', 'current')->last();
        $previous = $services->where('appointment_type', 'previous');

        return [
            'id'        => $profile->id,
            'name'      => $profile->full_name,
            'districts'  => $profile->district?->name,
            'joining_date'      => $profile->joining_date,
            'army_no'      => $profile->army_no,
            'rank'      => $profile->rank?->name,
            'unit'      => $profile->company?->name,
            'current'   => $current?->appointments_name ?? 'N/A',
            'previous'  => $previous->pluck('appointments_name')->implode(', '),
            'personal_completed' => $profile->personal_completed,
            'service_completed' => $profile->service_completed,
            'qualifications_completed' => $profile->qualifications_completed,
            'medical_completed' => $profile->medical_completed,

            // appends data //
            'is_on_leave' => $profile->is_on_leave,
            'current_leave_details' => $profile->current_leave_details,

            'is_leave' => (bool) $profile->is_on_leave,
            'is_sick' => $profile->is_sick,
            'status' => $profile->status,
            'mobile' => $profile->mobile,
            'blood_group' => $profile->blood_group,
            'image' => $profile->image ?? asset('/images/default-avatar.png'),
            'service_duration' => $this->duration($profile->joining_date),
            'marital_status' => $this->maritalinfo($profile->marital_status, $profile->num_boys, $profile->num_girls),
            'address' => $this->addressInfo($profile->district?->name, $profile->permanent_address),

            // Extended Details - Using cached collections
            'educations'       => $this->formatEducations($profile),
            'courses'          => $this->formatCourses($profile),
            'cadres'           => $this->formatCadres($profile),
            'cocurricular'     => $this->formatSkills($profile),
            'att'              => $this->formatAtt($profile),
            'ere'              => $this->formatEre($profile),
            'medical'          => $this->formatMedical($profile),
            'sickness'         => $this->formatSickness($profile),
            'good_behavior'    => $this->formatGoodDiscipline($profile),
            'bad_behavior'     => $this->formatBadDiscipline($profile),

            // Histories
            // 'duties_history'   => $this->formatDutiesHistory($profile),
            // 'leave_history'    => $this->formatLeaveHistory($profile),
            // 'appointment_history' => $this->formatAppointmentHistory($profile),
        ];
    }

    public function addressInfo($district, $address)
    {
        $fullAddress = trim("{$address}, {$district}", ', ');

        if (empty($fullAddress)) {
            return '<span class="text-gray-500"><i class="fas fa-map-marker-alt fa-fw text-gray-400"></i> N/A</span>';
        }

        return '<span class="text-gray-700">
                <i class="fas fa-map-marker-alt fa-fw text-green-200"></i>
                ' . e($fullAddress) . '
            </span>';
    }

    public function maritalinfo($status, $boys = 0, $girls = 0)
    {
        $info = $status;

        if (in_array($status, ['Married', 'Divorced', 'Widowed'])) {
            $children = [];

            if ($boys > 0) {
                $children[] = "{$boys} boy" . ($boys > 1 ? 's' : '');
            }

            if ($girls > 0) {
                $children[] = "{$girls} girl" . ($girls > 1 ? 's' : '');
            }

            if (!empty($children)) {
                $info .= ' (' . implode(', ', $children) . ')';
            }
        }

        return $info;
    }

    public function duration($joining_date)
    {
        if (!$joining_date) {
            return 'N/A';
        }

        // Use cached Carbon instance
        $cacheKey = (string) $joining_date;
        if (!isset($this->carbonCache[$cacheKey])) {
            $this->carbonCache[$cacheKey] = Carbon::parse($joining_date);
        }

        $joinDate = $this->carbonCache[$cacheKey];
        $diff = $joinDate->diff(Carbon::now());

        return "{$diff->y} years, {$diff->m} months, {$diff->d} days";
    }

    public function formatEducations(Soldier $profile)
    {
        if (!$profile->relationLoaded('educations') || $profile->educations->isEmpty()) {
            return collect([]);
        }

        return $profile->educations->map(function ($edu) {
            return [
                'name'   => $edu->name,
                'status' => $edu->pivot->result,
                'year'   => $edu->pivot->passing_year,
                'remark' => $edu->pivot->remark,
            ];
        });
    }

    public function formatCourses(Soldier $profile)
    {
        if (!$profile->relationLoaded('courses') || $profile->courses->isEmpty()) {
            return collect([]);
        }

        return $profile->courses->map(function ($data) {
            return [
                'name'       => $data->name,
                'status'     => $data->pivot->course_status,
                'start_date' => $data->pivot->start_date,
                'end_date'   => $data->pivot->end_date,
                'result'     => $data->pivot->remarks,
            ];
        });
    }

    public function formatCadres(Soldier $profile)
    {
        if (!$profile->relationLoaded('cadres') || $profile->cadres->isEmpty()) {
            return collect([]);
        }

        return $profile->cadres->map(function ($data) {
            return [
                'name'       => $data->name,
                'status'     => $data->pivot->course_status,
                'start_date' => $data->pivot->start_date,
                'end_date'   => $data->pivot->end_date,
                'result'     => $data->pivot->remarks,
            ];
        });
    }

    public function formatSkills(Soldier $profile)
    {
        if (!$profile->relationLoaded('skills') || $profile->skills->isEmpty()) {
            return collect([]);
        }

        return $profile->skills->map(function ($data) {
            return [
                'name'   => $data->name,
                'result' => $data->pivot->remarks,
            ];
        });
    }

    public function formatAtt(Soldier $profile)
    {
        if (!$profile->relationLoaded('att') || $profile->att->isEmpty()) {
            return collect([]);
        }

        return $profile->att->map(function ($data) {
            return [
                'name'       => $data->name,
                'start_date' => $data->pivot->start_date,
                'end_date'   => $data->pivot->end_date,
            ];
        });
    }

    public function formatEre(Soldier $profile)
    {
        if (!$profile->relationLoaded('ere') || $profile->ere->isEmpty()) {
            return collect([]);
        }

        return $profile->ere->map(function ($data) {
            return [
                'name'       => $data->name,
                'start_date' => $data->pivot->start_date,
                'end_date'   => $data->pivot->end_date,
            ];
        });
    }

    public function formatMedical(Soldier $profile)
    {
        if (!$profile->relationLoaded('medicalCategory') || $profile->medicalCategory->isEmpty()) {
            return collect([]);
        }

        return $profile->medicalCategory->map(function ($data) {
            return [
                'category'   => $data->name,
                'remarks'    => $data->pivot->remarks,
                'start_date' => $data->pivot->start_date,
                'end_date'   => $data->pivot->end_date,
            ];
        });
    }

    public function formatSickness(Soldier $profile)
    {
        if (!$profile->relationLoaded('sickness') || $profile->sickness->isEmpty()) {
            return collect([]);
        }

        return $profile->sickness->map(function ($data) {
            return [
                'category'   => $data->name,
                'remarks'    => $data->pivot->remarks,
                'start_date' => $data->pivot->start_date,
                'end_date'   => $data->pivot->end_date,
            ];
        });
    }

    public function formatGoodDiscipline(Soldier $profile)
    {
        if (!$profile->relationLoaded('goodDiscipline') || $profile->goodDiscipline->isEmpty()) {
            return collect([]);
        }

        return $profile->goodDiscipline->map(function ($data) {
            return [
                'name'    => $data->discipline_name,
                'remarks' => $data->remarks,
            ];
        });
    }

    public function formatBadDiscipline(Soldier $profile)
    {
        if (!$profile->relationLoaded('punishmentDiscipline') || $profile->punishmentDiscipline->isEmpty()) {
            return collect([]);
        }

        return $profile->punishmentDiscipline->map(function ($data) {
            return [
                'name'       => $data->discipline_name,
                'start_date' => $data->start_date,
                'remarks'    => $data->remarks,
            ];
        });
    }

    public function formatDutiesHistory(Soldier $profile): array
    {
        $dutiesHistory = [];

        // Fixed duties - already eager loaded
        if ($profile->relationLoaded('dutyRanks')) {
            $fixedDuties = $profile->dutyRanks;

            foreach ($fixedDuties as $dutyAssignment) {
                $duty = $dutyAssignment->duty;

                $dailyHours = 0;
                if ($duty && $duty->start_time && $duty->end_time) {
                    $dailyHours = $this->calculateDailyHours($duty->start_time, $duty->end_time);
                }

                $dutiesHistory[] = [
                    'type' => 'fixed_duty',
                    'name' => $duty ? $duty->duty_name : 'Unknown Duty',
                    'duty_name' => $duty ? $duty->duty_name : 'Unknown Duty',
                    'start_time' => $duty->start_time ?? null,
                    'end_time' => $duty->end_time ?? null,
                    'duration_days' => $duty->duration_days ?? 0,
                    'daily_hours' => $dailyHours,
                    'total_hours' => $dailyHours * ($duty->duration_days ?? 0),
                    'priority' => $dutyAssignment->priority,
                    'remarks' => $dutyAssignment->remarks,
                    'status' => $duty->status ?? 'unknown',
                    'assignment_type' => 'fixed',
                    'assignment_date' => $dutyAssignment->created_at?->toDateString(),
                ];
            }
        }

        // Courses - already eager loaded
        if ($profile->relationLoaded('courses')) {
            foreach ($profile->courses as $course) {
                $durationDays = null;
                if ($course->pivot->start_date && $course->pivot->end_date) {
                    $durationDays = $this->calculateDaysDiff($course->pivot->start_date, $course->pivot->end_date);
                }

                $dutiesHistory[] = [
                    'type' => 'course_duty',
                    'name' => $course->name,
                    'course_name' => $course->name,
                    'start_date' => $course->pivot->start_date,
                    'end_date' => $course->pivot->end_date,
                    'status' => $course->pivot->course_status,
                    'result' => $course->pivot->remarks,
                    'assignment_type' => 'course',
                    'duration_days' => $durationDays,
                ];
            }
        }

        // Cadres - already eager loaded
        if ($profile->relationLoaded('cadres')) {
            foreach ($profile->cadres as $cadre) {
                $durationDays = null;
                if ($cadre->pivot->start_date && $cadre->pivot->end_date) {
                    $durationDays = $this->calculateDaysDiff($cadre->pivot->start_date, $cadre->pivot->end_date);
                }

                $dutiesHistory[] = [
                    'type' => 'cadre_duty',
                    'name' => $cadre->name,
                    'cadre_name' => $cadre->name,
                    'start_date' => $cadre->pivot->start_date,
                    'end_date' => $cadre->pivot->end_date,
                    'status' => $cadre->pivot->course_status,
                    'result' => $cadre->pivot->remarks,
                    'assignment_type' => 'cadre',
                    'duration_days' => $durationDays,
                ];
            }
        }

        // Active assignments - only if method exists
        if (method_exists($profile, 'getActiveAssignments')) {
            $activeAssignments = $profile->getActiveAssignments();
            foreach ($activeAssignments as $assignment) {
                if ($assignment['type'] === 'fixed_duty') {
                    $dutiesHistory[] = array_merge($assignment, [
                        'is_active' => true,
                        'assignment_type' => 'fixed_duty'
                    ]);
                }
            }
        }

        // Sort by start date
        usort($dutiesHistory, function ($a, $b) {
            $dateA = $this->getDutyStartDate($a);
            $dateB = $this->getDutyStartDate($b);
            return $dateB <=> $dateA;
        });

        return $dutiesHistory;
    }

    /**
     * Calculate daily hours between two times
     */
    private function calculateDailyHours($startTime, $endTime): float
    {
        try {
            $start = Carbon::createFromTimeString($startTime);
            $end = Carbon::createFromTimeString($endTime);
            if ($end->lt($start)) {
                $end->addDay();
            }
            return $end->diffInHours($start);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calculate days difference between two dates
     */
    private function calculateDaysDiff($startDate, $endDate): ?int
    {
        try {
            return Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getDutyStartDate(array $duty): ?string
    {
        return $duty['start_date'] ??
            $duty['assignment_date'] ??
            (isset($duty['created_at']) ? $duty['created_at'] : null);
    }

    public function formatLeaveHistory(Soldier $profile): array
    {
        if (!$profile->relationLoaded('leaveApplications') || $profile->leaveApplications->isEmpty()) {
            return [];
        }

        $leaveHistory = [];
        $today = Carbon::today();

        foreach ($profile->leaveApplications as $leave) {
            $leaveType = $leave->leaveType;

            $duration = 0;
            $isCurrent = false;

            if ($leave->start_date && $leave->end_date) {
                $duration = $this->calculateDaysDiff($leave->start_date, $leave->end_date) + 1;

                if ($leave->application_current_status === 'approved') {
                    $isCurrent = $today->between($leave->start_date, $leave->end_date);
                }
            }

            $leaveHistory[] = [
                'id' => $leave->id,
                'leave_type' => $leaveType ? $leaveType->name : 'Unknown',
                'reason' => $leave->reason,
                'start_date' => $leave->start_date?->toDateString(),
                'end_date' => $leave->end_date?->toDateString(),
                'duration_days' => $duration,
                'status' => $leave->application_current_status,
                'hard_copy' => $leave->hard_copy,
                'application_date' => $leave->created_at?->toDateString(),
                'is_current' => $isCurrent,
            ];
        }

        return $leaveHistory;
    }

    public function formatAppointmentHistory(Soldier $profile): array
    {
        if (!$profile->relationLoaded('services') || $profile->services->isEmpty()) {
            return [];
        }

        $appointmentHistory = [];

        foreach ($profile->services as $service) {
            $duration = null;
            if ($service->appointments_from_date && $service->appointments_to_date) {
                $duration = $this->calculateDaysDiff(
                    $service->appointments_from_date,
                    $service->appointments_to_date
                ) + 1;
            }

            $appointmentHistory[] = [
                'id' => $service->id,
                'appointment_name' => $service->appointments_name,
                'appointment_type' => $service->appointment_type,
                'appointment_id' => $service->appointment_id,
                'from_date' => $service->appointments_from_date?->toDateString(),
                'to_date' => $service->appointments_to_date?->toDateString(),
                'duration_days' => $duration,
                'status' => $service->status,
                'note' => $service->note,
                'is_current' => $service->appointment_type === 'current' &&
                    $service->status === 'active',
                'is_active' => $service->status === 'active',
                'is_completed' => $service->status === 'completed',
            ];
        }

        return $appointmentHistory;
    }
    // In App\Services\SoldierDataFormatter

    /**
     * Format ATT history data
     */
    public function formatAttHistory(Soldier $soldier): array
    {
        return $soldier->att->map(function ($att) use ($soldier) {
            $startDate = $att->pivot->start_date;
            $endDate = $att->pivot->end_date;

            $today = Carbon::today();
            $start = Carbon::parse($startDate);
            $end = $endDate ? Carbon::parse($endDate) : null;

            $isActive = $start->lte($today) && (!$end || $end->gte($today));
            $status = $isActive ? 'active' : ($end && $end->isPast() ? 'completed' : 'scheduled');
            $durationDays = $endDate ? $start->diffInDays(Carbon::parse($endDate)) + 1 : null;

            return [
                'id' => $att->id,
                'name' => $att->name,
                'type' => 'Att',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'remarks' => $att->pivot->remarks,
                'status' => $status,
                'is_active' => $isActive,
                'is_current' => $isActive,
                'duration_days' => $durationDays,
                'test_period' => $this->formatAttPeriod($startDate, $endDate),
            ];
        })->toArray();
    }

    /**
     * Format ATT period for display
     */
    protected function formatAttPeriod($startDate, $endDate): string
    {
        if (!$startDate) {
            return 'Not scheduled';
        }

        $start = Carbon::parse($startDate)->format('M d, Y');

        if (!$endDate) {
            return "From {$start} (Ongoing)";
        }

        $end = Carbon::parse($endDate)->format('M d, Y');
        return "{$start} - {$end}";
    }
    // In App\Services\SoldierDataFormatter

    /**
     * Format CMD history data
     */
    public function formatCmdHistory(Soldier $soldier): array
    {
        return $soldier->cmds->map(function ($cmd) {
            $startDate = $cmd->pivot->start_date;
            $endDate = $cmd->pivot->end_date;

            $today = Carbon::today();
            $start = Carbon::parse($startDate);
            $end = $endDate ? Carbon::parse($endDate) : null;

            $isActive = $start->lte($today) && (!$end || $end->gte($today));
            $status = $isActive ? 'active' : ($end && $end->isPast() ? 'completed' : 'scheduled');
            $durationDays = $endDate ? $start->diffInDays($end) + 1 : null;

            return [
                'id' => $cmd->id,
                'name' => $cmd->name,
                'type' => 'Command',
                'status' => $status,
                'is_active' => $isActive,
                'is_current' => $isActive,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'remarks' => $cmd->pivot->remarks,
                'duration_days' => $durationDays,
                'command_period' => $this->formatCmdPeriod($startDate, $endDate),
                'cmd_status' => $cmd->status,
                'status_badge' => $cmd->status ? 'Active' : 'Inactive',
            ];
        })->toArray();
    }

    /**
     * Format CMD period for display
     */
    protected function formatCmdPeriod($startDate, $endDate): string
    {
        if (!$startDate) {
            return 'Not scheduled';
        }

        $start = Carbon::parse($startDate)->format('M d, Y');

        if (!$endDate) {
            return "From {$start} (Ongoing)";
        }

        $end = Carbon::parse($endDate)->format('M d, Y');
        return "{$start} - {$end}";
    }
}
