<?php

namespace App\Services;

use App\Models\Soldier;
use Illuminate\Support\Collection;

class ProfileDataFormatter
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
            'rank'      => optional($profile->rank)->name,
            'unit'      => optional($profile->company)->name,
            'current'   => optional($current)->appointments_name ?? 'N/A',
            'previous'  => $previous->pluck('appointments_name')->implode(', '),

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
