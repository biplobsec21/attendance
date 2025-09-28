<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoldierDuty extends Model
{
    use HasFactory, LogsAllActivity;

    protected $table = 'soldier_duties';

    protected $fillable = [
        'soldier_id',
        'duty_id',
        'assigned_date',
        'start_time',
        'end_time',
        'status',
        'remarks',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Get the soldier that owns the duty assignment.
     */
    public function soldier(): BelongsTo
    {
        return $this->belongsTo(Soldier::class);
    }

    /**
     * Get the duty that owns the duty assignment.
     */
    public function duty(): BelongsTo
    {
        return $this->belongsTo(Duty::class);
    }

    /**
     * Scope to get only assigned duties.
     */
    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    /**
     * Scope to get duties for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('assigned_date', $date);
    }
}
