<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Add this import
class SoldierExArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'soldier_id',
        'ex_area_id',
        'start_date',
        'end_date',
        'remarks',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function scopeActive(Builder $query)
    {
        // Check if status column exists
        if (Schema::hasColumn('soldier_ex_areas', 'status')) {
            return $query->whereIn('status', ['active', 'scheduled']);
        } else {
            // Fallback: use date-based filtering
            return $query->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            })->where('start_date', '<=', now()->toDateString());
        }
    }

    public function soldier()
    {
        return $this->belongsTo(Soldier::class);
    }

    public function exArea()
    {
        return $this->belongsTo(ExArea::class);
    }
    public function recommendation(): BelongsTo
    {
        return $this->belongsTo(InstructionRecomendation::class, 'recommendation_id');
    }
    public function updateStatus()
    {
        // Only update status if the column exists
        if (!Schema::hasColumn('soldier_ex_areas', 'status')) {
            return;
        }

        $today = Carbon::today();

        if ($this->end_date && $this->end_date->lessThan($today)) {
            $this->status = 'completed';
        } elseif ($this->start_date->lessThanOrEqualTo($today) && (!$this->end_date || $this->end_date->greaterThanOrEqualTo($today))) {
            $this->status = 'active';
        } else {
            $this->status = 'scheduled';
        }

        $this->save();
    }
}
