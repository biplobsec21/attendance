<?php

// app/Models/Duty.php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Duty extends Model
{
    use HasFactory, LogsAllActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // Add all the form fields here to allow them to be saved
    protected $fillable = [
        'duty_name',
        'start_time',
        'end_time',
        // 'manpower',
        'remark',
        'status',
    ];
    public function ranks()
    {
        return $this->belongsToMany(Rank::class, 'duty_rank')
            ->withPivot('duty_type', 'priority', 'rotation_days', 'remarks', 'manpower')
            ->withTimestamps();
    }
    public function dutyRanks()
    {
        return $this->hasMany(DutyRank::class);
    }
}
