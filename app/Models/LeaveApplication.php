<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveApplication extends Model
{
    use HasFactory, LogsAllActivity;

    protected $table = 'soldier_leave_applications';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'soldier_id',
        'leave_type_id',
        'reason',
        'hard_copy',
        'start_date',
        'end_date',
        'application_current_status',
    ];

    /**
     * Relationships
     */

    // Soldier (profile) relationship
    public function soldier()
    {
        return $this->belongsTo(Soldier::class);
    }

    // Leave Type relationship
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
