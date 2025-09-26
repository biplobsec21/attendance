<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class SoldierCadre extends Model
{
    use HasFactory, LogsAllActivity;

    protected $table = 'soldier_cadres';

    protected $fillable = [
        'soldier_id',
        'cadre_id',
        'remarks',
        'result',
        'completion_date',
        'start_date',
        'end_date',
        'course_status',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'completion_date' => 'date',
    ];

    /**
     * Get the soldier that owns the soldier cadre relationship.
     */
    public function soldier(): BelongsTo
    {
        return $this->belongsTo(Soldier::class);
    }

    /**
     * Get the cadre that owns the soldier cadre relationship.
     */
    public function cadre(): BelongsTo
    {
        return $this->belongsTo(Cadre::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Update the status based on current date and date ranges.
     */
    public function updateStatus()
    {
        $today = Carbon::today();

        if (is_null($this->start_date)) {
            $this->status = 'scheduled';
            return $this->save();
        }

        // If end_date exists and is in the past
        if ($this->end_date && $this->end_date->lt($today)) {
            $this->status = 'completed';
        }
        // If start_date is today or in the past and (end_date is null or in the future)
        elseif ($this->start_date->lte($today) && (!$this->end_date || $this->end_date->gte($today))) {
            $this->status = 'active';
        }
        // Otherwise, it's scheduled for the future
        else {
            $this->status = 'scheduled';
        }

        return $this->save();
    }

    /**
     * Check if the cadre is active on a specific date.
     */
    public function isActiveOnDate($date)
    {
        $date = Carbon::parse($date);
        $fromDate = $this->start_date;
        $toDate = $this->end_date;

        if (is_null($fromDate)) {
            return false;
        }

        return $date->gte($fromDate) && (!$toDate || $date->lte($toDate));
    }
}
