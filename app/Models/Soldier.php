<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder; // <-- Add this

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
        'joining_date',
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
    public function scopeAvailableForDuty(Builder $query)
    {
        return $query->where('is_sick', false);
    }
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
            ->withPivot(['completion_date', 'start_date', 'end_date', 'remarks', 'course_status'])
            ->withTimestamps();
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'soldier_skills')
            ->withPivot(['proficiency_level', 'remarks'])
            ->withTimestamps();
    }

    public function cadres(): BelongsToMany
    {
        return $this->belongsToMany(Cadre::class, 'soldier_cadres')
            ->withPivot(['completion_date', 'start_date', 'end_date', 'remarks', 'course_status'])

            ->withTimestamps();
    }
    public function services()
    {
        return $this->hasMany(SoldierServices::class, 'soldier_id');
    }
    public function educationsData()
    {
        return $this->hasMany(SoldierEducation::class, 'soldier_id');
    }

    public function educations(): BelongsToMany
    {

        return $this->belongsToMany(Education::class, 'soldier_educations')
            ->withPivot(['remarks', 'result', 'passing_year']) // add passing_year if needed
            ->withTimestamps();
    }

    public function ere(): BelongsToMany
    {
        return $this->belongsToMany(Eres::class, 'soldiers_ere')
            ->withPivot(['start_date', 'end_date', 'remarks'])
            ->withTimestamps();
    }
    public function att(): BelongsToMany
    {
        return $this->belongsToMany(Atts::class, 'soldiers_att')
            ->withPivot(['start_date', 'end_date', 'remarks'])
            ->withTimestamps();
    }

    public function medicalCategory(): BelongsToMany
    {
        return $this->belongsToMany(MedicalCategory::class, 'soldier_medical_categories')
            ->withPivot(['remarks', 'start_date', 'end_date', 'medical_category_id'])
            ->withTimestamps();
    }
    public function sickness(): BelongsToMany
    {
        return $this->belongsToMany(PermanentSickness::class, 'soldier_permanent_sickness')
            ->withPivot(['remarks', 'start_date', 'end_date'])
            ->withTimestamps();
    }
    public function discipline()
    {
        return $this->hasMany(SoldierDiscipline::class, 'soldier_id');
    }
    public function goodDiscipline()
    {
        return $this->hasMany(SoldierDiscipline::class, 'soldier_id')
            ->where('discipline_type', 'good');
    }

    public function punishmentDiscipline()
    {
        return $this->hasMany(SoldierDiscipline::class, 'soldier_id')
            ->where('discipline_type', 'punishment');
    }
    public function getServiceDurationAttribute()
    {
        if (!$this->joining_date) {
            return null;
        }

        $joiningDate = Carbon::parse($this->joining_date);
        $diff = $joiningDate->diff(Carbon::now());

        return sprintf('%dY %dM %dD', $diff->y, $diff->m, $diff->d);
    }
}
