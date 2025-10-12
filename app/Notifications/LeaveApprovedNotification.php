<?php

namespace App\Notifications;

use App\Models\LeaveApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class LeaveApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $leaveApplication;

    public function __construct(LeaveApplication $leaveApplication)
    {
        $this->leaveApplication = $leaveApplication;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        $soldierName = $this->leaveApplication->soldier->full_name . "(" . $this->leaveApplication->soldier->army_no . ")" ?? 'Unknown Soldier';

        return [
            'leave_application_id' => $this->leaveApplication->id,
            'soldier_id' => $this->leaveApplication->soldier_id,
            'soldier_name' => $soldierName,
            'start_date' => $this->leaveApplication->start_date->format('Y-m-d'),
            'end_date' => $this->leaveApplication->end_date->format('Y-m-d'),
            'leave_type' => $this->leaveApplication->leaveType->name ?? 'Unknown Type',
            'message' => "Leave application for {$soldierName} has been approved",
            'timestamp' => now(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->id, // This will use the auto-increment ID from database
            'type' => 'leave_approved',
            'data' => $this->toDatabase($notifiable),
            'read_at' => null,
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}
