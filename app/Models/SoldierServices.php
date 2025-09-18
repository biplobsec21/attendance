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
        'appointment_type',
        'appointment_id',
        'appointments_from_date',
        'appointments_to_date',
    ];



    public function soldier()
    {
        return $this->belongsTo(Soldier::class, 'soldier_id');
    }
    public function appointments()
    {
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
