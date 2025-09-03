<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MedicalCategory extends Model
{
    use HasFactory;
    protected $table = 'medical_categories';

    protected $fillable = [
        'name',
    ];

    /**
     * A cadre can have many soldiers through a pivot table.
     */
    public function soldiers(): BelongsToMany
    {
        return $this->belongsToMany(Soldier::class, 'soldier_medical_categories');
    }
}
