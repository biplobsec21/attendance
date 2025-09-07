<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DutyRank extends Model
{
    use HasFactory;

    protected $table = 'duty_rank';

    protected $fillable = [
        'duty_id',
        'rank_id',
        'duty_type',
        'priority',
        'rotation_days',
        'remarks',
        'manpower',
        'fixed_soldier_id'
    ];

    // Relationships
    public function duty()
    {
        return $this->belongsTo(Duty::class);
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }
    public function soldier()
    {
        return $this->belongsTo(Soldier::class, 'fixed_soldier_id');
    }
}
