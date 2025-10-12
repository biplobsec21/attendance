<?php

namespace App\Console\Commands;

use App\Events\LeaveCompleted;
use App\Models\LeaveApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CheckLeaveCompletion extends Command
{
    protected $signature = 'leaves:check-completion {--force : Force check even if already checked today}';
    protected $description = 'Check for completed leaves and send notifications';

    public function handle()
    {
        $force = $this->option('force');
        $today = now()->toDateString();

        if (!$force) {
            $lastChecked = Cache::get('last_leave_completion_check');
            if ($lastChecked && $lastChecked->gte(now()->startOfDay())) {
                $this->info('Leave completion was already checked today. Use --force to check again.');
                return;
            }
        }

        $this->info("Checking for leaves completed on: {$today}");

        $completedLeaves = LeaveApplication::approved()
            ->whereDate('end_date', $today)
            ->get();

        $this->info("Found {$completedLeaves->count()} completed leaves");

        foreach ($completedLeaves as $leave) {
            event(new LeaveCompleted($leave));
            $this->line("Notified for completed leave: Soldier ID {$leave->soldier_id} - {$leave->soldier->name}");
        }

        Cache::put('last_leave_completion_check', now(), now()->addDay());
        $this->info('Leave completion check completed successfully.');
    }
}
