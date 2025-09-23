<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

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
        'status',
        'note'
    ];

    protected $dates = ['appointments_from_date', 'appointments_to_date'];
    protected $casts = [
        'appointments_from_date' => 'date',
        'appointments_to_date' => 'date',
    ];
    // Scope for active appointments
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for completed appointments
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    // Automatically update status based on dates
    public function updateStatus()
    {
        $today = Carbon::today();

        if ($this->appointments_to_date && $this->appointments_to_date->lt($today)) {
            $this->status = 'completed';
        } elseif ($this->appointments_from_date->lte($today)) {
            $this->status = 'active';
        } else {
            $this->status = 'scheduled';
        }

        $this->save();
    }
    // Scope for scheduled appointments
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

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

    // Check if appointment is active on a specific date
    public function isActiveOnDate($date)
    {
        $date = Carbon::parse($date);
        $fromDate = $this->appointments_from_date;
        $toDate = $this->appointments_to_date;

        return $date->gte($fromDate) && (!$toDate || $date->lte($toDate));
    }
}
