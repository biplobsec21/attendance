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
     * Check if a soldier is excused from attendance on a given date for the current report type
     */
    public function isSoldierExcused($soldier, $date)
    {
        Log::debug("🔍 Checking if soldier {$soldier->id} is excused for {$this->reportType} report on date: {$date}");

        // First check if soldier has ERE - if yes, they are automatically excused
        if ($this->hasActiveEreOnDate($soldier, $date)) {
            Log::debug("✅ Soldier {$soldier->id} has active ERE - AUTOMATICALLY EXCUSED");
            return true;
        }

        $carbonDate = Carbon::parse($date);
        $yesterday = $carbonDate->copy()->subDay();

        // Check all excusal conditions
        $excusalConditions = [
            'Active Duty' => fn() => $this->hasActiveDutyOnDate($soldier, $carbonDate),
            'Fixed Duty' => fn() => $this->hasFixedDuty($soldier),
            'Active Course' => fn() => $this->hasActiveCourseOnDate($soldier, $carbonDate),
            'Active Cadre' => fn() => $this->hasActiveCadreOnDate($soldier, $carbonDate),
            'Active Appointment' => fn() => $this->hasActiveAppointmentOnDate($soldier, $carbonDate),
            'Approved Leave' => fn() => $this->isOnApprovedLeaveOnDate($soldier, $carbonDate),
            'Excusal Duty Yesterday' => fn() => $this->hadExcusalDutyYesterday($soldier, $yesterday),
        ];

        foreach ($excusalConditions as $conditionName => $condition) {
            if ($condition()) {
                Log::debug("✅ Soldier {$soldier->id} is excused due to: {$conditionName}");
                return true;
            }
        }

        Log::debug("❌ Soldier {$soldier->id} is NOT excused - no conditions met");
        return false;
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

    // All other methods remain the same until getFormat1Data...

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

            // CORRECTED: Total - Excused = All Total
            $row['Total'] = $companyTotal;
            $row['Excused'] = $companyExcused;
            $row['All Total'] = $companyTotal - $companyExcused; // Changed from + to -

            $totals['total'] += $companyTotal;
            $totals['excused'] += $companyExcused;
            $totals['all_total'] += ($companyTotal - $companyExcused); // Changed from + to -

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

    // All other methods remain exactly the same as before...
    // Only the getFormat1Data method was modified

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
     * Get Format 2 data - Exclusion by Duty / Appointment Type (with unique soldier counting)
     */
    public function getFormat2Data($date)
    {
        Log::info("📋 GENERATING FORMAT2 DATA for {$this->reportType} report on date: {$date}");

        $carbonDate = Carbon::parse($date);
        $companies = Company::orderBy('name')->get();
        $companyNames = $companies->pluck('name')->toArray();
        $data = [];

        Log::debug("🏢 Processing exclusions for {$companies->count()} companies");

        // We'll track unique soldiers per category to avoid duplicates
        $categoryData = [];
        $allSoldierIds = collect();

        // Process Roster Duties
        Log::debug("🎯 Processing Roster Duties...");
        $rosterDuties = SoldierDuty::whereDate('assigned_date', $carbonDate)
            ->whereHas('soldier', function ($query) use ($date) {
                $this->excludeSoldiersWithActiveEre($query, $date);
            })
            ->with(['soldier.company', 'duty'])
            ->get();

        Log::debug("   Found {$rosterDuties->count()} roster duty records");

        $rosterGroups = $rosterDuties->groupBy('duty.duty_name');
        foreach ($rosterGroups as $dutyName => $duties) {
            $uniqueSoldiers = $duties->unique('soldier_id');
            $counts = array_fill_keys($companyNames, 0);

            foreach ($uniqueSoldiers as $duty) {
                if ($duty->soldier && $duty->soldier->company) {
                    $counts[$duty->soldier->company->name]++;
                }
            }

            $soldierIds = $uniqueSoldiers->pluck('soldier_id')->toArray();
            $allSoldierIds = $allSoldierIds->merge($soldierIds);

            $categoryData[] = [
                'category' => 'Roster Duties',
                'type' => $dutyName,
                'counts' => $counts,
                'soldier_ids' => $soldierIds
            ];

            Log::debug("   📝 Roster Duty '{$dutyName}': " . count($soldierIds) . " unique soldiers");
        }

        // Process Fixed Duties
        Log::debug("📌 Processing Fixed Duties...");
        $fixedDuties = DutyRank::where('assignment_type', 'fixed')
            ->whereHas('soldier', function ($query) use ($date) {
                $this->excludeSoldiersWithActiveEre($query, $date);
            })
            ->with(['soldier.company', 'duty'])
            ->get();

        Log::debug("   Found {$fixedDuties->count()} fixed duty records");

        $fixedGroups = $fixedDuties->groupBy('duty.duty_name');
        foreach ($fixedGroups as $dutyName => $duties) {
            $uniqueSoldiers = $duties->unique('soldier_id');
            $counts = array_fill_keys($companyNames, 0);

            foreach ($uniqueSoldiers as $duty) {
                if ($duty->soldier && $duty->soldier->company) {
                    $counts[$duty->soldier->company->name]++;
                }
            }

            $soldierIds = $uniqueSoldiers->pluck('soldier_id')->toArray();
            $allSoldierIds = $allSoldierIds->merge($soldierIds);

            $categoryData[] = [
                'category' => 'Fixed Duties',
                'type' => $dutyName,
                'counts' => $counts,
                'soldier_ids' => $soldierIds
            ];

            Log::debug("   📝 Fixed Duty '{$dutyName}': " . count($soldierIds) . " unique soldiers");
        }

        // Process Appointments
        Log::debug("💼 Processing Appointments...");
        $appointments = SoldierServices::where('status', 'active')
            ->whereDate('appointments_from_date', '<=', $carbonDate)
            ->where(function ($query) use ($carbonDate) {
                $query->whereNull('appointments_to_date')
                    ->orWhereDate('appointments_to_date', '>=', $carbonDate);
            })
            ->whereHas('soldier', function ($query) use ($date) {
                $this->excludeSoldiersWithActiveEre($query, $date);
            })
            ->with(['soldier.company'])
            ->get();

        Log::debug("   Found {$appointments->count()} appointment records");

        $appointmentGroups = $appointments->groupBy('appointments_name');
        foreach ($appointmentGroups as $serviceName => $services) {
            $uniqueSoldiers = $services->unique('soldier_id');
            $counts = array_fill_keys($companyNames, 0);

            foreach ($uniqueSoldiers as $service) {
                if ($service->soldier && $service->soldier->company) {
                    $counts[$service->soldier->company->name]++;
                }
            }

            $soldierIds = $uniqueSoldiers->pluck('soldier_id')->toArray();
            $allSoldierIds = $allSoldierIds->merge($soldierIds);

            $categoryData[] = [
                'category' => 'Appointments',
                'type' => $serviceName,
                'counts' => $counts,
                'soldier_ids' => $soldierIds
            ];

            Log::debug("   📝 Appointment '{$serviceName}': " . count($soldierIds) . " unique soldiers");
        }

        // Process Courses
        Log::debug("📚 Processing Courses...");
        $courses = SoldierCourse::where('status', 'active')
            ->whereDate('start_date', '<=', $carbonDate)
            ->where(function ($query) use ($carbonDate) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $carbonDate);
            })
            ->whereHas('soldier', function ($query) use ($date) {
                $this->excludeSoldiersWithActiveEre($query, $date);
            })
            ->with(['soldier.company', 'course'])
            ->get();

        Log::debug("   Found {$courses->count()} course records");

        $courseGroups = $courses->groupBy('course.name');
        foreach ($courseGroups as $courseName => $courseRecords) {
            $uniqueSoldiers = $courseRecords->unique('soldier_id');
            $counts = array_fill_keys($companyNames, 0);

            foreach ($uniqueSoldiers as $courseRecord) {
                if ($courseRecord->soldier && $courseRecord->soldier->company) {
                    $counts[$courseRecord->soldier->company->name]++;
                }
            }

            $soldierIds = $uniqueSoldiers->pluck('soldier_id')->toArray();
            $allSoldierIds = $allSoldierIds->merge($soldierIds);

            $categoryData[] = [
                'category' => 'Courses',
                'type' => $courseName,
                'counts' => $counts,
                'soldier_ids' => $soldierIds
            ];

            Log::debug("   📝 Course '{$courseName}': " . count($soldierIds) . " unique soldiers");
        }

        // Process Cadres
        Log::debug("👥 Processing Cadres...");
        $cadres = SoldierCadre::where('status', 'active')
            ->whereDate('start_date', '<=', $carbonDate)
            ->where(function ($query) use ($carbonDate) {
                $query->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $carbonDate);
            })
            ->whereHas('soldier', function ($query) use ($date) {
                $this->excludeSoldiersWithActiveEre($query, $date);
            })
            ->with(['soldier.company', 'cadre'])
            ->get();

        Log::debug("   Found {$cadres->count()} cadre records");

        $cadreGroups = $cadres->groupBy('cadre.name');
        foreach ($cadreGroups as $cadreName => $cadreRecords) {
            $uniqueSoldiers = $cadreRecords->unique('soldier_id');
            $counts = array_fill_keys($companyNames, 0);

            foreach ($uniqueSoldiers as $cadreRecord) {
                if ($cadreRecord->cadre && $cadreRecord->soldier && $cadreRecord->soldier->company) {
                    $counts[$cadreRecord->soldier->company->name]++;
                }
            }

            $soldierIds = $uniqueSoldiers->pluck('soldier_id')->toArray();
            $allSoldierIds = $allSoldierIds->merge($soldierIds);

            $categoryData[] = [
                'category' => 'Cadres',
                'type' => $cadreName,
                'counts' => $counts,
                'soldier_ids' => $soldierIds
            ];

            Log::debug("   📝 Cadre '{$cadreName}': " . count($soldierIds) . " unique soldiers");
        }

        // Process Leaves
        Log::debug("🏖️  Processing Leaves...");
        $leaves = LeaveApplication::where('application_current_status', 'approved')
            ->whereDate('start_date', '<=', $carbonDate)
            ->whereDate('end_date', '>=', $carbonDate)
            ->whereHas('soldier', function ($query) use ($date) {
                $this->excludeSoldiersWithActiveEre($query, $date);
            })
            ->with(['soldier.company', 'leaveType'])
            ->get();

        Log::debug("   Found {$leaves->count()} leave records");

        $leaveGroups = $leaves->groupBy(function ($leave) {
            return $leave->leaveType->name ?? 'Leave';
        });

        foreach ($leaveGroups as $leaveTypeName => $leaveRecords) {
            $uniqueSoldiers = $leaveRecords->unique('soldier_id');
            $counts = array_fill_keys($companyNames, 0);

            foreach ($uniqueSoldiers as $leaveRecord) {
                if ($leaveRecord->soldier && $leaveRecord->soldier->company) {
                    $counts[$leaveRecord->soldier->company->name]++;
                }
            }

            $soldierIds = $uniqueSoldiers->pluck('soldier_id')->toArray();
            $allSoldierIds = $allSoldierIds->merge($soldierIds);

            $categoryData[] = [
                'category' => 'Leave',
                'type' => $leaveTypeName,
                'counts' => $counts,
                'soldier_ids' => $soldierIds
            ];

            Log::debug("   📝 Leave '{$leaveTypeName}': " . count($soldierIds) . " unique soldiers");
        }

        // Build final data rows
        Log::debug("📊 Building final data rows...");
        foreach ($categoryData as $category) {
            $row = [
                'category' => $category['category'],
                'type' => $category['type'],
            ];

            foreach ($companies as $company) {
                $row[$company->name] = $category['counts'][$company->name];
            }

            $row['Total'] = array_sum($category['counts']);
            $data[] = $row;
        }

        // Add totals row - count unique soldiers across all categories
        $uniqueExcusedSoldiers = $allSoldierIds->unique();

        Log::debug("🔢 Calculating unique totals:");
        Log::debug("   Raw soldier IDs count: " . $allSoldierIds->count());
        Log::debug("   Unique soldier IDs count: " . $uniqueExcusedSoldiers->count());
        Log::debug("   Duplicate entries: " . ($allSoldierIds->count() - $uniqueExcusedSoldiers->count()));

        // Get company distribution of unique excused soldiers
        $uniqueSoldiersData = Soldier::whereIn('id', $uniqueExcusedSoldiers)
            ->with('company')
            ->get()
            ->groupBy('company.name');

        $totals = array_fill_keys($companyNames, 0);
        foreach ($uniqueSoldiersData as $companyName => $soldiers) {
            if (in_array($companyName, $companyNames)) {
                $totals[$companyName] = $soldiers->count();
                Log::debug("   📍 Company {$companyName}: {$soldiers->count()} unique excused soldiers");
            }
        }

        $totalRow = [
            'category' => 'Total',
            'type' => '',
        ];

        foreach ($companies as $company) {
            $totalRow[$company->name] = $totals[$company->name];
        }

        $totalRow['Total'] = $uniqueExcusedSoldiers->count();
        $data[] = $totalRow;

        Log::info("📋 FORMAT2 COMPLETED - Total unique excused soldiers: {$totalRow['Total']}");
        Log::info("📋 FORMAT2 BREAKDOWN - Raw entries: {$allSoldierIds->count()}, Unique: {$uniqueExcusedSoldiers->count()}, Duplicates: " . ($allSoldierIds->count() - $uniqueExcusedSoldiers->count()));

        return $data;
    }

    /**
     * NEW METHOD: Get actual unique excused count for verification
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
     * NEW METHOD: Compare Format1 and Format2 totals for debugging
     */
    public function compareTotals($date)
    {
        Log::info("⚖️  COMPARING TOTALS for {$this->reportType} report on date: {$date}");

        $format1Data = $this->getFormat1Data($date);
        $format1Excused = end($format1Data)['Excused'] ?? 0;

        $format2Data = $this->getFormat2Data($date);
        $format2Total = end($format2Data)['Total'] ?? 0;

        $actualExcused = $this->getActualExcusedCount($date);

        Log::warning("📊 COMPARISON RESULTS:");
        Log::warning("   Format1 (Excused): {$format1Excused}");
        Log::warning("   Format2 (Total): {$format2Total}");
        Log::warning("   Actual Excused: {$actualExcused}");

        if ($format1Excused === $format2Total && $format1Excused === $actualExcused) {
            Log::info("🎉 SUCCESS: All counts match!");
        } else {
            Log::error("❌ MISMATCH DETECTED:");
            if ($format1Excused !== $format2Total) {
                Log::error("   Format1 ({$format1Excused}) ≠ Format2 ({$format2Total})");
            }
            if ($format1Excused !== $actualExcused) {
                Log::error("   Format1 ({$format1Excused}) ≠ Actual ({$actualExcused})");
            }
        }

        return [
            'format1_excused' => $format1Excused,
            'format2_total' => $format2Total,
            'actual_excused' => $actualExcused,
            'match' => ($format1Excused === $format2Total && $format1Excused === $actualExcused)
        ];
    }
}
