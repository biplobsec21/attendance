<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermanentSickness extends Model
{
    use HasFactory, LogsAllActivity;
    protected $table = 'permanent_sickness';

    protected $fillable = [
        'name',
    ];

    /**
     * A cadre can have many soldiers through a pivot table.
     */
    public function soldiers(): BelongsToMany
    {
        return $this->belongsToMany(Soldier::class, 'soldier_permanent_sickness');
    }
}
