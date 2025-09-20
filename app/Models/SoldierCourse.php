<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoldierCourse extends Model
{
    use HasFactory, LogsAllActivity;

    protected $table = 'soldier_courses';

    protected $fillable = [
        'soldier_id',
        'course_id',
        'remarks',
        'result',
        'completion_date',
        'start_date',
        'end_date',
        'course_status',
    ];

    /**
     * Get the soldier that owns the soldier cadre relationship.
     */
    public function soldier(): BelongsTo
    {
        return $this->belongsTo(Soldier::class);
    }

    /**
     * Get the cadre that owns the soldier cadre relationship.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Cadre::class);
    }
}
