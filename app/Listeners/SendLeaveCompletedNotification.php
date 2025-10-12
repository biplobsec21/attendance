<?php

namespace App\Listeners;

use App\Events\LeaveCompleted;
use App\Notifications\LeaveCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendLeaveCompletedNotification implements ShouldQueue
{
    public function handle(LeaveCompleted $event)
    {
        // Notify relevant users
        $users = \App\Models\User::get();

        foreach ($users as $user) {
            $user->notify(new LeaveCompletedNotification($event->leaveApplication));
        }
    }
}
