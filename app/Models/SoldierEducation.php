<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoldierEducation extends Model
{
    use HasFactory;

    protected $table = 'soldier_educations';

    protected $fillable = [
        'soldier_id',
        'education_id',
        'remarks',
        'result',
        'passing_year'
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
    public function education(): BelongsTo
    {
        return $this->belongsTo(Education::class);
    }
}
