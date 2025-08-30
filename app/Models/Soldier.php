<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Soldier extends Model
{
    use HasFactory;
    protected $table = 'soldiers';

    protected $fillable = [
        'image',
        'army_no',
        'full_name',
        'rank_id',
        'company_id',
        'mobile',
        'gender',
        'blood_group',
        'marital_status',
        'num_boys',
        'num_girls',
        'village',
        'district_id',
        'permanent_address',
        'status',
        'current_duty_status',
        'personal_completed',
        'service_completed',
        'qualifications_completed',
        'medical_completed',

    ];
    protected $casts = [
        'status' => 'boolean',
        'personal_completed' => 'boolean',
        'service_completed' => 'boolean',
        'medical_completed' => 'boolean',
        'qualifications_completed' => 'boolean',
        'num_boys' => 'integer',
        'num_girls' => 'integer',
    ];

    // ✅ Relationship with district
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    // ✅ Accessors
    public function getFullChildrenCountAttribute()
    {
        return $this->num_boys + $this->num_girls;
    }

    public function getStatusTextAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class);
    }
    // public function district(): BelongsTo
    // {
    //     return $this->belongsTo(District::class);
    // }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'soldier_courses')
            ->withPivot(['completion_date', 'remarks'])
            ->withTimestamps();
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'soldier_skills')
            ->withPivot(['proficiency_level'])
            ->withTimestamps();
    }

    public function cadres(): BelongsToMany
    {
        return $this->belongsToMany(Cadre::class, 'soldier_cadres')
            ->withPivot(['remarks', 'result'])
            ->withTimestamps();
    }
}
