<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SoldierServices extends Model
{
    use HasFactory;

    protected $fillable = [
        'soldier_id',
        'appointments_name',
        'appointment_type',

        'appointments_from_date',
        'appointments_to_date',
    ];



    public function soldier()
    {
        return $this->belongsTo(Soldier::class, 'soldier_id');
    }
}
