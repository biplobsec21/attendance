<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DutyRankTableSeeder extends Seeder
{
    public function run(): void
    {
        $ranks = DB::table('ranks')->pluck('id')->toArray();
        $duties = DB::table('duties')->pluck('id')->toArray();

        // Track which duties have already been assigned as fixed
        $fixedAssigned = [];

        foreach ($duties as $dutyId) {
            // 🎯 Pick up to 2 random ranks for this duty
            $selectedRanks = collect($ranks)->shuffle()->take(rand(1, 2));

            foreach ($selectedRanks as $rankId) {
                $assignFixed = false;
                $fixedSoldierId = null;
                $manpower = rand(2, 5); // default manpower for roster duty

                // Only assign fixed if this duty has not yet been assigned as fixed
                if (!in_array($dutyId, $fixedAssigned)) {
                    // Pick one active soldier of this rank
                    $soldierId = DB::table('soldiers')
                        ->where('rank_id', $rankId)
                        ->where('status', 'active')
                        ->where('is_sick', false)
                        ->where('is_leave', false)
                        ->inRandomOrder()
                        ->value('id');

                    // Check if this soldier already has a fixed duty
                    $exists = DB::table('duty_rank')
                        ->where('rank_id', $rankId)
                        ->where('fixed_soldier_id', $soldierId)
                        ->where('duty_type', 'fixed')
                        ->exists();

                    if ($soldierId && !$exists) {
                        // Assign this duty as fixed
                        $assignFixed = true;
                        $fixedAssigned[] = $dutyId;
                        $fixedSoldierId = $soldierId;
                        $manpower = 1; // fixed duty always has manpower = 1
                    }
                }

                // Set duty type
                $dutyType = $assignFixed ? 'fixed' : 'roster';

                // Random priority (1-3)
                $priority = rand(1, 3);

                DB::table('duty_rank')->insert([
                    'duty_id' => $dutyId,
                    'rank_id' => $rankId,
                    'duty_type' => $dutyType,
                    'priority' => $priority,
                    'manpower' => $manpower,
                    'rotation_days' => null,
                    'remarks' => $dutyType === 'fixed' ? 'Mandatory for this rank' : null,
                    'fixed_soldier_id' => $fixedSoldierId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
