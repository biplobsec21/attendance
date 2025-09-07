<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldierDuty extends Model
{
    use HasFactory;

    protected $fillable = [
        'soldier_id',
        'duty_id',
        'assigned_date',
        'start_time',
        'end_time',
        'status',
        'remarks'
    ];

    public function soldier()
    {
        return $this->belongsTo(Soldier::class);
    }

    public function duty()
    {
        return $this->belongsTo(Duty::class);
    }
}
