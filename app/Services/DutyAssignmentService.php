<?php

namespace App\Services;

use App\Models\Duty;
use App\Models\Soldier;
use App\Models\SoldierDuty;
use Carbon\Carbon;

class DutyAssignmentService
{
    public function assignDutiesForDate(string $date)
    {
        $duties = Duty::with('ranks')->get();

        foreach ($duties as $duty) {
            $type = $this->getDutyType($duty);

            if ($type === 'fixed') {
                $this->assignFixedDuty($duty, $date);
            } elseif ($type === 'roster') {
                $this->assignRotatingDuty($duty, $date); // roaster duty is the regular duty
            } else {
                // $this->assignRegularDuty($duty, $date); // roaster duty is the regular duty
                $this->assignRotatingDuty($duty, $date);
            }
        }
    }

    protected function getDutyType(Duty $duty)
    {
        return $duty->ranks->first()->pivot->duty_type ?? $duty->duty_type;
    }

    /** ✅ Fixed duties */
    protected function assignFixedDuty(Duty $duty, string $date)
    {
        // Preload today's and yesterday's assignments for this duty
        $yesterday = Carbon::parse($date)->subDay()->toDateString();
        $assigned = SoldierDuty::where('duty_id', $duty->id)
            ->whereIn('assigned_date', [$date, $yesterday])
            ->get()
            ->groupBy('soldier_id');

        foreach ($duty->ranks as $rank) {
            $manpower = $rank->pivot->manpower;

            if ($rank->pivot->fixed_soldier_id) {
                $soldier = Soldier::where('id', $rank->pivot->fixed_soldier_id)
                    ->where('status', 'active')
                    ->where('is_sick', false)
                    ->where('is_leave', false)
                    ->first();

                if ($soldier && !$this->hasOverlappingDutyInCollection($soldier, $duty, $date, $assigned)) {
                    $this->createAssignment($soldier, $duty, $date);
                }
                continue;
            }

            $soldiers = Soldier::where('rank_id', $rank->id)
                ->where('status', 'active')
                ->where('is_sick', false)
                ->where('is_leave', false)
                ->orderBy('id')
                ->take($manpower)
                ->get();

            foreach ($soldiers as $soldier) {
                if (!$this->hasOverlappingDutyInCollection($soldier, $duty, $date, $assigned)) {
                    $this->createAssignment($soldier, $duty, $date);
                }
            }
        }
    }




    /** ✅ Rotating duties */
    protected function assignRotatingDuty(Duty $duty, string $date)
    {
        $rankIds = $duty->ranks->pluck('id');
        $manpowerByRank = $duty->ranks->mapWithKeys(fn($r) => [$r->id => $r->pivot->manpower]);

        // Preload eligible soldiers
        $eligible = Soldier::whereIn('rank_id', $rankIds)
            ->where('status', 'active')
            ->where('is_sick', false)
            ->where('is_leave', false)
            ->get();

        // Preload yesterday's and today's assignments
        $yesterday = Carbon::parse($date)->subDay()->toDateString();
        $assigned = SoldierDuty::where('duty_id', $duty->id)
            ->whereIn('assigned_date', [$date, $yesterday])
            ->get()
            ->groupBy('soldier_id');

        // Exclude yesterday's soldiers
        $recent = SoldierDuty::where('duty_id', $duty->id)
            ->where('assigned_date', $yesterday)
            ->pluck('soldier_id')
            ->toArray();

        $eligible = $eligible->whereNotIn('id', $recent);

        foreach ($manpowerByRank as $rankId => $manpower) {
            $rankEligible = $eligible->where('rank_id', $rankId)->shuffle();

            foreach ($rankEligible->take($manpower) as $soldier) {
                if ($this->hasOverlappingDutyInCollection($soldier, $duty, $date, $assigned)) {
                    continue;
                }
                $this->createAssignment($soldier, $duty, $date);
            }
        }
    }




    /** ✅ Regular duties */
    protected function assignRegularDuty(Duty $duty, string $date)
    {
        $rankIds = $duty->ranks->pluck('id');
        $manpower = $duty->ranks->pluck('manpower');

        $eligible = Soldier::whereIn('rank_id', $rankIds)
            ->where('status', 'active')
            ->where('is_sick', false)
            ->get();

        $eligible = $eligible->filter(
            fn($s) =>
            !$this->hasOverlappingDuty($s->id, $date, $duty->start_time, $duty->end_time)
        );

        $selected = $eligible->shuffle()->take($manpower);

        foreach ($selected as $soldier) {
            $this->createAssignment($soldier, $duty, $date);
        }
    }

    /** 🚫 Overlap checker (handles overnight duties) */
    protected function hasOverlappingDutyInCollection(Soldier $soldier, Duty $duty, string $date, $assignedCollection): bool
    {
        $startDT = Carbon::parse($date . ' ' . $duty->start_time);
        $endDT   = Carbon::parse($date . ' ' . $duty->end_time);

        $assignments = $assignedCollection->get($soldier->id, collect());

        foreach ($assignments as $existing) {
            $exStart = Carbon::parse($existing->assigned_date . ' ' . $existing->start_time);
            $exEnd   = Carbon::parse($existing->assigned_date . ' ' . $existing->end_time);

            // Skip multi-day adjustment since not required
            if ($exStart < $endDT && $exEnd > $startDT) {
                return true;
            }
        }

        return false;
    }

    protected function createAssignment(Soldier $soldier, Duty $duty, string $date)
    {
        return SoldierDuty::firstOrCreate([
            'soldier_id'    => $soldier->id,
            'duty_id'       => $duty->id,
            'assigned_date' => $date,
        ], [
            'start_time' => $duty->start_time,
            'end_time'   => $duty->end_time,
            'status'     => 'assigned',
        ]);
    }
}
