<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Designation extends Model
{
    use HasFactory, LogsAllActivity;

    protected $fillable = [
        'name',
    ];

    public function soldiers(): HasMany
    {
        return $this->hasMany(Soldier::class);
    }
}
