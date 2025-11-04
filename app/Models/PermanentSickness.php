<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermanentSickness extends Model
{
    use HasFactory, LogsAllActivity;
    protected $table = 'permanent_sickness';

    protected $fillable = [
        'name',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * A cadre can have many soldiers through a pivot table.
     */
    public function soldiers()
    {
        return $this->belongsToMany(Soldier::class, 'soldier_permanent_sickness', 'permanent_sickness_id', 'soldier_id')
            ->withPivot(['remarks', 'start_date', 'end_date'])
            ->withTimestamps();
    }
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }
    public function getSoldiersCountAttribute(): int
    {
        return $this->soldiers()->count();
    }
}
