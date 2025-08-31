<?php

// app/Models/Duty.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duty extends Model
{
    use HasFactory;

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
        'manpower',
        'remark',
        'status',
    ];
}