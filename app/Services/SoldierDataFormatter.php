<?php

namespace App\Services;

use App\Models\Soldier;
use App\Models\LeaveApplication;
use App\Models\SoldierServices;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SoldierDataFormatter
{
    public function formatCollection(Collection $profiles): Collection
    {
        return $profiles->map(fn($profile) => $this->format($profile));
    }

    public function format($profile): array
    {
        $current  = $profile->services->where('appointment_type', 'current')->last();
        $previous = $profile->services->where('appointment_type', 'previous');

        return [
            'id'        => $profile->id,
            'name'      => $profile->full_name,
            'joining_date'      => $profile->joining_date,
            'army_no'      => $profile->army_no,
            'rank'      => optional($profile->rank)->name,
            'unit'      => optional($profile->company)->name,
            'current'   => optional($current)->appointments_name ?? 'N/A',
            'previous'  => $previous->pluck('appointments_name')->implode(', '),
            'personal_completed' => $profile->personal_completed,
            'service_completed' => $profile->service_completed,
            'qualifications_completed' => $profile->qualifications_completed,
            'medical_completed' => $profile->medical_completed,

            // appends data //
            'is_on_leave' => $profile->is_on_leave,
            'current_leave_details' => $profile->current_leave_details,

            'is_leave' => $profile->is_on_leave ? true : false,
            'is_sick' => $profile->is_sick,
            'status' => $profile->status,
            'mobile' => $profile->mobile,
            'blood_group' => $profile->blood_group,
            'image' => $profile->image ?? asset('/images/default-avatar.png'),
            'service_duration' => $this->duration($profile->joining_date),
            'marital_status' => $this->maritalinfo($profile->marital_status, $profile->num_boys, $profile->num_girls),
            'address' => $this->addressInfo($profile->district->name, $profile->permanent_address),

            // Extended Details
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

            // NEW: Added histories
            'duties_history'   => $this->formatDutiesHistory($profile),
            'leave_history'    => $this->formatLeaveHistory($profile),
            'appointment_history' => $this->formatAppointmentHistory($profile),

            // 'actions'   => view('mpm.page.profile.partials.actions', compact('profile'))->render(),
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
        // Base marital status
        $info = $status;

        // If married/divorced/widowed, include children info
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

        // Parse the date
        $joinDate = Carbon::parse($joining_date);
        $now = Carbon::now();

        // Calculate the difference
        $diff = $joinDate->diff($now);

        // Return formatted duration
        return "{$diff->y} years, {$diff->m} months, {$diff->d} days";
    }

    public function formatEducations(Soldier $profile)
    {
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
        return $profile->skills->map(function ($data) {
            return [
                'name'   => $data->name,
                'result' => $data->pivot->remarks,
            ];
        });
    }

    public function formatAtt(Soldier $profile)
    {
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
        return $profile->goodDiscipline->map(function ($data) {
            return [
                'name'    => $data->discipline_name,
                'remarks' => $data->remarks,
            ];
        });
    }

    public function formatBadDiscipline(Soldier $profile)
    {
        return $profile->punishmentDiscipline->map(function ($data) {
            return [
                'name'       => $data->discipline_name,
                'start_date' => $data->start_date,
                'remarks'    => $data->remarks,
            ];
        });
    }

    /**
     * NEW: Format duties history including fixed duties, courses, cadres, and active assignments
     */
    public function formatDutiesHistory(Soldier $profile): array
    {
        $dutiesHistory = [];

        // Get fixed duties history
        $fixedDuties = $profile->dutyRanks()
            ->where('assignment_type', 'fixed')
            ->with(['duty' => function ($query) {
                $query->select('id', 'duty_name', 'start_time', 'end_time', 'duration_days', 'status');
            }])
            ->get();

        foreach ($fixedDuties as $dutyAssignment) {
            $duty = $dutyAssignment->duty;

            // Calculate hours
            $dailyHours = 0;
            if ($duty && $duty->start_time && $duty->end_time) {
                try {
                    $start = Carbon::createFromTimeString($duty->start_time);
                    $end = Carbon::createFromTimeString($duty->end_time);
                    if ($end->lt($start)) {
                        $end->addDay();
                    }
                    $dailyHours = $end->diffInHours($start);
                } catch (\Exception $e) {
                    // Handle invalid time format
                }
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

        // Get courses as duties
        foreach ($profile->courses as $course) {
            $dutiesHistory[] = [
                'type' => 'course_duty',
                'name' => $course->name,
                'course_name' => $course->name,
                'start_date' => $course->pivot->start_date,
                'end_date' => $course->pivot->end_date,
                'status' => $course->pivot->course_status,
                'result' => $course->pivot->remarks,
                'assignment_type' => 'course',
                'duration_days' => $course->pivot->start_date && $course->pivot->end_date ?
                    Carbon::parse($course->pivot->start_date)->diffInDays(Carbon::parse($course->pivot->end_date)) : null,
            ];
        }

        // Get cadres as duties
        foreach ($profile->cadres as $cadre) {
            $dutiesHistory[] = [
                'type' => 'cadre_duty',
                'name' => $cadre->name,
                'cadre_name' => $cadre->name,
                'start_date' => $cadre->pivot->start_date,
                'end_date' => $cadre->pivot->end_date,
                'status' => $cadre->pivot->course_status,
                'result' => $cadre->pivot->remarks,
                'assignment_type' => 'cadre',
                'duration_days' => $cadre->pivot->start_date && $cadre->pivot->end_date ?
                    Carbon::parse($cadre->pivot->start_date)->diffInDays(Carbon::parse($cadre->pivot->end_date)) : null,
            ];
        }

        // Get active assignments
        $activeAssignments = $profile->getActiveAssignments();
        foreach ($activeAssignments as $assignment) {
            if ($assignment['type'] === 'fixed_duty') {
                $dutiesHistory[] = array_merge($assignment, [
                    'is_active' => true,
                    'assignment_type' => 'fixed_duty'
                ]);
            }
        }

        // Sort by start date (most recent first)
        usort($dutiesHistory, function ($a, $b) {
            $dateA = $this->getDutyStartDate($a);
            $dateB = $this->getDutyStartDate($b);
            return $dateB <=> $dateA; // Descending order
        });

        return $dutiesHistory;
    }

    /**
     * Helper method to extract start date from duty record
     */
    private function getDutyStartDate(array $duty): ?string
    {
        return $duty['start_date'] ??
            $duty['assignment_date'] ??
            (isset($duty['created_at']) ? $duty['created_at'] : null);
    }

    /**
     * NEW: Format leave history
     */
    public function formatLeaveHistory(Soldier $profile): array
    {
        $leaveHistory = [];

        // Get all leave applications, ordered by most recent first
        $leaveApplications = $profile->leaveApplications()
            ->with('leaveType')
            ->orderBy('start_date', 'desc')
            ->get();

        foreach ($leaveApplications as $leave) {
            $leaveType = $leave->leaveType;

            // Calculate duration
            $duration = 0;
            if ($leave->start_date && $leave->end_date) {
                $duration = Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
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
                'is_current' => $leave->application_current_status === 'approved' &&
                    $leave->start_date && $leave->end_date &&
                    Carbon::today()->between($leave->start_date, $leave->end_date),
            ];
        }

        return $leaveHistory;
    }

    /**
     * NEW: Format appointment history (services history)
     */
    public function formatAppointmentHistory(Soldier $profile): array
    {
        $appointmentHistory = [];

        // Get all services, ordered by most recent first
        $services = $profile->services()
            ->orderBy('appointments_from_date', 'desc')
            ->get();

        foreach ($services as $service) {
            // Calculate duration
            $duration = null;
            if ($service->appointments_from_date && $service->appointments_to_date) {
                $duration = Carbon::parse($service->appointments_from_date)
                    ->diffInDays(Carbon::parse($service->appointments_to_date)) + 1;
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
}
