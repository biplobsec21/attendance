<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use App\Notifications\LeaveApprovedNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveApplication extends Model
{
    use HasFactory, LogsAllActivity;

    protected $table = 'soldier_leave_applications';

    protected $fillable = [
        'soldier_id',
        'leave_type_id',
        'reason',
        'hard_copy',
        'start_date',
        'end_date',
        'application_current_status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::updated(function ($leaveApplication) {
            // Check if status was changed to approved
            if (
                $leaveApplication->isDirty('application_current_status') &&
                $leaveApplication->application_current_status === 'approved'
            ) {

                // Notify users directly
                $users = \App\Models\User::get();
                foreach ($users as $user) {
                    $user->notify(new LeaveApprovedNotification($leaveApplication));
                }
            }

            // Check if leave was just completed (end_date is today and status is approved)
            if (
                $leaveApplication->isDirty('end_date') &&
                $leaveApplication->end_date->isToday() &&
                $leaveApplication->application_current_status === 'approved'
            ) {

                // Trigger leave completed event
                event(new \App\Events\LeaveCompleted($leaveApplication));
            }
        });
    }

    /**
     * Relationships
     */
    public function soldier()
    {
        return $this->belongsTo(Soldier::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    /**
     * Scopes
     */
    public function scopeApproved($query)
    {
        return $query->where('application_current_status', 'approved');
    }

    public function scopeCurrent($query)
    {
        $today = now()->toDateString();
        return $query->approved()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);
    }
}
