<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SoldierServices extends Model
{
    use HasFactory, LogsAllActivity;

    protected $fillable = [
        'soldier_id',
        'appointments_name',
        'appointment_type', // current // previous
        'appointment_id',
        'appointments_from_date',
        'appointments_to_date',
    ];



    public function soldier()
    {
        return $this->belongsTo(Soldier::class, 'soldier_id');
    }
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
    /** 🔹 Scope for Current Appointments */
    public function scopeCurrent($query)
    {
        return $query->where('appointment_type', 'current');
    }

    /** 🔹 Scope for Previous Appointments */
    public function scopePrevious($query)
    {
        return $query->where('appointment_type', 'previous');
    }
}
