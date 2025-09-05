<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SoldierDiscipline extends Model
{
    use HasFactory;
    protected $table = 'soldiers_discipline';

    protected $fillable = [
        'soldier_id',
        'discipline_type',
        'discipline_name',
        'remarks',
        'start_date',
        'end_date',
    ];

    /**
     * Get the soldier that owns the soldier cadre relationship.
     */
    public function soldiers(): BelongsToMany
    {
        return $this->belongsToMany(Soldier::class, 'soldier_educations');
    }
}
