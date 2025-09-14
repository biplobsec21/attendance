<?php

namespace App\Services;

use App\Models\Soldier;
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
        // dd($previous);
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
            'is_leave' => $profile->is_leave,
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

            'actions'   => view('mpm.page.profile.partials.actions', compact('profile'))->render(),
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
                'name'       => $data->name, // consider $data->name if available
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
                'name'   => $data->name, // consider $data->name
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
}
