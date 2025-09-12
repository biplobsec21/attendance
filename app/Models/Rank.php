<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rank extends Model
{
    use HasFactory, LogsAllActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Scope a query to only include active ranks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to only include inactive ranks.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /**
     * Get the status as a string.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    /**
     * Get the status badge class.
     *
     * @return string
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    }
    public function duties()
    {
        return $this->belongsToMany(Duty::class, 'duty_rank')
            ->withPivot('duty_type', 'priority', 'rotation_days', 'remarks')
            ->withTimestamps();
    }

    /**
     * Helper: get only fixed duties
     */
    public function fixedDuties()
    {
        return $this->duties()->wherePivot('duty_type', 'fixed');
    }

    /**
     * Helper: get only roster duties
     */
    public function rosterDuties()
    {
        return $this->duties()->wherePivot('duty_type', 'roster');
    }

    /**
     * Helper: get only regular duties
     */
    public function regularDuties()
    {
        return $this->duties()->wherePivot('duty_type', 'regular');
    }
    public function dutyRanks()
    {
        return $this->hasMany(DutyRank::class);
    }
}
