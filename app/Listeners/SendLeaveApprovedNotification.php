<?php

namespace App\Listeners;

use App\Events\LeaveApproved;
use App\Notifications\LeaveApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;

class SendLeaveApprovedNotification implements ShouldQueue
{
    public function handle(LeaveApproved $event)
    {
        // Notify relevant users - adjust the query based on your user roles
        // Example: notify all active users or users with specific roles
        $users = \App\Models\User::get();

        // Alternative: If you want to notify based on roles, use:
        // $users = \App\Models\User::whereIn('role', ['admin', 'commander', 'supervisor'])->get();

        foreach ($users as $user) {
            $user->notify(new LeaveApprovedNotification($event->leaveApplication));
        }
    }
}
