<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoldierCadre extends Model
{
    use HasFactory, LogsAllActivity;

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
