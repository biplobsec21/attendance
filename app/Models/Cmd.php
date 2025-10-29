<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cmd extends Model
{
    use HasFactory, LogsAllActivity;

    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    public function getStatusTextAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
    }
    // // Relationships
    // public function soldiers()
    // {
    //     return $this->belongsToMany(Soldier::class, 'soldiers_cmds')
    //         ->withPivot(['remarks', 'start_date', 'end_date'])
    //         ->withTimestamps();
    // }
    /**
     * Relationship with soldiers
     */
    public function soldiers()
    {
        return $this->belongsToMany(Soldier::class, 'soldiers_cmds', 'cmd_id', 'soldier_id')
            ->withPivot(['start_date', 'end_date', 'remarks'])
            ->withTimestamps();
    }
}
