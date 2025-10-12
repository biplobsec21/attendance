<?php

namespace App\Http\Middleware;

use App\Events\LeaveCompleted;
use App\Models\LeaveApplication;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CheckLeaveCompletion
{
    public function handle(Request $request, Closure $next)
    {
        // Only check for authenticated users
        if (auth()->check()) {
            $this->checkCompletedLeaves();
        }

        return $next($request);
    }

    protected function checkCompletedLeaves()
    {
        // Use cache to prevent checking on every request - check once per day
        $lastChecked = Cache::get('last_leave_completion_check', now()->subDay());
        // dd($lastChecked->lt(now()->startOfDay()));
        if ($lastChecked->lt(now()->startOfDay())) {
            $today = now()->toDateString();
            // dd($today);
            // Find leaves that ended yesterday (completed today)
            $completedLeaves = LeaveApplication::approved()
                ->whereDate('end_date', $today)
                ->get();

            foreach ($completedLeaves as $leave) {
                event(new LeaveCompleted($leave));
            }

            // Update last checked timestamp
            Cache::put('last_leave_completion_check', now(), now()->addDay());
        }
    }
}
