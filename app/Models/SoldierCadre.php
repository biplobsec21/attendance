<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoldierCadre extends Model
{
    use HasFactory;

    protected $table = 'soldier_cadres';

    protected $fillable = [
        'soldier_id',
        'cadre_id',
        'remarks',
        'result',
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
    public function cadre(): BelongsTo
    {
        return $this->belongsTo(Cadre::class);
    }
}