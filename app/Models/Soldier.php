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
use Illuminate\Support\Facades\Schema;

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

    // public function att(): BelongsToMany
    // {
    //     return $this->belongsToMany(Atts::class, 'soldiers_att')
    //         ->withPivot(['start_date', 'end_date', 'remarks'])
    //         ->withTimestamps();
    // }
    public function att(): BelongsToMany
    {
        return $this->belongsToMany(Atts::class, 'soldiers_att', 'soldier_id', 'atts_id')
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
    public function hasActiveAssignments()
    {
        $hasActiveCourses = $this->activeCourses()->exists();
        $hasActiveCadres = $this->activeCadres()->exists();
        $hasActiveExAreas = $this->activeExAreas()->exists();
        $hasActiveServices = $this->activeServices()->exists();

        return $hasActiveCourses || $hasActiveCadres || $hasActiveExAreas || $hasActiveServices;
    }
    public function getActiveAssignmentsCount()
    {
        return $this->activeServices()->count();
    }
    // New method to check if soldier can take more appointments
    public function canTakeMoreAppointments($maxAllowed = null)
    {
        if ($maxAllowed === null) {
            return true; // No limit
        }

        return $this->getActiveAssignmentsCount() < $maxAllowed;
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
    public function hasCompletedAssignmentsToday()
    {
        $today = now()->toDateString();

        $completedCoursesToday = $this->courses()
            ->whereDate('end_date', $today)
            ->exists();

        $completedCadresToday = $this->cadres()
            ->whereDate('end_date', $today)
            ->exists();

        $completedExAreasToday = $this->exAreas()
            ->whereDate('end_date', $today)
            ->exists();

        return $completedCoursesToday || $completedCadresToday || $completedExAreasToday;
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

    // In App\Models\Soldier

    /**
     * Get active ATT records
     */
    public function activeAtt(): BelongsToMany
    {
        return $this->att()
            ->wherePivot(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', Carbon::today());
            });
    }

    /**
     * Check if soldier has active ATT records
     */
    public function hasActiveAtt(): bool
    {
        return $this->activeAtt()->exists();
    }

    /**
     * Get ATT history with formatted data
     */
    public function getAttHistory(): array
    {
        return $this->att()
            ->orderBy('pivot_start_date', 'desc')
            ->get()
            ->map(function ($att) {
                return [
                    'id' => $att->id,
                    'name' => $att->name,
                    'type' => $att->type ?? 'Annual Training',
                    'start_date' => $att->pivot->start_date,
                    'end_date' => $att->pivot->end_date,
                    'remarks' => $att->pivot->remarks,
                    'status' => $this->getAttStatus($att->pivot->start_date, $att->pivot->end_date),
                    'is_active' => $this->isAttActive($att->pivot->start_date, $att->pivot->end_date),
                    'duration_days' => $this->calculateAttDuration($att->pivot->start_date, $att->pivot->end_date),
                ];
            })
            ->toArray();
    }

    /**
     * Calculate ATT status
     */
    protected function getAttStatus($startDate, $endDate): string
    {
        $today = Carbon::today();
        $start = Carbon::parse($startDate);
        $end = $endDate ? Carbon::parse($endDate) : null;

        if (!$end || $end->isFuture()) {
            return 'active';
        }

        if ($end->isPast()) {
            return 'completed';
        }

        return 'scheduled';
    }

    /**
     * Check if ATT is active
     */
    protected function isAttActive($startDate, $endDate): bool
    {
        $today = Carbon::today();
        $start = Carbon::parse($startDate);
        $end = $endDate ? Carbon::parse($endDate) : null;

        return $start->lte($today) && (!$end || $end->gte($today));
    }

    /**
     * Calculate ATT duration in days
     */
    protected function calculateAttDuration($startDate, $endDate): int
    {
        if (!$endDate) {
            return 0;
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        return $start->diffInDays($end) + 1; // Inclusive of both start and end dates
    }
    // In App\Models\Soldier

    /**
     * CMD (Command) relationship
     */
    public function cmds(): BelongsToMany
    {
        return $this->belongsToMany(Cmd::class, 'soldiers_cmds', 'soldier_id', 'cmd_id')
            ->withPivot(['start_date', 'end_date', 'remarks'])
            ->withTimestamps();
    }

    /**
     * Get active CMD records
     */
    public function activeCmds(): BelongsToMany
    {
        return $this->cmds()
            ->wherePivot(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', Carbon::today());
            });
    }

    /**
     * Check if soldier has active CMD records
     */
    public function hasActiveCmds(): bool
    {
        return $this->activeCmds()->exists();
    }

    /**
     * Get CMD history with formatted data
     */
    public function getCmdHistory(): array
    {
        return $this->cmds()
            ->orderBy('pivot_start_date', 'desc')
            ->get()
            ->map(function ($cmd) {
                return [
                    'id' => $cmd->id,
                    'name' => $cmd->name,
                    'type' => 'Command',
                    'start_date' => $cmd->pivot->start_date,
                    'end_date' => $cmd->pivot->end_date,
                    'remarks' => $cmd->pivot->remarks,
                    'status' => $this->getCmdStatus($cmd->pivot->start_date, $cmd->pivot->end_date),
                    'is_active' => $this->isCmdActive($cmd->pivot->start_date, $cmd->pivot->end_date),
                    'duration_days' => $this->calculateCmdDuration($cmd->pivot->start_date, $cmd->pivot->end_date),
                ];
            })
            ->toArray();
    }

    /**
     * Calculate CMD status
     */
    protected function getCmdStatus($startDate, $endDate): string
    {
        $today = Carbon::today();
        $start = Carbon::parse($startDate);
        $end = $endDate ? Carbon::parse($endDate) : null;

        if (!$end || $end->isFuture()) {
            return 'active';
        }

        if ($end->isPast()) {
            return 'completed';
        }

        return 'scheduled';
    }

    /**
     * Check if CMD is active
     */
    protected function isCmdActive($startDate, $endDate): bool
    {
        $today = Carbon::today();
        $start = Carbon::parse($startDate);
        $end = $endDate ? Carbon::parse($endDate) : null;

        return $start->lte($today) && (!$end || $end->gte($today));
    }

    /**
     * Calculate CMD duration in days
     */
    protected function calculateCmdDuration($startDate, $endDate): int
    {
        if (!$endDate) {
            return 0;
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        return $start->diffInDays($end) + 1;
    }

    public function exAreas()
    {
        return $this->hasMany(SoldierExArea::class);
    }
    public function exAreas2(): BelongsToMany
    {
        return $this->belongsToMany(ExArea::class, 'soldier_ex_areas', 'soldier_id', 'ex_area_id')
            ->withPivot(['start_date', 'end_date', 'status', 'remarks'])
            ->withTimestamps();
    }


    public function activeExAreas()
    {
        // Check if status column exists
        if (Schema::hasColumn('soldier_ex_areas', 'status')) {
            return $this->hasMany(SoldierExArea::class)->whereIn('status', ['active', 'scheduled']);
        } else {
            // Fallback: use date-based filtering if status column doesn't exist
            return $this->hasMany(SoldierExArea::class)
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', now()->toDateString());
                })
                ->where('start_date', '<=', now()->toDateString());
        }
    }

    public function previousExAreas()
    {
        return $this->hasMany(SoldierExArea::class)->where('status', 'completed');
    }
    public function absents(): HasMany
    {
        return $this->hasMany(Absent::class, 'soldier_id');
    }
    // ------- ------------------ New Methods for Soldier Model ----------------- //
    // ------- ------------------ New Methods for Soldier Model ----------------- //
    // ------- ------------------ New Methods for Soldier Model ----------------- //
    // ------- ------------------ New Methods for Soldier Model ----------------- //
    // ------- ------------------ New Methods for Soldier Model ----------------- //
    /**
     * Get all active assignments for a specific date
     *
     * @param string|Carbon|null $date
     * @return array
     */
    public function getActiveAssignmentsSummary($date = null): array
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        $assignments = [];

        // Active Cadres
        $activeCadres = $this->cadres()
            ->wherePivot('status', 'active')
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->get();

        foreach ($activeCadres as $cadre) {
            $assignments[] = [
                'type' => 'cadre',
                'name' => $cadre->name,
                'start_date' => $cadre->pivot->start_date,
                'end_date' => $cadre->pivot->end_date,
                'status' => $cadre->pivot->status,
            ];
        }

        // Active Courses
        $activeCourses = $this->courses()
            ->wherePivot('status', 'active')
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->get();

        foreach ($activeCourses as $course) {
            $assignments[] = [
                'type' => 'course',
                'name' => $course->name,
                'start_date' => $course->pivot->start_date,
                'end_date' => $course->pivot->end_date,
                'status' => $course->pivot->status,
            ];
        }

        // Active Ex Areas
        $activeExAreas = $this->exAreas()
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->get();

        foreach ($activeExAreas as $exArea) {
            $assignments[] = [
                'type' => 'ex_area',
                'name' => $exArea->ex_area_id ?? 'Ex Area',
                'start_date' => $exArea->start_date,
                'end_date' => $exArea->end_date,
                'status' => $exArea->status,
            ];
        }

        // Active Services
        $activeServices = $this->services()
            ->where('status', 'active')
            ->whereDate('appointments_from_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('appointments_to_date')
                    ->orWhereDate('appointments_to_date', '>=', $date);
            })
            ->get();

        foreach ($activeServices as $service) {
            $assignments[] = [
                'type' => 'service',
                'name' => $service->appointments_name ?? 'Service',
                'start_date' => $service->appointments_from_date,
                'end_date' => $service->appointments_to_date,
                'status' => $service->status,
            ];
        }

        // Fixed Duty Assignments
        $fixedDuties = $this->dutyRanks()
            ->where('duty_type', 'fixed')
            ->whereHas('duty', function ($q) {
                $q->where('status', 'Active');
            })
            ->with('duty')
            ->get();

        foreach ($fixedDuties as $dutyRank) {
            $assignments[] = [
                'type' => 'fixed_duty',
                'name' => $dutyRank->duty->duty_name ?? 'Fixed Duty',
                'duty_details' => [
                    'start_time' => $dutyRank->duty->start_time,
                    'end_time' => $dutyRank->duty->end_time,
                    'duration_days' => $dutyRank->duty->duration_days,
                ],
                'status' => 'active',
            ];
        }

        // Active Leave
        $activeLeaves = $this->leaveApplications()
            ->where('application_current_status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->with('leaveType')
            ->get();

        foreach ($activeLeaves as $leave) {
            $assignments[] = [
                'type' => 'leave',
                'name' => $leave->leaveType->name ?? 'Leave',
                'reason' => $leave->reason,
                'start_date' => $leave->start_date,
                'end_date' => $leave->end_date,
                'status' => 'approved',
            ];
        }

        // Active CMD
        $activeCmds = $this->cmds()
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->get();

        foreach ($activeCmds as $cmd) {
            $assignments[] = [
                'type' => 'cmd',
                'name' => $cmd->name,
                'start_date' => $cmd->pivot->start_date,
                'end_date' => $cmd->pivot->end_date,
                'status' => 'active',
            ];
        }

        // Active ATT
        $activeAtts = $this->att()
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->get();

        foreach ($activeAtts as $att) {
            $assignments[] = [
                'type' => 'att',
                'name' => $att->name,
                'start_date' => $att->pivot->start_date,
                'end_date' => $att->pivot->end_date,
                'status' => 'active',
            ];
        }

        // Active ERE
        $activeEres = $this->ere()
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->get();

        foreach ($activeEres as $ere) {
            $assignments[] = [
                'type' => 'ere',
                'name' => $ere->name,
                'start_date' => $ere->pivot->start_date,
                'end_date' => $ere->pivot->end_date,
                'status' => 'active',
            ];
        }

        // Active Absents
        $activeAbsents = $this->absents()
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->get();

        foreach ($activeAbsents as $absent) {
            $assignments[] = [
                'type' => 'absent',
                'name' => 'Absent',
                'start_date' => $absent->start_date,
                'end_date' => $absent->end_date,
                'reason' => $absent->reason ?? null,
                'status' => 'approved',
            ];
        }

        return $assignments;
    }
    /**
     * Scope to get soldiers who are excluded (have active assignments)
     *
     * @param Builder $query
     * @param string|Carbon $date
     * @return Builder
     */
    public function scopeExcluded($query, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        return $query->where(function ($q) use ($date) {
            // Has active cadres
            $q->orWhereHas('cadres', function ($query) use ($date) {
                $query->wherePivot('status', 'active')
                    ->whereDate('start_date', '<=', $date)
                    ->where(function ($q) use ($date) {
                        $q->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $date);
                    });
            })
                // Has active courses
                ->orWhereHas('courses', function ($query) use ($date) {
                    $query->wherePivot('status', 'active')
                        ->whereDate('start_date', '<=', $date)
                        ->where(function ($q) use ($date) {
                            $q->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', $date);
                        });
                })
                // Has active ex areas
                ->orWhereHas('exAreas', function ($query) use ($date) {
                    $query->where('status', 'active')
                        ->whereDate('start_date', '<=', $date)
                        ->where(function ($q) use ($date) {
                            $q->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', $date);
                        });
                })
                // Has active services
                ->orWhereHas('services', function ($query) use ($date) {
                    $query->where('status', 'active')
                        ->whereDate('appointments_from_date', '<=', $date)
                        ->where(function ($q) use ($date) {
                            $q->whereNull('appointments_to_date')
                                ->orWhereDate('appointments_to_date', '>=', $date);
                        });
                })
                // Has fixed duty assignments
                ->orWhereHas('dutyRanks', function ($query) {
                    $query->where('duty_type', 'fixed')
                        ->whereHas('duty', function ($q) {
                            $q->where('status', 'Active');
                        });
                })
                // Has active leave
                ->orWhereHas('leaveApplications', function ($query) use ($date) {
                    $query->where('application_current_status', 'approved')
                        ->whereDate('start_date', '<=', $date)
                        ->where(function ($q) use ($date) {
                            $q->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', $date);
                        });
                })
                // Has active CMD
                ->orWhereHas('cmds', function ($query) use ($date) {
                    $query->whereDate('start_date', '<=', $date)
                        ->where(function ($q) use ($date) {
                            $q->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', $date);
                        });
                })
                // Has active ATT
                ->orWhereHas('att', function ($query) use ($date) {
                    $query->whereDate('start_date', '<=', $date)
                        ->where(function ($q) use ($date) {
                            $q->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', $date);
                        });
                })
                // Has active ERE
                ->orWhereHas('ere', function ($query) use ($date) {
                    $query->whereDate('start_date', '<=', $date)
                        ->where(function ($q) use ($date) {
                            $q->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', $date);
                        });
                })
                // Has active absent records
                ->orWhereHas('absents', function ($query) use ($date) {
                    $query->where('status', 'approved')
                        ->whereDate('start_date', '<=', $date)
                        ->where(function ($q) use ($date) {
                            $q->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', $date);
                        });
                });
        });
    }

    /**
     * Scope to get soldiers who are available (NOT excluded)
     */
    public function scopeAvailable($query, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        // Get excluded soldier IDs
        $excludedIds = self::excluded($date)->pluck('id')->toArray();

        // Return soldiers NOT in excluded list
        return $query->whereNotIn('id', $excludedIds);
    }

    /**
     * Load active assignments for a specific date
     */
    public function scopeWithActiveAssignments($query, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        return $query->with([
            'cadres' => function ($q) use ($date) {
                $q->wherePivot('status', 'active')
                    ->whereDate('start_date', '<=', $date)
                    ->where(function ($query) use ($date) {
                        $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $date);
                    });
            },
            'courses' => function ($q) use ($date) {
                $q->wherePivot('status', 'active')
                    ->whereDate('start_date', '<=', $date)
                    ->where(function ($query) use ($date) {
                        $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $date);
                    });
            },
            'exAreas' => function ($q) use ($date) {
                $q->where('status', 'active')
                    ->whereDate('start_date', '<=', $date)
                    ->where(function ($query) use ($date) {
                        $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $date);
                    });
            },
            'services' => function ($q) use ($date) {
                $q->where('status', 'active')
                    ->whereDate('appointments_from_date', '<=', $date)
                    ->where(function ($query) use ($date) {
                        $query->whereNull('appointments_to_date')
                            ->orWhereDate('appointments_to_date', '>=', $date);
                    });
            },
            'dutyRanks' => function ($q) {
                $q->where('duty_type', 'fixed')
                    ->whereHas('duty', function ($query) {
                        $query->where('status', 'Active');
                    });
            },
            'dutyRanks.duty',
            'leaveApplications' => function ($q) use ($date) {
                $q->where('application_current_status', 'approved')
                    ->whereDate('start_date', '<=', $date)
                    ->where(function ($query) use ($date) {
                        $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $date);
                    });
            },
            'leaveApplications.leaveType',
            'cmds' => function ($q) use ($date) {
                $q->whereDate('start_date', '<=', $date)
                    ->where(function ($query) use ($date) {
                        $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $date);
                    });
            },
            'att' => function ($q) use ($date) {
                $q->whereDate('start_date', '<=', $date)
                    ->where(function ($query) use ($date) {
                        $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $date);
                    });
            },
            'ere' => function ($q) use ($date) {
                $q->whereDate('start_date', '<=', $date)
                    ->where(function ($query) use ($date) {
                        $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $date);
                    });
            },
            'absents' => function ($q) use ($date) {
                $q->where('status', 'approved')
                    ->whereDate('start_date', '<=', $date)
                    ->where(function ($query) use ($date) {
                        $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $date);
                    });
            }
        ]);
    }
    // ------- ------------------ New Methods for Soldier Model ----------------- //
    // ------- ------------------ New Methods for Soldier Model ----------------- //
    // ------- ------------------ New Methods for Soldier Model ----------------- //
    // ------- ------------------ New Methods for Soldier Model ----------------- //
    // ------- ------------------ New Methods for Soldier Model ----------------- //
}
