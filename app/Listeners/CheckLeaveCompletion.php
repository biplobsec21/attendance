<?php

namespace App\Listeners;

use App\Events\LeaveCompleted;
use App\Models\LeaveApplication;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckLeaveCompletion implements ShouldQueue
{
    public function handle($event = null)
    {
        $today = now()->toDateString();

        // Find leaves that ended yesterday (completed today)
        $completedLeaves = LeaveApplication::approved()
            ->whereDate('end_date', $today)
            ->get();

        foreach ($completedLeaves as $leave) {
            event(new LeaveCompleted($leave));
        }
    }
}
