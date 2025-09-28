<?php

namespace App\Services;

use App\Models\Duty;
use App\Models\Soldier;
use App\Models\SoldierDuty;
use App\Models\SoldierCadre;
use App\Models\SoldierCourse;
use App\Models\SoldierServices;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DutyAssignmentService
{
    /**
     * Assign roster duties for a given date.
     */
    public function assignDutiesForDate($date)
    {
        $date = Carbon::parse($date)->toDateString();

        // 1. Get all active roster duties with manpower requirements
        $duties = Duty::where('status', 'Active')
            ->whereHas('dutyRanks', function ($q) {
                $q->where('duty_type', 'roster');
            })
            ->with(['dutyRanks' => function ($q) {
                $q->where('duty_type', 'roster');
            }])
            ->get();

        // 2. Build exclusion list: soldiers on leave, cadre, course, service for this date
        $excludedSoldierIds = $this->getExcludedSoldierIds($date);

        // 3. For each duty and rank, assign soldiers
        foreach ($duties as $duty) {
            foreach ($duty->dutyRanks as $dutyRank) {
                $rankId = $dutyRank->rank_id;
                $manpower = $dutyRank->manpower ?? 1;

                // Get eligible soldiers for this rank
                $eligibleSoldiers = Soldier::where('rank_id', $rankId)
                    ->where('status', true)
                    ->whereNotIn('id', $excludedSoldierIds)
                    ->whereDoesntHave('currentLeaveApplications')
                    ->get();

                // Filter: not assigned same duty yesterday (fairness)
                // Filter: not assigned same duty on the most recent previous date (fairness)
                $eligibleSoldiers = $eligibleSoldiers->filter(function ($soldier) use ($duty, $date) {
                    // Find the latest previous assignment date for this soldier and duty
                    $lastAssignment = SoldierDuty::where('soldier_id', $soldier->id)
                        ->where('duty_id', $duty->id)
                        ->where('assigned_date', '<', $date)
                        ->orderByDesc('assigned_date')
                        ->first();
                    // dd($lastAssignment);
                    // If last assignment was exactly the day before, skip this soldier
                    if ($lastAssignment) {
                        $lastDate = Carbon::parse($lastAssignment->assigned_date);
                        $currentDate = Carbon::parse($date);
                        if ($lastDate->diffInDays($currentDate) === 1) {
                            return false;
                        }
                    }
                    return true;
                });

                // Filter: not already assigned to any duty for this date (unique assignment)
                $eligibleSoldiers = $eligibleSoldiers->filter(function ($soldier) use ($date) {
                    return !SoldierDuty::where('soldier_id', $soldier->id)
                        ->where('assigned_date', $date)
                        ->exists();
                });
                // dd($eligibleSoldiers);

                // Assign up to manpower required
                $toAssign = $eligibleSoldiers->take($manpower);
                // dd($eligibleSoldiers);
                foreach ($toAssign as $soldier) {
                    SoldierDuty::updateOrCreate(
                        [
                            'soldier_id'    => $soldier->id,
                            'duty_id'       => $duty->id,
                            'assigned_date' => $date,
                        ],
                        [
                            'start_time' => $duty->start_time,
                            'end_time'   => $duty->end_time,
                            'status'     => 'assigned',
                        ]
                    );
                }
            }
        }
    }

    /**
     * Get all soldier IDs who are unavailable for roster assignment on a given date.
     */
    protected function getExcludedSoldierIds($date)
    {
        $date = Carbon::parse($date);

        // Cadres
        $cadreIds = SoldierCadre::where('status', 'active')
            ->orWhere(function ($q) use ($date) {
                $q->where('status', 'scheduled')
                    ->whereDate('start_date', '<=', $date)
                    ->where(function ($q2) use ($date) {
                        $q2->whereNull('end_date')->orWhereDate('end_date', '>=', $date);
                    });
            })
            ->pluck('soldier_id')
            ->toArray();

        // Courses
        $courseIds = SoldierCourse::where('status', 'active')
            ->orWhere(function ($q) use ($date) {
                $q->where('status', 'scheduled')
                    ->whereDate('start_date', '<=', $date)
                    ->where(function ($q2) use ($date) {
                        $q2->whereNull('end_date')->orWhereDate('end_date', '>=', $date);
                    });
            })
            ->pluck('soldier_id')
            ->toArray();

        // Services
        $serviceIds = SoldierServices::where('status', 'active')
            ->orWhere(function ($q) use ($date) {
                $q->where('status', 'scheduled')
                    ->whereDate('appointments_from_date', '<=', $date)
                    ->where(function ($q2) use ($date) {
                        $q2->whereNull('appointments_to_date')->orWhereDate('appointments_to_date', '>=', $date);
                    });
            })
            ->pluck('soldier_id')
            ->toArray();

        // Leave (handled in eligibleSoldiers query, but can be included here if needed)
        // $leaveIds = Soldier::onLeave()->pluck('id')->toArray();

        return array_unique(array_merge($cadreIds, $courseIds, $serviceIds));
    }
}
