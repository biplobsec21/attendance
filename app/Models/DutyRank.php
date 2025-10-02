<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DutyRank extends Model
{
    use HasFactory, LogsAllActivity;

    protected $table = 'duty_rank';

    protected $fillable = [
        'duty_id',
        'rank_id',
        'soldier_id',
        'assignment_type',
        'duty_type',
        'manpower',
        'start_time',
        'end_time',
        'group_id',
        'priority',
        'rotation_days',
        'remarks'

    ];
    protected $casts = [
        'manpower' => 'integer',
        'priority' => 'integer',
        'rotation_days' => 'integer',
    ];
    // Relationships
    public function duty()
    {
        return $this->belongsTo(Duty::class);
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }
    public function soldier()
    {
        return $this->belongsTo(Soldier::class, 'soldier_id');
    }
    // Scopes
    public function scopeRosterAssignments($query)
    {
        return $query->where('assignment_type', 'roster');
    }

    public function scopeFixedAssignments($query)
    {
        return $query->where('assignment_type', 'fixed');
    }

    public function scopeForSoldier($query, $soldierId)
    {
        return $query->where('soldier_id', $soldierId);
    }

    // Accessors
    public function getAssignmentDescriptionAttribute(): string
    {
        if ($this->assignment_type === 'fixed' && $this->soldier) {
            return "Fixed: {$this->soldier->full_name} ({$this->soldier->army_no})";
        }

        return "Roster: {$this->rank->name} x {$this->manpower}";
    }

    public function getIsFixedAssignmentAttribute(): bool
    {
        return $this->assignment_type === 'fixed';
    }

    public function getIsRosterAssignmentAttribute(): bool
    {
        return $this->assignment_type === 'roster';
    }
}
