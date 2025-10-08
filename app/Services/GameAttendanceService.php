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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GameAttendanceService
{
    protected $reportType;
    protected $excusalField;

    // Report type constants
    const REPORT_GAME = 'game';
    const REPORT_PT = 'pt';
    const REPORT_ROLL_CALL = 'roll_call';
    const REPORT_PARADE = 'parade';

    // Excusal priority constants (lower number = higher priority)
    const PRIORITY_LEAVE = 1;
    const PRIORITY_ROSTER_DUTY = 2;
    const PRIORITY_FIXED_DUTY = 3;
    const PRIORITY_APPOINTMENT = 4;
    const PRIORITY_COURSE = 5;
    const PRIORITY_CADRE = 6;
    const PRIORITY_EXCUSAL_DUTY_YESTERDAY = 7;

    public function __construct($reportType = self::REPORT_GAME)
    {
        $this->reportType = $reportType;
        $this->excusalField = $this->getExcusalFieldForReportType($reportType);
    }

    /**
     * Get the excusal field name based on report type
     */
    private function getExcusalFieldForReportType($reportType)
    {
        $fieldMap = [
            self::REPORT_GAME => 'excused_next_day_games',
            self::REPORT_PT => 'excused_next_day_pt',
            self::REPORT_ROLL_CALL => 'excused_next_day_roll_call',
            self::REPORT_PARADE => 'excused_next_day_parade',
        ];

        return $fieldMap[$reportType] ?? 'excused_next_day_games';
    }

    /**
     * Get the primary excusal reason for a soldier (highest priority reason)
     * Returns: ['reason' => string, 'priority' => int, 'details' => string] or null
     */
    public function getSoldierExcusalReason($soldier, $date)
    {
        Log::debug("🔍 Checking excusal reason for soldier {$soldier->id} on date: {$date}");

        // First check if soldier has ERE - if yes, they are automatically excused
        if ($this->hasActiveEreOnDate($soldier, $date)) {
            Log::debug("✅ Soldier {$soldier->id} has active ERE - AUTOMATICALLY EXCUSED");
            return [
                'reason' => 'ERE',
                'priority' => 0,
                'details' => 'Extra Regimental Employment'
            ];
        }

        $carbonDate = Carbon::parse($date);
        $yesterday = $carbonDate->copy()->subDay();

        // Check all excusal conditions with priority
        $excusalChecks = [
            [
                'name' => 'Leave',
                'priority' => self::PRIORITY_LEAVE,
                'check' => fn() => $this->isOnApprovedLeaveOnDate($soldier, $carbonDate),
                'details_fn' => fn() => $this->getLeaveDetails($soldier, $carbonDate)
            ],
            [
                'name' => 'Roster Duty',
                'priority' => self::PRIORITY_ROSTER_DUTY,
                'check' => fn() => $this->hasActiveDutyOnDate($soldier, $carbonDate),
                'details_fn' => fn() => $this->getRosterDutyDetails($soldier, $carbonDate)
            ],
            [
                'name' => 'Fixed Duty',
                'priority' => self::PRIORITY_FIXED_DUTY,
                'check' => fn() => $this->hasFixedDuty($soldier),
                'details_fn' => fn() => $this->getFixedDutyDetails($soldier)
            ],
            [
                'name' => 'Appointment',
                'priority' => self::PRIORITY_APPOINTMENT,
                'check' => fn() => $this->hasActiveAppointmentOnDate($soldier, $carbonDate),
                'details_fn' => fn() => $this->getAppointmentDetails($soldier, $carbonDate)
            ],
            [
                'name' => 'Course',
                'priority' => self::PRIORITY_COURSE,
                'check' => fn() => $this->hasActiveCourseOnDate($soldier, $carbonDate),
                'details_fn' => fn() => $this->getCourseDetails($soldier, $carbonDate)
            ],
            [
                'name' => 'Cadre',
                'priority' => self::PRIORITY_CADRE,
                'check' => fn() => $this->hasActiveCadreOnDate($soldier, $carbonDate),
                'details_fn' => fn() => $this->getCadreDetails($soldier, $carbonDate)
            ],
            [
                'name' => 'Excusal Duty Yesterday',
                'priority' => self::PRIORITY_EXCUSAL_DUTY_YESTERDAY,
                'check' => fn() => $this->hadExcusalDutyYesterday($soldier, $yesterday),
                'details_fn' => fn() => $this->getExcusalDutyYesterdayDetails($soldier, $yesterday)
            ],
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
     * Check if a soldier is excused (backward compatibility)
     */
    public function isSoldierExcused($soldier, $date)
    {
        return $this->getSoldierExcusalReason($soldier, $date) !== null;
    }

    // Detail getter methods for each excusal type
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

    private function getRosterDutyDetails($soldier, $carbonDate)
    {
        $duty = SoldierDuty::where('soldier_id', $soldier->id)
            ->whereDate('assigned_date', $carbonDate)
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

        return $duty && $duty->duty ? $duty->duty->duty_name : 'Fixed Duty';
    }

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

    private function getExcusalDutyYesterdayDetails($soldier, $yesterday)
    {
        $duty = SoldierDuty::where('soldier_id', $soldier->id)
            ->whereDate('assigned_date', $yesterday)
            ->whereHas('duty', function ($query) {
                $query->where($this->excusalField, true);
            })
            ->with('duty')
            ->first();

        return $duty && $duty->duty ? $duty->duty->duty_name . " (Yesterday)" : 'Excusal Duty Yesterday';
    }

    /**
     * Check if soldier had excusal duty yesterday for the current report type
     */
    private function hadExcusalDutyYesterday($soldier, $yesterday)
    {
        $hadExcusal = SoldierDuty::where('soldier_id', $soldier->id)
            ->whereDate('assigned_date', $yesterday)
            ->whereHas('duty', function ($query) {
                $query->where($this->excusalField, true);
            })
            ->exists();

        if ($hadExcusal) {
            Log::debug("🔄 Soldier {$soldier->id} had excusal duty yesterday ({$yesterday->toDateString()}) for {$this->reportType}");
        }

        return $hadExcusal;
    }

    /**
     * Get report title based on report type
     */
    public function getReportTitle()
    {
        $titles = [
            self::REPORT_GAME => 'Game Attendance Report',
            self::REPORT_PT => 'PT Attendance Report',
            self::REPORT_ROLL_CALL => 'Roll Call Attendance Report',
            self::REPORT_PARADE => 'Parade Attendance Report',
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
            self::REPORT_PARADE => "parade_attendance_report_{$formattedDate}",
        ];

        return $fileNames[$this->reportType] ?? "attendance_report_{$formattedDate}";
    }

    /**
     * Get Format 1 data - Summary by Company and Rank Type
     */
    public function getFormat1Data($date)
    {
        Log::info("📊 GENERATING FORMAT1 DATA for {$this->reportType} report on date: {$date}");

        $companies = Company::orderBy('name')->get();
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
        Log::info("📋 GENERATING FORMAT2 DATA (PRIORITY-BASED) for {$this->reportType} report on date: {$date}");

        $carbonDate = Carbon::parse($date);
        $companies = Company::orderBy('name')->get();
        $companyNames = $companies->pluck('name')->toArray();

        // Get all soldiers (excluding ERE)
        $allSoldiers = Soldier::where(function ($query) use ($date) {
            $this->excludeSoldiersWithActiveEre($query, $date);
        })
            ->with('company')
            ->get();

        Log::debug("👥 Total soldiers to process (excluding ERE): {$allSoldiers->count()}");

        // Categorize each soldier by their PRIMARY excusal reason
        $categorizedSoldiers = [];
        $totalExcused = 0;

        foreach ($allSoldiers as $soldier) {
            $excusalReason = $this->getSoldierExcusalReason($soldier, $date);

            if ($excusalReason) {
                $totalExcused++;
                $category = $excusalReason['reason'];
                $details = $excusalReason['details'];

                if (!isset($categorizedSoldiers[$category])) {
                    $categorizedSoldiers[$category] = [];
                }

                if (!isset($categorizedSoldiers[$category][$details])) {
                    $categorizedSoldiers[$category][$details] = [];
                }

                $categorizedSoldiers[$category][$details][] = $soldier;
            }
        }

        Log::debug("📊 Total excused soldiers: {$totalExcused}");
        Log::debug("📋 Categories found: " . implode(', ', array_keys($categorizedSoldiers)));

        // Build the data rows
        $data = [];
        $grandTotal = array_fill_keys($companyNames, 0);

        // Define category order for output
        $categoryOrder = [
            'Leave' => 'Leave',
            'Roster Duty' => 'Roster Duties',
            'Fixed Duty' => 'Fixed Duties',
            'Appointment' => 'Appointments',
            'Course' => 'Courses',
            'Cadre' => 'Cadres',
            'Excusal Duty Yesterday' => 'Excusal Duties (Yesterday)',
        ];

        foreach ($categoryOrder as $categoryKey => $categoryDisplay) {
            if (isset($categorizedSoldiers[$categoryKey])) {
                foreach ($categorizedSoldiers[$categoryKey] as $typeName => $soldiers) {
                    $row = [
                        'category' => $categoryDisplay,
                        'type' => $typeName,
                    ];

                    $counts = array_fill_keys($companyNames, 0);

                    foreach ($soldiers as $soldier) {
                        if ($soldier->company) {
                            $companyName = $soldier->company->name;
                            if (in_array($companyName, $companyNames)) {
                                $counts[$companyName]++;
                                $grandTotal[$companyName]++;
                            }
                        }
                    }

                    foreach ($companies as $company) {
                        $row[$company->name] = $counts[$company->name];
                    }

                    $row['Total'] = count($soldiers);
                    $data[] = $row;

                    Log::debug("   📝 {$categoryDisplay} - {$typeName}: " . count($soldiers) . " soldiers");
                }
            }
        }

        // Add totals row
        $totalRow = [
            'category' => 'Total',
            'type' => '',
        ];

        foreach ($companies as $company) {
            $totalRow[$company->name] = $grandTotal[$company->name];
        }

        $totalRow['Total'] = $totalExcused;
        $data[] = $totalRow;

        Log::info("📋 FORMAT2 COMPLETED (PRIORITY-BASED) - Total excused: {$totalExcused}");
        Log::info("📊 NO DUPLICATES - Each soldier counted only once in their highest priority category");

        return $data;
    }

    /**
     * Check if a soldier has active ERE on a given date
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
     * Check if soldier has active duty on specific date
     */
    private function hasActiveDutyOnDate($soldier, $carbonDate)
    {
        $hasDuty = SoldierDuty::where('soldier_id', $soldier->id)
            ->whereDate('assigned_date', $carbonDate)
            ->exists();

        if ($hasDuty) {
            Log::debug("🎯 Soldier {$soldier->id} has active duty on {$carbonDate->toDateString()}");
        }

        return $hasDuty;
    }

    /**
     * Check if soldier has fixed duty
     */
    private function hasFixedDuty($soldier)
    {
        $hasFixedDuty = DutyRank::where('soldier_id', $soldier->id)
            ->where('assignment_type', 'fixed')
            ->exists();

        if ($hasFixedDuty) {
            Log::debug("📌 Soldier {$soldier->id} has fixed duty");
        }

        return $hasFixedDuty;
    }

    /**
     * Check if soldier has active course on date (null end_date means ongoing)
     */
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
     * Check if soldier has active cadre on date (null end_date means ongoing)
     */
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
     * Check if soldier has active appointment on date
     */
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
     * Check if soldier is on approved leave on date
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
     * Get actual unique excused count for verification
     */
    public function getActualExcusedCount($date)
    {
        Log::info("🔍 CALCULATING ACTUAL EXCUSED COUNT for {$this->reportType} report on date: {$date}");

        $soldiers = Soldier::where(function ($query) use ($date) {
            $this->excludeSoldiersWithActiveEre($query, $date);
        })
            ->get();

        Log::debug("👥 Total soldiers (excluding ERE): {$soldiers->count()}");

        $excusedCount = 0;
        $excusedSoldierIds = [];

        foreach ($soldiers as $soldier) {
            if ($this->isSoldierExcused($soldier, $date)) {
                $excusedCount++;
                $excusedSoldierIds[] = $soldier->id;
            }
        }

        Log::info("✅ ACTUAL EXCUSED COUNT: {$excusedCount} soldiers");
        Log::debug("📝 Excused soldier IDs: " . implode(', ', $excusedSoldierIds));

        return $excusedCount;
    }

    /**
     * Get Format 3 data - Detailed list of all excused soldiers
     * Returns a list of all excused soldiers with their complete information
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
     * Get summary statistics for all formats
     * Returns comprehensive statistics including counts from all formats
     */
    public function getSummaryStatistics($date)
    {
        Log::info("📊 GENERATING SUMMARY STATISTICS for {$this->reportType} report on date: {$date}");

        // Get Format 1 data
        $format1Data = $this->getFormat1Data($date);
        $format1Totals = end($format1Data);

        // Get Format 2 data
        $format2Data = $this->getFormat2Data($date);
        $format2Totals = end($format2Data);

        // Get Format 3 data (detailed list)
        $format3Data = $this->getFormat3Data($date);

        // Calculate statistics
        $totalStrength = $format1Totals['Total'] ?? 0;
        $totalExcused = $format1Totals['Excused'] ?? 0;
        $totalPresent = $format1Totals['All Total'] ?? 0;

        $excusedByCategory = [];
        foreach ($format2Data as $row) {
            if ($row['category'] !== 'Total') {
                $category = $row['category'];
                if (!isset($excusedByCategory[$category])) {
                    $excusedByCategory[$category] = 0;
                }
                $excusedByCategory[$category] += $row['Total'];
            }
        }

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
            'format2_total' => $format2Totals['Total'] ?? 0,
            'format3_count' => count($format3Data),
            'counts_match' => (
                ($format1Totals['Excused'] ?? 0) === ($format2Totals['Total'] ?? 0) &&
                ($format1Totals['Excused'] ?? 0) === count($format3Data)
            ),

            // Breakdown by category
            'excused_by_category' => $excusedByCategory,

            // Company breakdown
            'companies' => $this->getCompanyBreakdown($format1Data),
        ];

        Log::info("📊 SUMMARY STATISTICS COMPLETED");
        Log::info("   Total Strength: {$statistics['total_strength']}");
        Log::info("   Total Excused: {$statistics['total_excused']}");
        Log::info("   Total Present: {$statistics['total_present']}");
        Log::info("   Counts Match: " . ($statistics['counts_match'] ? 'YES ✅' : 'NO ❌'));

        return $statistics;
    }

    /**
     * Get company-wise breakdown
     */
    private function getCompanyBreakdown($format1Data)
    {
        $companies = [];

        foreach ($format1Data as $row) {
            if ($row['company'] !== 'Total') {
                $companies[] = [
                    'name' => $row['company'],
                    'total' => $row['Total'] ?? 0,
                    'excused' => $row['Excused'] ?? 0,
                    'present' => $row['All Total'] ?? 0,
                ];
            }
        }

        return $companies;
    }

    /**
     * Get excused soldiers by company for Format 3
     */
    public function getFormat3DataByCompany($date)
    {
        Log::info("📝 GENERATING FORMAT3 DATA BY COMPANY for {$this->reportType} report on date: {$date}");

        $allExcusedSoldiers = $this->getFormat3Data($date);

        // Group by company
        $groupedByCompany = [];
        foreach ($allExcusedSoldiers as $soldier) {
            $companyName = $soldier['company'];

            if (!isset($groupedByCompany[$companyName])) {
                $groupedByCompany[$companyName] = [
                    'company_name' => $companyName,
                    'soldiers' => [],
                    'total_count' => 0,
                ];
            }

            $groupedByCompany[$companyName]['soldiers'][] = $soldier;
            $groupedByCompany[$companyName]['total_count']++;
        }

        // Sort by company name
        ksort($groupedByCompany);

        Log::info("📝 FORMAT3 BY COMPANY COMPLETED - " . count($groupedByCompany) . " companies");

        return array_values($groupedByCompany);
    }

    /**
     * Get excused soldiers by category for Format 3
     */
    public function getFormat3DataByCategory($date)
    {
        Log::info("📝 GENERATING FORMAT3 DATA BY CATEGORY for {$this->reportType} report on date: {$date}");

        $allExcusedSoldiers = $this->getFormat3Data($date);

        // Group by category
        $groupedByCategory = [];
        foreach ($allExcusedSoldiers as $soldier) {
            $category = $soldier['excusal_category'];

            if (!isset($groupedByCategory[$category])) {
                $groupedByCategory[$category] = [
                    'category_name' => $category,
                    'soldiers' => [],
                    'total_count' => 0,
                ];
            }

            $groupedByCategory[$category]['soldiers'][] = $soldier;
            $groupedByCategory[$category]['total_count']++;
        }

        // Sort by priority (lower priority number = higher priority)
        uasort($groupedByCategory, function ($a, $b) {
            $priorityA = $a['soldiers'][0]['priority'] ?? 999;
            $priorityB = $b['soldiers'][0]['priority'] ?? 999;
            return $priorityA - $priorityB;
        });

        Log::info("📝 FORMAT3 BY CATEGORY COMPLETED - " . count($groupedByCategory) . " categories");

        return array_values($groupedByCategory);
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
            'format3' => [
                'all_soldiers' => $this->getFormat3Data($date),
                'by_company' => $this->getFormat3DataByCompany($date),
                'by_category' => $this->getFormat3DataByCategory($date),
            ],
        ];
    }

    /**
     * Compare Format1 and Format2 totals for debugging
     */
    public function compareTotals($date)
    {
        Log::info("⚖️  COMPARING TOTALS for {$this->reportType} report on date: {$date}");

        $format1Data = $this->getFormat1Data($date);
        $format1Excused = end($format1Data)['Excused'] ?? 0;

        $format2Data = $this->getFormat2Data($date);
        $format2Total = end($format2Data)['Total'] ?? 0;

        $format3Data = $this->getFormat3Data($date);
        $format3Count = count($format3Data);

        $actualExcused = $this->getActualExcusedCount($date);

        Log::warning("📊 COMPARISON RESULTS:");
        Log::warning("   Format1 (Excused): {$format1Excused}");
        Log::warning("   Format2 (Total): {$format2Total}");
        Log::warning("   Format3 (Count): {$format3Count}");
        Log::warning("   Actual Excused: {$actualExcused}");

        if ($format1Excused === $format2Total && $format1Excused === $format3Count && $format1Excused === $actualExcused) {
            Log::info("🎉 SUCCESS: All counts match perfectly!");
        } else {
            Log::error("❌ MISMATCH DETECTED:");
            if ($format1Excused !== $format2Total) {
                Log::error("   Format1 ({$format1Excused}) ≠ Format2 ({$format2Total})");
            }
            if ($format1Excused !== $format3Count) {
                Log::error("   Format1 ({$format1Excused}) ≠ Format3 ({$format3Count})");
            }
            if ($format1Excused !== $actualExcused) {
                Log::error("   Format1 ({$format1Excused}) ≠ Actual ({$actualExcused})");
            }
        }

        return [
            'format1_excused' => $format1Excused,
            'format2_total' => $format2Total,
            'format3_count' => $format3Count,
            'actual_excused' => $actualExcused,
            'match' => ($format1Excused === $format2Total && $format1Excused === $format3Count && $format1Excused === $actualExcused)
        ];
    }
}
