<?php

namespace App\Services;

use App\Models\Soldier;
use App\Models\Rank;
use App\Models\Company;
use App\Models\Duty;
use App\Models\SoldierDuty;
use App\Models\DutyRank;
use App\Models\SoldierCourse;
use App\Models\SoldierCadre;
use App\Models\SoldierServices;
use App\Models\LeaveApplication;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GameAttendanceService
{
    protected $reportType;
    protected $excusalField;
    protected $sessionTimes;

    // Report type constants
    const REPORT_GAME = 'game';
    const REPORT_PT = 'pt';
    const REPORT_ROLL_CALL = 'roll_call';
    const REPORT_PARADE = 'parade';

    // Excusal priority constants (lower number = higher priority)
    const PRIORITY_ERE = 0;
    const PRIORITY_LEAVE = 1;
    const PRIORITY_ROSTER_DUTY = 2;
    const PRIORITY_FIXED_DUTY = 3;
    const PRIORITY_APPOINTMENT = 4;
    const PRIORITY_COURSE = 5;
    const PRIORITY_CADRE = 6;

    public function __construct($reportType = self::REPORT_GAME)
    {
        $this->reportType = $reportType;
        $this->excusalField = $this->getExcusalFieldForReportType($reportType);
        $this->sessionTimes = $this->getSessionTimesFromSettings();
    }

    /**
     * Get session times from SiteSetting
     */
    private function getSessionTimesFromSettings()
    {
        $settings = SiteSetting::getSettings();

        $sessionTimes = [
            self::REPORT_PARADE => $this->parseSessionTime($settings->parade_time),
            self::REPORT_PT => $this->parseSessionTime($settings->pt_time),
            self::REPORT_GAME => $this->parseSessionTime($settings->games_time),
            self::REPORT_ROLL_CALL => $this->parseSessionTime($settings->roll_call_time),
        ];

        // Set default duration (1 hour) if only start time is provided
        foreach ($sessionTimes as $reportType => &$time) {
            if (!isset($time['end'])) {
                $endTime = Carbon::parse($time['start'])->addHour();
                $time['end'] = $endTime->format('H:i');
            }
        }

        return $sessionTimes;
    }

    /**
     * Parse session time from settings
     */
    private function parseSessionTime($time)
    {
        if ($time instanceof Carbon) {
            $startTime = $time->format('H:i');
        } else {
            $startTime = Carbon::parse($time)->format('H:i');
        }

        return ['start' => $startTime];
    }

    /**
     * Get the excusal field name based on report type
     */
    private function getExcusalFieldForReportType($reportType)
    {
        $fieldMap = [
            self::REPORT_GAME => 'excused_next_session_games',
            self::REPORT_PT => 'excused_next_session_pt',
            self::REPORT_ROLL_CALL => 'excused_next_session_roll_call',
            self::REPORT_PARADE => 'excused_next_session_parade',
        ];

        return $fieldMap[$reportType] ?? 'excused_next_session_games';
    }

    /**
     * Get session time range for the current report type
     */
    public function getSessionTimeRange($date)
    {
        if (!isset($this->sessionTimes[$this->reportType])) {
            return null;
        }

        $carbonDate = Carbon::parse($date);
        $sessionTime = $this->sessionTimes[$this->reportType];

        $startTime = Carbon::parse($carbonDate->format('Y-m-d') . ' ' . $sessionTime['start']);
        $endTime = isset($sessionTime['end'])
            ? Carbon::parse($carbonDate->format('Y-m-d') . ' ' . $sessionTime['end'])
            : $startTime->copy()->addHour();

        return [
            'start' => $startTime,
            'end' => $endTime
        ];
    }

    /**
     * OPTIMIZED: Check if duty time overlaps with session time
     * Handles cross-midnight duties properly
     */
    private function doesDutyOverlapWithSession($soldierDuty, $sessionTimeRange)
    {
        // If duty doesn't have specific times, assume it covers the whole day
        if (!$soldierDuty->start_time || !$soldierDuty->end_time) {
            Log::debug("⏰ Duty {$soldierDuty->duty->duty_name} has no specific time - assumed full day coverage");
            return true;
        }

        $dutyAssignedDate = $soldierDuty->assigned_date;
        $dutyStart = Carbon::parse($dutyAssignedDate->format('Y-m-d') . ' ' . $soldierDuty->start_time->format('H:i:s'));
        $dutyEnd = Carbon::parse($dutyAssignedDate->format('Y-m-d') . ' ' . $soldierDuty->end_time->format('H:i:s'));

        // Handle cross-midnight duty (end time before start time)
        if ($dutyEnd->format('H:i') < $dutyStart->format('H:i')) {
            $dutyEnd->addDay();
            Log::debug("🌙 Cross-midnight duty detected: {$dutyStart->format('Y-m-d H:i')} → {$dutyEnd->format('Y-m-d H:i')}");
        }

        // Check if duty overlaps with session
        // Overlap condition: dutyStart < sessionEnd AND dutyEnd > sessionStart
        $overlaps = $dutyStart < $sessionTimeRange['end'] && $dutyEnd > $sessionTimeRange['start'];

        Log::debug("⏰ Time overlap check:");
        Log::debug("   Duty: {$dutyStart->format('Y-m-d H:i')} → {$dutyEnd->format('Y-m-d H:i')}");
        Log::debug("   Session: {$sessionTimeRange['start']->format('Y-m-d H:i')} → {$sessionTimeRange['end']->format('Y-m-d H:i')}");
        Log::debug("   Overlaps: " . ($overlaps ? 'YES ✅' : 'NO ❌'));

        return $overlaps;
    }

    /**
     * Get the primary excusal reason for a soldier (highest priority reason)
     */
    public function getSoldierExcusalReason($soldier, $date)
    {
        Log::debug("🔍 Checking excusal reason for soldier {$soldier->id} on date: {$date} for report type: {$this->reportType}");

        // First check if soldier has ERE - if yes, they are automatically excused
        if ($this->hasActiveEreOnDate($soldier, $date)) {
            Log::debug("✅ Soldier {$soldier->id} has active ERE - AUTOMATICALLY EXCUSED");
            return [
                'reason' => 'ERE',
                'priority' => self::PRIORITY_ERE,
                'details' => 'Extra Regimental Employment'
            ];
        }

        $carbonDate = Carbon::parse($date);
        $sessionTimeRange = $this->getSessionTimeRange($date);

        if (!$sessionTimeRange) {
            Log::warning("⚠️ No session time configured for report type: {$this->reportType}");
        } else {
            Log::debug("🕒 Session time: {$sessionTimeRange['start']->format('H:i')} - {$sessionTimeRange['end']->format('H:i')}");
        }

        // Check all excusal conditions with priority
        $excusalChecks = [
            // [
            //     'name' => 'Leave',
            //     'priority' => self::PRIORITY_LEAVE,
            //     'check' => fn() => $this->isOnApprovedLeaveOnDate($soldier, $carbonDate),
            //     'details_fn' => fn() => $this->getLeaveDetails($soldier, $carbonDate)
            // ],
            [
                'name' => 'Roster Duty',
                'priority' => self::PRIORITY_ROSTER_DUTY,
                'check' => fn() => $this->hasActiveDutyWithTimeOrExcusal($soldier, $carbonDate, $sessionTimeRange),
                'details_fn' => fn() => $this->getRosterDutyDetails($soldier, $carbonDate, $sessionTimeRange)
            ],
            [
                'name' => 'Fixed Duty',
                'priority' => self::PRIORITY_FIXED_DUTY,
                'check' => fn() => $this->hasFixedDutyWithExcusal($soldier, $carbonDate, $sessionTimeRange),
                'details_fn' => fn() => $this->getFixedDutyDetails($soldier)
            ],
            // [
            //     'name' => 'Appointment',
            //     'priority' => self::PRIORITY_APPOINTMENT,
            //     'check' => fn() => $this->hasActiveAppointmentOnDate($soldier, $carbonDate),
            //     'details_fn' => fn() => $this->getAppointmentDetails($soldier, $carbonDate)
            // ],
            // [
            //     'name' => 'Course',
            //     'priority' => self::PRIORITY_COURSE,
            //     'check' => fn() => $this->hasActiveCourseOnDate($soldier, $carbonDate),
            //     'details_fn' => fn() => $this->getCourseDetails($soldier, $carbonDate)
            // ],
            // [
            //     'name' => 'Cadre',
            //     'priority' => self::PRIORITY_CADRE,
            //     'check' => fn() => $this->hasActiveCadreOnDate($soldier, $carbonDate),
            //     'details_fn' => fn() => $this->getCadreDetails($soldier, $carbonDate)
            // ],
        ];

        // Find the highest priority (lowest number) excusal reason
        $primaryReason = null;

        foreach ($excusalChecks as $excusalCheck) {
            if ($excusalCheck['check']()) {
                if ($primaryReason === null || $excusalCheck['priority'] < $primaryReason['priority']) {
                    $primaryReason = [
                        'reason' => $excusalCheck['name'],
                        'priority' => $excusalCheck['priority'],
                        'details' => $excusalCheck['details_fn']()
                    ];
                }
            }
        }

        if ($primaryReason) {
            Log::debug("✅ Soldier {$soldier->id} excused due to: {$primaryReason['reason']} - {$primaryReason['details']}");
        } else {
            Log::debug("❌ Soldier {$soldier->id} is NOT excused");
        }

        return $primaryReason;
    }

    /**
     * OPTIMIZED: Check if soldier has active duty with time overlap OR excusal checkbox
     * OR LOGIC: (Checkbox TRUE) OR (Time Overlap) → Both are independent excusal reasons
     * NOW CHECKS BOTH CURRENT DAY AND PREVIOUS DAY (for cross-midnight duties)
     */
    private function hasActiveDutyWithTimeOrExcusal($soldier, $carbonDate, $sessionTimeRange)
    {
        if (!$sessionTimeRange) {
            Log::warning("⚠️ No session time range - falling back to date-only check");
            return $this->hasActiveDutyOnDateOnly($soldier, $carbonDate);
        }

        $previousDay = $carbonDate->copy()->subDay();

        Log::debug("🔍 Checking duties for soldier {$soldier->id} on {$carbonDate->format('Y-m-d')} (including previous day for cross-midnight)");

        // Look for duties on BOTH current day AND previous day
        $duties = SoldierDuty::where('soldier_id', $soldier->id)
            ->where(function ($query) use ($carbonDate, $previousDay) {
                $query->whereDate('assigned_date', $carbonDate)
                    ->orWhereDate('assigned_date', $previousDay);
            })
            ->where('status', 'assigned')
            ->with('duty')
            ->get();

        Log::debug("   Found {$duties->count()} duties to check (current + previous day)");

        foreach ($duties as $duty) {
            if (!$duty->duty) {
                Log::debug("   ⚠️ Duty has no associated duty record - skipping");
                continue;
            }

            $dutyName = $duty->duty->duty_name ?? 'Unknown';
            $assignedDate = $duty->assigned_date->format('Y-m-d');

            Log::debug("   📋 Checking duty: {$dutyName} (assigned: {$assignedDate})");

            // CHECK 1: Excusal checkbox (highest priority)
            $hasExcusalTag = $duty->duty->{$this->excusalField};

            if ($hasExcusalTag === true) {
                Log::debug("   ✅ Duty '{$dutyName}' has excusal checkbox = TRUE for {$this->reportType} - AUTOMATICALLY EXCUSED {$this->excusalField}");
                return true;
            }

            // CHECK 2: Time overlap
            $timeOverlaps = $this->doesDutyOverlapWithSession($duty, $sessionTimeRange);

            if ($timeOverlaps) {
                Log::debug("   ⏰ Duty '{$dutyName}' time overlaps with {$this->reportType} session - EXCUSED");
                return true;
            }

            Log::debug("   ❌ Duty '{$dutyName}' does NOT excuse (checkbox DDDD: {$this->excusalField} " . ($hasExcusalTag ? 'true' : 'false/null') . ", time overlap: no)");
        }

        Log::debug("   ❌ No excusing duties found");
        return false;
    }

    /**
     * OPTIMIZED: Check if soldier has fixed duty with excusal checkbox OR time overlap
     * Fixed duties are recurring, so we check against current report session
     */
    private function hasFixedDutyWithExcusal($soldier, $carbonDate, $sessionTimeRange)
    {
        if (!$sessionTimeRange) {
            // Fallback to checkbox-only check
            return $this->hasFixedDutyWithExcusalCheckboxOnly($soldier);
        }

        $fixedDuties = DutyRank::where('soldier_id', $soldier->id)
            ->where('assignment_type', 'fixed')
            ->with('duty')
            ->get();

        if ($fixedDuties->isEmpty()) {
            return false;
        }

        Log::debug("🔍 Checking {$fixedDuties->count()} fixed duties for soldier {$soldier->id}");

        foreach ($fixedDuties as $fixedDutyRank) {
            $duty = $fixedDutyRank->duty;

            if (!$duty) {
                continue;
            }

            $dutyName = $duty->duty_name ?? 'Unknown';
            Log::debug("   📌 Checking fixed duty: {$dutyName}");

            // CHECK 1: Excusal checkbox
            $hasExcusalTag = $duty->{$this->excusalField};

            if ($hasExcusalTag === true) {
                Log::debug("   ✅ Fixed duty '{$dutyName}' has excusal checkbox = TRUE - AUTOMATICALLY EXCUSED");
                return true;
            }

            // CHECK 2: Time overlap (if duty has time information)
            if ($duty->start_time && $duty->end_time) {
                // Create a virtual soldier duty for time comparison
                $virtualDuty = new SoldierDuty([
                    'assigned_date' => $carbonDate,
                    'start_time' => $duty->start_time,
                    'end_time' => $duty->end_time,
                    'soldier_id' => $soldier->id,
                    'duty_id' => $duty->id,
                ]);
                $virtualDuty->setRelation('duty', $duty);

                if ($this->doesDutyOverlapWithSession($virtualDuty, $sessionTimeRange)) {
                    Log::debug("   ⏰ Fixed duty '{$dutyName}' time overlaps with session - EXCUSED");
                    return true;
                }
            }

            Log::debug("   ❌ Fixed duty '{$dutyName}' does NOT excuse");
        }

        return false;
    }

    /**
     * Fallback: Check fixed duty with checkbox only (when no session time configured)
     */
    private function hasFixedDutyWithExcusalCheckboxOnly($soldier)
    {
        $hasFixedDuty = DutyRank::where('soldier_id', $soldier->id)
            ->where('assignment_type', 'fixed')
            ->whereHas('duty', function ($query) {
                $query->where($this->excusalField, true);
            })
            ->exists();

        if ($hasFixedDuty) {
            Log::debug("📌 Soldier {$soldier->id} has fixed duty with excusal checkbox for {$this->reportType}");
        }

        return $hasFixedDuty;
    }

    /**
     * Fallback: Date-only duty check (when no time information available)
     */
    private function hasActiveDutyOnDateOnly($soldier, $carbonDate)
    {
        return SoldierDuty::where('soldier_id', $soldier->id)
            ->whereDate('assigned_date', $carbonDate)
            ->where('status', 'assigned')
            ->exists();
    }

    // ========== DATE-ONLY CHECKS (for non-duty excusals) ==========

    /**
     * Check if soldier is on approved leave (date only)
     */
    private function isOnApprovedLeaveOnDate($soldier, $carbonDate)
    {
        $onLeave = LeaveApplication::where('soldier_id', $soldier->id)
            ->where('application_current_status', 'approved')
            ->whereDate('start_date', '<=', $carbonDate)
            ->whereDate('end_date', '>=', $carbonDate)
            ->exists();

        if ($onLeave) {
            Log::debug("🏖️  Soldier {$soldier->id} is on approved leave on {$carbonDate->toDateString()}");
        }

        return $onLeave;
    }

    /**
     * Check if soldier has active appointment (date only)
     */ // obsolete
    private function hasActiveAppointmentOnDate($soldier, $carbonDate)
    {
        $hasAppointment = SoldierServices::where('soldier_id', $soldier->id)
            ->where('status', 'active')
            ->whereDate('appointments_from_date', '<=', $carbonDate)
            ->where(function ($query) use ($carbonDate) {
                $query->whereNull('appointments_to_date')
                    ->orWhereDate('appointments_to_date', '>=', $carbonDate);
            })
            ->exists();

        if ($hasAppointment) {
            Log::debug("💼 Soldier {$soldier->id} has active appointment on {$carbonDate->toDateString()}");
        }

        return $hasAppointment;
    }

    /**
     * Check if soldier has active course (date only)
     */ // obsolete
    private function hasActiveCourseOnDate($soldier, $carbonDate)
    {
        $hasCourse = SoldierCourse::where('soldier_id', $soldier->id)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $carbonDate)
            ->where(function ($query) use ($carbonDate) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $carbonDate);
            })
            ->exists();

        if ($hasCourse) {
            Log::debug("📚 Soldier {$soldier->id} has active course on {$carbonDate->toDateString()}");
        }

        return $hasCourse;
    }

    /**
     * Check if soldier has active cadre (date only)
     */ // obsolete
    private function hasActiveCadreOnDate($soldier, $carbonDate)
    {
        $hasCadre = SoldierCadre::where('soldier_id', $soldier->id)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $carbonDate)
            ->where(function ($query) use ($carbonDate) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $carbonDate);
            })
            ->exists();

        if ($hasCadre) {
            Log::debug("👥 Soldier {$soldier->id} has active cadre on {$carbonDate->toDateString()}");
        }

        return $hasCadre;
    }

    /**
     * Check if soldier has active ERE on a given date (date only)
     */
    private function hasActiveEreOnDate($soldier, $date)
    {
        $carbonDate = Carbon::parse($date);

        $hasEre = $soldier->ere()
            ->whereDate('start_date', '<=', $carbonDate)
            ->where(function ($query) use ($carbonDate) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $carbonDate);
            })
            ->exists();

        if ($hasEre) {
            Log::debug("📋 Soldier {$soldier->id} has active ERE from {$carbonDate->toDateString()}");
        }

        return $hasEre;
    }

    // ========== DETAIL GETTER METHODS ==========

    private function getLeaveDetails($soldier, $carbonDate)
    {
        $leave = LeaveApplication::where('soldier_id', $soldier->id)
            ->where('application_current_status', 'approved')
            ->whereDate('start_date', '<=', $carbonDate)
            ->whereDate('end_date', '>=', $carbonDate)
            ->with('leaveType')
            ->first();

        return $leave && $leave->leaveType ? $leave->leaveType->name : 'Leave';
    }

    private function getRosterDutyDetails($soldier, $carbonDate, $sessionTimeRange)
    {
        if (!$sessionTimeRange) {
            return $this->getRosterDutyDetailsSimple($soldier, $carbonDate);
        }

        $previousDay = $carbonDate->copy()->subDay();

        // Get the duty that actually excused the soldier
        $duty = SoldierDuty::where('soldier_id', $soldier->id)
            ->where(function ($query) use ($carbonDate, $previousDay) {
                $query->whereDate('assigned_date', $carbonDate)
                    ->orWhereDate('assigned_date', $previousDay);
            })
            ->where('status', 'assigned')
            ->with('duty')
            ->get()
            ->first(function ($soldierDuty) use ($sessionTimeRange) {
                if (!$soldierDuty->duty) return false;

                // Check if this duty has checkbox OR time overlap
                $hasCheckbox = $soldierDuty->duty->{$this->excusalField} === true;
                $hasOverlap = $this->doesDutyOverlapWithSession($soldierDuty, $sessionTimeRange);

                return $hasCheckbox || $hasOverlap;
            });

        if (!$duty || !$duty->duty) {
            return 'Roster Duty';
        }

        $details = $duty->duty->duty_name;

        // Add date if from previous day
        if ($duty->assigned_date->format('Y-m-d') !== $carbonDate->format('Y-m-d')) {
            $details .= " (" . $duty->assigned_date->format('d M') . ")";
        }

        // Add time information if available
        if ($duty->start_time && $duty->end_time) {
            $startTime = $duty->start_time->format('H:i');
            $endTime = $duty->end_time->format('H:i');

            // Indicate if cross-midnight
            if ($endTime < $startTime) {
                $details .= " ({$startTime} - {$endTime}+1)";
            } else {
                $details .= " ({$startTime} - {$endTime})";
            }
        }

        // Add excusal reason indicator
        $hasExcusalTag = $duty->duty->{$this->excusalField} === true;
        if ($hasExcusalTag) {
            $details .= " [✓]";
        }

        return $details;
    }

    private function getRosterDutyDetailsSimple($soldier, $carbonDate)
    {
        $duty = SoldierDuty::where('soldier_id', $soldier->id)
            ->whereDate('assigned_date', $carbonDate)
            ->where('status', 'assigned')
            ->with('duty')
            ->first();

        return $duty && $duty->duty ? $duty->duty->duty_name : 'Roster Duty';
    }

    private function getFixedDutyDetails($soldier)
    {
        $duty = DutyRank::where('soldier_id', $soldier->id)
            ->where('assignment_type', 'fixed')
            ->with('duty')
            ->first();

        if (!$duty || !$duty->duty) {
            return 'Fixed Duty';
        }

        $details = $duty->duty->duty_name;

        // Add time if available
        if ($duty->duty->start_time && $duty->duty->end_time) {
            $startTime = $duty->duty->start_time->format('H:i');
            $endTime = $duty->duty->end_time->format('H:i');
            $details .= " ({$startTime} - {$endTime})";
        }

        return $details;
    }
    // obsolete
    private function getAppointmentDetails($soldier, $carbonDate)
    {
        $appointment = SoldierServices::where('soldier_id', $soldier->id)
            ->where('status', 'active')
            ->whereDate('appointments_from_date', '<=', $carbonDate)
            ->where(function ($query) use ($carbonDate) {
                $query->whereNull('appointments_to_date')
                    ->orWhereDate('appointments_to_date', '>=', $carbonDate);
            })
            ->first();

        return $appointment ? $appointment->appointments_name : 'Appointment';
    }
    // obsolete
    private function getCourseDetails($soldier, $carbonDate)
    {
        $course = SoldierCourse::where('soldier_id', $soldier->id)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $carbonDate)
            ->where(function ($query) use ($carbonDate) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $carbonDate);
            })
            ->with('course')
            ->first();

        return $course && $course->course ? $course->course->name : 'Course';
    }
    // obsolete
    private function getCadreDetails($soldier, $carbonDate)
    {
        $cadre = SoldierCadre::where('soldier_id', $soldier->id)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $carbonDate)
            ->where(function ($query) use ($carbonDate) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $carbonDate);
            })
            ->with('cadre')
            ->first();

        return $cadre && $cadre->cadre ? $cadre->cadre->name : 'Cadre';
    }

    /**
     * Check if a soldier is excused (backward compatibility)
     */
    public function isSoldierExcused($soldier, $date)
    {
        return $this->getSoldierExcusalReason($soldier, $date) !== null;
    }

    /**
     * Get report title based on report type
     */
    public function getReportTitle()
    {
        $titles = [
            self::REPORT_GAME => 'Game Attendance Report',
            self::REPORT_PT => 'PT Attendance Report',
            self::REPORT_ROLL_CALL => 'Roll Call Report',
            self::REPORT_PARADE => '2nd Fall in Report',
        ];

        return $titles[$this->reportType] ?? 'Attendance Report';
    }

    /**
     * Get file name based on report type
     */
    public function getFileName($date)
    {
        $formattedDate = Carbon::parse($date)->format('Y_m_d');
        $fileNames = [
            self::REPORT_GAME => "game_attendance_report_{$formattedDate}",
            self::REPORT_PT => "pt_attendance_report_{$formattedDate}",
            self::REPORT_ROLL_CALL => "roll_call_attendance_report_{$formattedDate}",
            self::REPORT_PARADE => "2nd_fallin_report_{$formattedDate}",
        ];

        return $fileNames[$this->reportType] ?? "attendance_report_{$formattedDate}";
    }

    /**
     * Get Format 1 data - Summary by Company and Rank Type
     */
    public function getFormat1Data($date)
    {
        Log::info("📊 GENERATING FORMAT1 DATA for {$this->reportType} report on date: {$date}");

        $companies = Company::orderBy('id')->get();
        $rankTypes = $this->getRankTypes();
        $data = [];
        $totals = array_fill_keys($rankTypes, 0);
        $totals['total'] = 0;
        $totals['excused'] = 0;
        $totals['all_total'] = 0;

        Log::debug("🏢 Processing {$companies->count()} companies and " . count($rankTypes) . " rank types");

        foreach ($companies as $company) {
            Log::debug("🔍 Processing company: {$company->name}");

            $row = ['company' => $company->name];
            $companyTotal = 0;
            $companyExcused = 0;

            foreach ($rankTypes as $type) {
                $count = Soldier::where('company_id', $company->id)
                    ->whereHas('rank', function ($query) use ($type) {
                        $query->where('type', $type);
                    })
                    ->where(function ($query) use ($date) {
                        $this->excludeSoldiersWithActiveEre($query, $date);
                    })
                    ->count();

                $row[$type] = $count;
                $companyTotal += $count;
                $totals[$type] += $count;

                Log::debug("   📈 Rank type {$type}: {$count} soldiers");
            }

            // Calculate excused soldiers for this company
            $allSoldiers = Soldier::where('company_id', $company->id)
                ->where(function ($query) use ($date) {
                    $this->excludeSoldiersWithActiveEre($query, $date);
                })
                ->get();

            Log::debug("   👥 Company {$company->name} has {$allSoldiers->count()} total soldiers (excluding ERE)");

            $excusedSoldiers = [];
            foreach ($allSoldiers as $soldier) {
                if ($this->isSoldierExcused($soldier, $date)) {
                    $companyExcused++;
                    $excusedSoldiers[] = $soldier->id;
                }
            }

            Log::debug("   ✅ Company {$company->name}: {$companyExcused} excused soldiers (IDs: " . implode(', ', $excusedSoldiers) . ")");

            $row['Total'] = $companyTotal;
            $row['Excused'] = $companyExcused;
            $row['All Total'] = $companyTotal - $companyExcused;

            $totals['total'] += $companyTotal;
            $totals['excused'] += $companyExcused;
            $totals['all_total'] += ($companyTotal - $companyExcused);

            $data[] = $row;
        }

        // Add totals row
        $totalRow = ['company' => 'Total'];
        foreach ($rankTypes as $type) {
            $totalRow[$type] = $totals[$type];
        }
        $totalRow['Total'] = $totals['total'];
        $totalRow['Excused'] = $totals['excused'];
        $totalRow['All Total'] = $totals['all_total'];

        $data[] = $totalRow;

        Log::info("📈 FORMAT1 COMPLETED - Total present: {$totals['total']}, Total excused: {$totals['excused']}, Net total: {$totals['all_total']}");

        return $data;
    }

    /**
     * Get Format 2 data - Exclusion by Duty / Appointment Type (with priority-based unique counting)
     */
    public function getFormat2Data($date)
    {
        Log::info("📋 GENERATING FORMAT2 DATA (SIMPLIFIED GROUPING) for {$this->reportType} report on date: {$date}");

        $carbonDate = Carbon::parse($date);
        $companies = Company::orderBy('id')->get();
        $companyNames = $companies->pluck('name')->toArray();

        // Get all soldiers (excluding ERE)
        $allSoldiers = Soldier::where(function ($query) use ($date) {
            $this->excludeSoldiersWithActiveEre($query, $date);
        })
            ->with('company')
            ->get();

        Log::debug("👥 Total soldiers to process (excluding ERE): {$allSoldiers->count()}");

        // Group by simple duty types
        $dutyGroups = [
            'Roster Duties' => [],
            'Fixed Duties' => [],
            'Leave' => [],
            'Appointments' => [],
            'Courses' => [],
            'Cadres' => [],
            'ERE' => [],
        ];

        $totalExcused = 0;
        $grandTotal = array_fill_keys($companyNames, 0);

        foreach ($allSoldiers as $soldier) {
            $excusalReason = $this->getSoldierExcusalReason($soldier, $date);

            if ($excusalReason) {
                $totalExcused++;
                $category = $excusalReason['reason'];

                // Map to simple group names
                $groupName = match ($category) {
                    'Roster Duty' => 'Roster Duties',
                    'Fixed Duty' => 'Fixed Duties',
                    'Leave' => 'Leave',
                    'Appointment' => 'Appointments',
                    'Course' => 'Courses',
                    'Cadre' => 'Cadres',
                    'ERE' => 'ERE',
                    default => 'Other'
                };

                if (!isset($dutyGroups[$groupName][$soldier->company->name])) {
                    $dutyGroups[$groupName][$soldier->company->name] = 0;
                }

                $dutyGroups[$groupName][$soldier->company->name]++;
                $grandTotal[$soldier->company->name]++;
            }
        }

        // Build the data rows
        $data = [];

        foreach ($dutyGroups as $groupName => $companyCounts) {
            if (array_sum($companyCounts) > 0) {
                $row = [
                    'category' => $groupName,
                    'type' => '', // Empty for simplified version
                ];

                foreach ($companies as $company) {
                    $row[$company->name] = $companyCounts[$company->name] ?? 0;
                }

                $row['Total'] = array_sum($companyCounts);
                $data[] = $row;

                Log::debug("   📊 {$groupName}: " . $row['Total'] . " soldiers");
            }
        }

        // Add totals row
        $totalRow = [
            'category' => 'Total',
            'type' => '',
        ];

        foreach ($companies as $company) {
            $totalRow[$company->name] = $grandTotal[$company->name] ?? 0;
        }

        $totalRow['Total'] = $totalExcused;
        $data[] = $totalRow;

        Log::info("📋 FORMAT2 COMPLETED (SIMPLIFIED) - Total excused: {$totalExcused}");

        return $data;
    }

    /**
     * Common method to check if soldier has active ERE for query optimization
     */
    private function excludeSoldiersWithActiveEre($query, $date)
    {
        $carbonDate = Carbon::parse($date);

        return $query->whereDoesntHave('ere', function ($q) use ($carbonDate) {
            $q->whereDate('start_date', '<=', $carbonDate)
                ->where(function ($q2) use ($carbonDate) {
                    $q2->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $carbonDate);
                });
        });
    }

    /**
     * Get all rank types
     */
    public function getRankTypes()
    {
        $rankTypes = Rank::select('type')
            ->distinct()
            ->pluck('type')
            ->toArray();

        $customOrder = ['OFFICER', 'JCO', 'OR', 'RCO'];

        return collect($rankTypes)
            ->sortBy(function ($type) use ($customOrder) {
                return array_search($type, $customOrder) ?? 999;
            })
            ->values()
            ->toArray();
    }

    /**
     * Get Format 3 data - Detailed list of all excused soldiers
     */
    public function getFormat3Data($date)
    {
        Log::info("📝 GENERATING FORMAT3 DATA (EXCUSED SOLDIERS LIST) for {$this->reportType} report on date: {$date}");

        $carbonDate = Carbon::parse($date);

        // Get all soldiers (excluding ERE)
        $allSoldiers = Soldier::where(function ($query) use ($date) {
            $this->excludeSoldiersWithActiveEre($query, $date);
        })
            ->with(['company', 'rank'])
            ->orderBy('company_id')
            ->orderBy('army_no')
            ->get();

        Log::debug("👥 Total soldiers to check (excluding ERE): {$allSoldiers->count()}");

        $excusedSoldiersList = [];
        $serialNumber = 1;

        foreach ($allSoldiers as $soldier) {
            $excusalReason = $this->getSoldierExcusalReason($soldier, $date);

            if ($excusalReason) {
                $excusedSoldiersList[] = [
                    'sl_no' => $serialNumber++,
                    'army_no' => $soldier->army_no ?? 'N/A',
                    'rank' => $soldier->rank->name ?? 'N/A',
                    'name' => $soldier->full_name ?? 'N/A',
                    'company' => $soldier->company->name ?? 'N/A',
                    'excusal_category' => $excusalReason['reason'],
                    'excusal_details' => $excusalReason['details'],
                    'priority' => $excusalReason['priority'],
                ];
            }
        }

        Log::info("📝 FORMAT3 COMPLETED - Total excused soldiers: " . count($excusedSoldiersList));

        return $excusedSoldiersList;
    }

    /**
     * Get complete report data - all formats combined
     */
    public function getCompleteReportData($date)
    {
        Log::info("📋 GENERATING COMPLETE REPORT DATA for {$this->reportType} report on date: {$date}");

        return [
            'statistics' => $this->getSummaryStatistics($date),
            'format1' => $this->getFormat1Data($date),
            'format2' => $this->getFormat2Data($date),
            'format3' => $this->getFormat3Data($date),
        ];
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStatistics($date)
    {
        Log::info("📊 GENERATING SUMMARY STATISTICS for {$this->reportType} report on date: {$date}");

        // Get Format 1 data
        $format1Data = $this->getFormat1Data($date);
        $format1Totals = end($format1Data);

        // Get Format 3 data (detailed list)
        $format3Data = $this->getFormat3Data($date);

        // Calculate statistics
        $totalStrength = $format1Totals['Total'] ?? 0;
        $totalExcused = $format1Totals['Excused'] ?? 0;
        $totalPresent = $format1Totals['All Total'] ?? 0;

        $statistics = [
            'date' => $date,
            'report_type' => $this->reportType,
            'report_title' => $this->getReportTitle(),

            // Overall counts
            'total_strength' => $totalStrength,
            'total_excused' => $totalExcused,
            'total_present' => $totalPresent,
            'excusal_percentage' => $totalStrength > 0 ? round(($totalExcused / $totalStrength) * 100, 2) : 0,

            // Format verification
            'format1_excused' => $format1Totals['Excused'] ?? 0,
            'format3_count' => count($format3Data),
            'counts_match' => ($format1Totals['Excused'] ?? 0) === count($format3Data),
        ];

        Log::info("📊 SUMMARY STATISTICS COMPLETED");
        Log::info("   Total Strength: {$statistics['total_strength']}");
        Log::info("   Total Excused: {$statistics['total_excused']}");
        Log::info("   Total Present: {$statistics['total_present']}");
        Log::info("   Counts Match: " . ($statistics['counts_match'] ? 'YES ✅' : 'NO ❌'));

        return $statistics;
    }

    /**
     * Debug method to check all excusal possibilities for a soldier
     */
    public function debugSoldierExcusal($soldier, $date)
    {
        $carbonDate = Carbon::parse($date);
        $sessionTimeRange = $this->getSessionTimeRange($date);

        Log::info("═══════════════════════════════════════════════════════");
        Log::info("🔍 COMPLETE EXCUSAL DEBUG FOR SOLDIER {$soldier->id}");
        Log::info("═══════════════════════════════════════════════════════");
        Log::info("📅 Date: {$date}");
        Log::info("📋 Report Type: {$this->reportType}");
        Log::info("🏷️  Excusal Field: {$this->excusalField}");

        if ($sessionTimeRange) {
            Log::info("🕒 Session Time: {$sessionTimeRange['start']->format('H:i')} - {$sessionTimeRange['end']->format('H:i')}");
        } else {
            Log::info("⚠️  No session time configured");
        }

        Log::info("───────────────────────────────────────────────────────");

        $checks = [
            'ERE' => $this->hasActiveEreOnDate($soldier, $date),
            'Leave' => $this->isOnApprovedLeaveOnDate($soldier, $carbonDate),
            'Roster Duty' => $this->hasActiveDutyWithTimeOrExcusal($soldier, $carbonDate, $sessionTimeRange),
            'Fixed Duty' => $this->hasFixedDutyWithExcusal($soldier, $carbonDate, $sessionTimeRange),
            'Appointment' => $this->hasActiveAppointmentOnDate($soldier, $carbonDate),
            'Course' => $this->hasActiveCourseOnDate($soldier, $carbonDate),
            'Cadre' => $this->hasActiveCadreOnDate($soldier, $carbonDate),
        ];

        foreach ($checks as $checkName => $result) {
            $icon = $result ? '✅' : '❌';
            $status = $result ? 'EXCUSED' : 'NOT EXCUSED';
            Log::info("{$icon} {$checkName}: {$status}");
        }

        Log::info("───────────────────────────────────────────────────────");

        $finalReason = $this->getSoldierExcusalReason($soldier, $date);

        if ($finalReason) {
            Log::info("🎯 FINAL RESULT: EXCUSED");
            Log::info("   Priority: {$finalReason['priority']}");
            Log::info("   Reason: {$finalReason['reason']}");
            Log::info("   Details: {$finalReason['details']}");
        } else {
            Log::info("🎯 FINAL RESULT: NOT EXCUSED");
        }

        Log::info("═══════════════════════════════════════════════════════");

        return [
            'soldier_id' => $soldier->id,
            'date' => $date,
            'report_type' => $this->reportType,
            'checks' => $checks,
            'final_result' => $finalReason,
        ];
    }

    /**
     * Test cross-midnight duty handling
     */
    public function testCrossMidnightDuty($dutyStartTime, $dutyEndTime, $sessionStartTime, $sessionEndTime, $testDate = null)
    {
        $testDate = $testDate ?? Carbon::today();

        Log::info("═══════════════════════════════════════════════════════");
        Log::info("🧪 TESTING CROSS-MIDNIGHT DUTY");
        Log::info("═══════════════════════════════════════════════════════");
        Log::info("📅 Test Date: {$testDate->format('Y-m-d')}");
        Log::info("⏰ Duty Time: {$dutyStartTime} → {$dutyEndTime}");
        Log::info("🕒 Session Time: {$sessionStartTime} → {$sessionEndTime}");
        Log::info("───────────────────────────────────────────────────────");

        // Create test duty
        $testDuty = new SoldierDuty([
            'assigned_date' => $testDate,
            'start_time' => Carbon::parse($dutyStartTime),
            'end_time' => Carbon::parse($dutyEndTime),
        ]);

        $testDuty->setRelation('duty', new Duty(['duty_name' => 'Test Duty']));

        // Create test session range
        $sessionRange = [
            'start' => Carbon::parse($testDate->format('Y-m-d') . ' ' . $sessionStartTime),
            'end' => Carbon::parse($testDate->format('Y-m-d') . ' ' . $sessionEndTime),
        ];

        $overlaps = $this->doesDutyOverlapWithSession($testDuty, $sessionRange);

        Log::info("───────────────────────────────────────────────────────");
        Log::info("🎯 RESULT: " . ($overlaps ? 'OVERLAPS ✅' : 'NO OVERLAP ❌'));
        Log::info("═══════════════════════════════════════════════════════");

        return $overlaps;
    }
}
