<?php

namespace App\Events;

use App\Models\LeaveApplication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leaveApplication;

    public function __construct(LeaveApplication $leaveApplication)
    {
        $this->leaveApplication = $leaveApplication;
    }
}
