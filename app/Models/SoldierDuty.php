<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoldierDuty extends Model
{
    use HasFactory, LogsAllActivity;

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
