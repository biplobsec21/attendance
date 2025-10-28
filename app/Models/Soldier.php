<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\LogsAllActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Soldier extends Model
{
    use HasFactory, LogsAllActivity, Notifiable;
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
        'family_mobile_1',
        'family_mobile_2',
        'living_type',
        'living_address',
        'no_of_brothers',
        'no_of_sisters',
        'notes',
    ];

    protected $casts = [
        'status' => 'boolean',
        'personal_completed' => 'boolean',
        'service_completed' => 'boolean',
        'medical_completed' => 'boolean',
        'qualifications_completed' => 'boolean',
        'num_boys' => 'integer',
        'num_girls' => 'integer',
        'no_of_brothers' => 'integer', // ADD THIS
        'no_of_sisters' => 'integer',  // ADD THIS
    ];

    // Add computed attribute automatically
    protected $appends = ['is_on_leave', 'current_leave_details'];

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

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'soldier_courses')
            ->withPivot(['completion_date', 'start_date', 'end_date', 'remarks', 'course_status', 'result'])
            ->withTimestamps();
    }

    // New relationship for active courses
    public function activeCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'soldier_courses')
            ->withPivot(['completion_date', 'start_date', 'end_date', 'remarks', 'course_status', 'result', 'status'])
            ->wherePivotIn('status', ['active', 'scheduled'])
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
            ->withPivot(['completion_date', 'start_date', 'end_date', 'remarks', 'course_status', 'result'])
            ->withTimestamps();
    }

    // New relationship for active cadres
    public function activeCadres(): BelongsToMany
    {
        return $this->belongsToMany(Cadre::class, 'soldier_cadres')
            ->withPivot(['completion_date', 'start_date', 'end_date', 'remarks', 'course_status', 'result', 'status'])
            ->wherePivotIn('status', ['active', 'scheduled'])
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
            ->withPivot(['remarks', 'result', 'passing_year'])
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

    public function leaveApplications(): HasMany
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function currentLeaveApplications(): HasMany
    {
        $today = Carbon::today()->toDateString();
        return $this->leaveApplications()
            ->where('application_current_status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);
    }

    // Computed attributes
    public function getIsOnLeaveAttribute(): bool
    {
        if ($this->relationLoaded('current_leave_applications_count')) {
            return $this->current_leave_applications_count > 0;
        }

        return $this->currentLeaveApplications()->exists();
    }

    public function getCurrentLeaveDetailsAttribute(): ?array
    {
        $leave = $this->currentLeaveApplications()->with('leaveType')->first();

        if (!$leave) return null;

        return [
            'leave_type' => optional($leave->leaveType)->name,
            'reason'     => $leave->reason,
            'start_date' => $leave->start_date->toDateString(),
            'end_date'   => $leave->end_date->toDateString(),
        ];
    }

    // Scopes
    public function scopeOnLeave($query)
    {
        return $query->whereHas('currentLeaveApplications');
    }

    public function scopeNotOnLeave($query)
    {
        return $query->whereDoesntHave('currentLeaveApplications');
    }

    // New method to check if soldier has active assignments
    // New method to check if soldier has active assignments
    public function hasActiveAssignments(): bool
    {
        return $this->activeCourses()->exists() ||
            $this->activeCadres()->exists() ||
            $this->hasActiveServices();
    }
    // Helper method to check if soldier has active services
    public function hasActiveServices(): bool
    {
        $today = Carbon::today();

        return $this->services()
            ->where(function ($query) use ($today) {
                $query->whereNull('appointments_to_date')
                    ->orWhere('appointments_to_date', '>=', $today);
            })
            ->where('appointments_from_date', '<=', $today)
            ->where('status', '!=', 'completed')
            ->exists();
    }
    // Add this method to the Soldier model

    public function activeServices()
    {
        $today = Carbon::today();

        return $this->services()
            ->where(function ($query) use ($today) {
                $query->whereNull('appointments_to_date')
                    ->orWhere('appointments_to_date', '>=', $today);
            })
            ->where('appointments_from_date', '<=', $today);
    }
    // New method to get all active assignments
    // New method to get all active assignments
    public function getActiveAssignments(): array
    {
        $assignments = [];
        $today = Carbon::today();

        // Get active courses
        $activeCourses = $this->activeCourses()->get();
        foreach ($activeCourses as $course) {
            $assignments[] = [
                'type' => 'course',
                'name' => $course->name,
                'start_date' => $course->pivot->start_date,
                'end_date' => $course->pivot->end_date,
                'status' => $course->pivot->status,
            ];
        }

        // Get active cadres
        $activeCadres = $this->activeCadres()->get();
        foreach ($activeCadres as $cadre) {
            $assignments[] = [
                'type' => 'cadre',
                'name' => $cadre->name,
                'start_date' => $cadre->pivot->start_date,
                'end_date' => $cadre->pivot->end_date,
                'status' => $cadre->pivot->status,
            ];
        }

        // Get active services
        $activeServices = $this->services()
            ->where(function ($query) use ($today) {
                $query->whereNull('appointments_to_date')
                    ->orWhere('appointments_to_date', '>=', $today);
            })
            ->where('appointments_from_date', '<=', $today)
            ->get();

        foreach ($activeServices as $service) {
            $assignments[] = [
                'type' => 'service',
                'name' => $service->appointments_name ?? 'Service Assignment',
                'start_date' => $service->appointments_from_date,
                'end_date' => $service->appointments_to_date,
                'status' => 'active',
            ];
        }

        // Get active fixed duty assignments with detailed information
        $activeFixedDuties = $this->dutyRanks()
            ->where('assignment_type', 'fixed')
            ->whereHas('duty', function ($query) {
                $query->where('status', 'Active');
            })
            ->with(['duty' => function ($query) {
                $query->select('id', 'duty_name', 'start_time', 'end_time', 'duration_days', 'status');
            }])
            ->get();

        foreach ($activeFixedDuties as $dutyAssignment) {
            $duty = $dutyAssignment->duty;

            // Calculate total hours for display
            $start = Carbon::createFromTimeString($duty->start_time);
            $end = Carbon::createFromTimeString($duty->end_time);
            if ($end->lt($start)) {
                $end->addDay();
            }
            $dailyHours = $end->diffInHours($start);
            $totalHours = $dailyHours * $duty->duration_days;

            $assignments[] = [
                'type' => 'fixed_duty',
                'name' => $duty->duty_name,
                'duty_name' => $duty->duty_name,
                'start_time' => $duty->start_time,
                'end_time' => $duty->end_time,
                'duration_days' => $duty->duration_days,
                'daily_hours' => $dailyHours,
                'total_hours' => $totalHours,
                'priority' => $dutyAssignment->priority,
                'remarks' => $dutyAssignment->remarks,
                'duty_id' => $duty->id,
                'assignment_id' => $dutyAssignment->id,
                'status' => 'active',
                'schedule' => $this->getDutyScheduleDescription($duty),
            ];
        }

        return $assignments;
    }
    public function dutyRanks(): HasMany
    {
        return $this->hasMany(DutyRank::class, 'soldier_id');
    }
    /**
     * Relationship with duties through duty_ranks for fixed assignments
     */
    public function fixedDuties(): BelongsToMany
    {
        return $this->belongsToMany(Duty::class, 'duty_rank', 'soldier_id', 'duty_id')
            ->wherePivot('assignment_type', 'fixed')
            ->withPivot(['priority', 'remarks', 'start_time', 'end_time', 'duration_days', 'assignment_type'])
            ->withTimestamps();
    }
    /**
     * Get active fixed duties
     */
    public function activeFixedDuties(): BelongsToMany
    {
        return $this->fixedDuties()
            ->where('duties.status', 'Active');
    }
    /**
     * Helper method to generate duty schedule description
     */
    protected function getDutyScheduleDescription($duty): string
    {
        if (!$duty) {
            return 'Unknown schedule';
        }

        $description = $duty->start_time . ' - ' . $duty->end_time;

        // Check if it's an overnight duty
        try {
            $start = Carbon::createFromTimeString($duty->start_time);
            $end = Carbon::createFromTimeString($duty->end_time);
            if ($end->lt($start)) {
                $description .= ' (overnight)';
            }
        } catch (\Exception $e) {
            // Handle invalid time format gracefully
        }

        if ($duty->duration_days > 1) {
            $description .= ' for ' . $duty->duration_days . ' days';
        }

        return $description;
    }
    /**
     * Check if soldier has any active fixed duty assignments
     */
    public function hasActiveFixedDuties(): bool
    {
        return $this->dutyRanks()
            ->where('assignment_type', 'fixed')
            ->whereHas('duty', function ($query) {
                $query->where('status', 'Active');
            })
            ->exists();
    }
    /**
     * Get count of active fixed duties
     */
    public function getActiveFixedDutiesCount(): int
    {
        return $this->dutyRanks()
            ->where('assignment_type', 'fixed')
            ->whereHas('duty', function ($query) {
                $query->where('status', 'Active');
            })
            ->count();
    }
    /**
     * Get only fixed duty assignments
     */
    public function getFixedDutyAssignments(): array
    {
        return collect($this->getActiveAssignments())
            ->where('type', 'fixed_duty')
            ->values()
            ->toArray();
    }
    public function hasCompletedAssignmentsToday(): bool
    {
        $today = Carbon::today();

        // Check courses completed today
        $coursesCompletedToday = $this->courses()
            ->wherePivot('end_date', $today)
            ->exists();

        // Check cadres completed today
        $cadresCompletedToday = $this->cadres()
            ->wherePivot('end_date', $today)
            ->exists();

        return $coursesCompletedToday || $cadresCompletedToday;
    }

    public function hasEreRecords(): bool
    {
        return $this->ere()->exists();
    }
    /**
     * Scope to get soldiers without ERE records
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutEre($query)
    {
        return $query->whereDoesntHave('ere');
    }
    /**
     * Scope to get soldiers with ERE records
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithEre($query)
    {
        return $query->whereHas('ere');
    }
}
