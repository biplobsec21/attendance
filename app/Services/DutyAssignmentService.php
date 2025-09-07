<?php

namespace App\Services;

use App\Models\Duty;
use App\Models\Soldier;
use App\Models\SoldierDuty;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DutyAssignmentService
{
    /**
     * Assign duties for a given date.
     *
     * @param  string|Carbon  $date
     */
    public function assignDutiesForDate(string|Carbon $date): void
    {
        // Normalize date
        $date = $date instanceof Carbon ? $date->copy() : Carbon::parse($date);

        $yesterday = $date->copy()->subDay()->toDateString();
        $today     = $date->toDateString();

        // Preload assignments for yesterday + today
        $assigned = SoldierDuty::whereIn('assigned_date', [$yesterday, $today])
            ->get()
            ->groupBy('soldier_id');

        Duty::with('ranks')->get()->each(function ($duty) use ($date, $assigned, $yesterday) {
            if ($duty->is_fixed) {
                $this->assignFixedDuty($duty, $date, $assigned);
            } elseif ($duty->is_rotating) {
                $this->assignRotatingDuty($duty, $date, $assigned, $yesterday);
            } else {
                $this->assignRegularDuty($duty, $date, $assigned);
            }
        });
    }

    private function assignFixedDuty(Duty $duty, Carbon $date, $assigned): void
    {
        foreach ($duty->ranks as $rank) {
            $fixedId = $rank->pivot->fixed_soldier_id;

            if ($fixedId) {
                $this->createAssignment($duty, $fixedId, $date, $assigned);
            } else {
                $eligible = Soldier::availableForDuty()
                    ->where('rank_id', $rank->id)
                    ->inRandomOrder()
                    ->take($rank->pivot->manpower)
                    ->pluck('id');

                foreach ($eligible as $soldierId) {
                    $this->createAssignment($duty, $soldierId, $date, $assigned);
                }
            }
        }
    }

    private function assignRotatingDuty(Duty $duty, Carbon $date, $assigned, string $yesterday): void
    {
        foreach ($duty->ranks as $rank) {
            $rankEligible = Soldier::availableForDuty()
                ->where('rank_id', $rank->id)
                ->pluck('id');

            // Remove yesterday’s assignees for same duty
            $yesterdayAssigned = SoldierDuty::where('duty_id', $duty->id)
                ->where('assigned_date', $yesterday)
                ->pluck('soldier_id');

            $eligible = $rankEligible->diff($yesterdayAssigned)->shuffle()->take($rank->pivot->manpower);

            if ($eligible->isEmpty()) {
                Log::warning("No eligible soldiers for rotating duty {$duty->id} on {$date->toDateString()} (rank {$rank->id})");
            }

            foreach ($eligible as $soldierId) {
                $this->createAssignment($duty, $soldierId, $date, $assigned);
            }
        }
    }

    private function assignRegularDuty(Duty $duty, Carbon $date, $assigned): void
    {
        // Map manpower by rank from pivot table
        $manpowerByRank = $duty->ranks->mapWithKeys(fn($r) => [$r->id => $r->pivot->manpower]);

        foreach ($manpowerByRank as $rankId => $manpower) {
            $eligible = Soldier::availableForDuty()
                ->where('rank_id', $rankId)
                ->inRandomOrder()
                ->take($manpower)
                ->pluck('id');

            if ($eligible->isEmpty()) {
                Log::warning("No eligible soldiers for regular duty {$duty->id} on {$date->toDateString()} (rank {$rankId})");
            }

            foreach ($eligible as $soldierId) {
                $this->createAssignment($duty, $soldierId, $date, $assigned);
            }
        }
    }

    private function createAssignment(Duty $duty, int $soldierId, Carbon $date, $assigned): void
    {
        if ($this->hasOverlappingDutyInCollection($soldierId, $duty, $date, $assigned)) {
            Log::info("Skipped soldier {$soldierId} for duty {$duty->id} on {$date->toDateString()} due to overlap");
            return;
        }

        SoldierDuty::firstOrCreate([
            'soldier_id'    => $soldierId,
            'duty_id'       => $duty->id,
            'assigned_date' => $date->toDateString(),
        ], [
            'start_time' => $duty->start_time,
            'end_time'   => $duty->end_time,
        ]);

        $assigned[$soldierId] ??= collect();
        $assigned[$soldierId]->push((object)[
            'duty_id'     => $duty->id,
            'start_time'  => $duty->start_time,
            'end_time'    => $duty->end_time,
        ]);
    }

    private function hasOverlappingDutyInCollection(int $soldierId, Duty $duty, Carbon $date, $assigned): bool
    {
        $start = Carbon::parse($date->toDateString() . ' ' . $duty->start_time);
        $end   = Carbon::parse($date->toDateString() . ' ' . $duty->end_time);

        // Handle overnight duties (end < start)
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        foreach ($assigned->get($soldierId, collect()) as $a) {
            $aStart = Carbon::parse($date->toDateString() . ' ' . $a->start_time);
            $aEnd   = Carbon::parse($date->toDateString() . ' ' . $a->end_time);

            if ($aEnd->lessThanOrEqualTo($aStart)) {
                $aEnd->addDay();
            }

            if ($start->lt($aEnd) && $end->gt($aStart)) {
                return true;
            }
        }

        return false;
    }
}
