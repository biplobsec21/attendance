<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Rank;
use App\Models\CompanyRankManpower;
use App\Models\Soldier;
use App\Models\SoldierLeaveApplication;
use App\Models\LeaveType;
use Carbon\Carbon;

class ManpowerDataService
{
    /**
     * Get all manpower data for exports
     *
     * @param string $date
     * @return array
     */
    public function getManpowerData($date = null)
    {
        // Use provided date or default to current date
        $currentDate = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();

        $companies = Company::active()->orderBy('id')->get();
        $ranks = Rank::active()->orderBy('id')->get();
        $leaveTypes = LeaveType::active()->orderBy('id')->get();

        // Separate officer ranks from other ranks
        $officerRanks = $ranks->filter(function ($rank) {
            return $rank->type === 'OFFICER';
        });

        $otherRanks = $ranks->filter(function ($rank) {
            return $rank->type !== 'OFFICER';
        });

        // Fetch existing manpower data (planned distribution)
        $manpower = CompanyRankManpower::get()
            ->groupBy('company_id')
            ->map(function ($rows) {
                return $rows->keyBy('rank_id');
            });

        // Calculate officer totals for each company (planned)
        $officerTotals = [];
        foreach ($companies as $company) {
            $officerTotal = 0;
            foreach ($officerRanks as $rank) {
                if (isset($manpower[$company->id][$rank->id])) {
                    $officerTotal += $manpower[$company->id][$rank->id]->manpower_number;
                }
            }
            $officerTotals[$company->id] = $officerTotal;
        }

        // Fetch received manpower data (actual soldiers without active ERE records)
        $receivedManpower = Soldier::selectRaw('soldiers.company_id, soldiers.rank_id, COUNT(*) as count')
            ->leftJoin('soldiers_ere', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldiers_ere.soldier_id')
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldiers_ere.end_date')
                            ->orWhere('soldiers_ere.end_date', '>=', $currentDate);
                    })
                    ->where('soldiers_ere.start_date', '<=', $currentDate);
            })
            ->whereIn('soldiers.company_id', $companies->pluck('id'))
            ->whereIn('soldiers.rank_id', $ranks->pluck('id'))
            ->whereNull('soldiers_ere.id') // Only count soldiers without active ERE records
            ->groupBy('soldiers.company_id', 'soldiers.rank_id')
            ->get()
            ->groupBy('company_id')
            ->map(function ($rows) {
                return $rows->keyBy('rank_id');
            });

        // Calculate officer totals for received manpower
        $receivedOfficerTotals = [];
        foreach ($companies as $company) {
            $officerTotal = 0;
            foreach ($officerRanks as $rank) {
                if (isset($receivedManpower[$company->id][$rank->id])) {
                    $officerTotal += $receivedManpower[$company->id][$rank->id]->count;
                }
            }
            $receivedOfficerTotals[$company->id] = $officerTotal;
        }

        // Fetch soldiers with active leave (without ERE)
        $leaveManpower = Soldier::selectRaw('soldiers.company_id, soldiers.rank_id, COUNT(*) as count')
            ->join('soldier_leave_applications', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_leave_applications.soldier_id')
                    ->where('soldier_leave_applications.application_current_status', 'approved')
                    ->where('soldier_leave_applications.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_leave_applications.end_date')
                            ->orWhere('soldier_leave_applications.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldiers_ere', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldiers_ere.soldier_id')
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldiers_ere.end_date')
                            ->orWhere('soldiers_ere.end_date', '>=', $currentDate);
                    })
                    ->where('soldiers_ere.start_date', '<=', $currentDate);
            })
            ->whereIn('soldiers.company_id', $companies->pluck('id'))
            ->whereIn('soldiers.rank_id', $ranks->pluck('id'))
            ->whereNull('soldiers_ere.id') // Exclude soldiers with active ERE records
            ->groupBy('soldiers.company_id', 'soldiers.rank_id')
            ->get()
            ->groupBy('company_id')
            ->map(function ($rows) {
                return $rows->keyBy('rank_id');
            });

        // Calculate officer totals for leave manpower
        $leaveOfficerTotals = [];
        foreach ($companies as $company) {
            $officerTotal = 0;
            foreach ($officerRanks as $rank) {
                if (isset($leaveManpower[$company->id][$rank->id])) {
                    $officerTotal += $leaveManpower[$company->id][$rank->id]->count;
                }
            }
            $leaveOfficerTotals[$company->id] = $officerTotal;
        }

        // Fetch soldiers without active leave (without ERE)
        $withoutLeaveManpower = Soldier::selectRaw('soldiers.company_id, soldiers.rank_id, COUNT(*) as count')
            ->leftJoin('soldier_leave_applications', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_leave_applications.soldier_id')
                    ->where('soldier_leave_applications.application_current_status', 'approved')
                    ->where('soldier_leave_applications.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_leave_applications.end_date')
                            ->orWhere('soldier_leave_applications.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldiers_ere', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldiers_ere.soldier_id')
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldiers_ere.end_date')
                            ->orWhere('soldiers_ere.end_date', '>=', $currentDate);
                    })
                    ->where('soldiers_ere.start_date', '<=', $currentDate);
            })
            ->whereIn('soldiers.company_id', $companies->pluck('id'))
            ->whereIn('soldiers.rank_id', $ranks->pluck('id'))
            ->whereNull('soldiers_ere.id') // Exclude soldiers with active ERE records
            ->whereNull('soldier_leave_applications.id') // Only count soldiers without active leave
            ->groupBy('soldiers.company_id', 'soldiers.rank_id')
            ->get()
            ->groupBy('company_id')
            ->map(function ($rows) {
                return $rows->keyBy('rank_id');
            });

        // Calculate officer totals for without leave manpower
        $withoutLeaveOfficerTotals = [];
        foreach ($companies as $company) {
            $officerTotal = 0;
            foreach ($officerRanks as $rank) {
                if (isset($withoutLeaveManpower[$company->id][$rank->id])) {
                    $officerTotal += $withoutLeaveManpower[$company->id][$rank->id]->count;
                }
            }
            $withoutLeaveOfficerTotals[$company->id] = $officerTotal;
        }

        // Fetch leave type distribution data
        $leaveTypeManpower = Soldier::selectRaw('soldiers.company_id, soldier_leave_applications.leave_type_id, COUNT(*) as count')
            ->join('soldier_leave_applications', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_leave_applications.soldier_id')
                    ->where('soldier_leave_applications.application_current_status', 'approved')
                    ->where('soldier_leave_applications.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_leave_applications.end_date')
                            ->orWhere('soldier_leave_applications.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldiers_ere', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldiers_ere.soldier_id')
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldiers_ere.end_date')
                            ->orWhere('soldiers_ere.end_date', '>=', $currentDate);
                    })
                    ->where('soldiers_ere.start_date', '<=', $currentDate);
            })
            ->whereIn('soldiers.company_id', $companies->pluck('id'))
            ->whereNull('soldiers_ere.id')
            ->groupBy('soldiers.company_id', 'soldier_leave_applications.leave_type_id')
            ->get()
            ->groupBy('company_id')
            ->map(function ($rows) {
                return $rows->keyBy('leave_type_id');
            });

        // Calculate totals for each leave type
        $leaveTypeTotals = [];
        foreach ($leaveTypes as $leaveType) {
            $total = 0;
            foreach ($companies as $company) {
                if (isset($leaveTypeManpower[$company->id][$leaveType->id])) {
                    $total += $leaveTypeManpower[$company->id][$leaveType->id]->count;
                }
            }
            $leaveTypeTotals[$leaveType->id] = $total;
        }

        // Calculate company totals for leave types
        $leaveTypeCompanyTotals = [];
        foreach ($companies as $company) {
            $total = 0;
            foreach ($leaveTypes as $leaveType) {
                if (isset($leaveTypeManpower[$company->id][$leaveType->id])) {
                    $total += $leaveTypeManpower[$company->id][$leaveType->id]->count;
                }
            }
            $leaveTypeCompanyTotals[$company->id] = $total;
        }

        // Fetch additional soldier status data
        $additionalStatuses = $this->getAdditionalStatuses($currentDate, $companies);

        return [
            'companies' => $companies,
            'officerRanks' => $officerRanks,
            'otherRanks' => $otherRanks,
            'leaveTypes' => $leaveTypes,
            'manpower' => $manpower,
            'officerTotals' => $officerTotals,
            'receivedManpower' => $receivedManpower,
            'receivedOfficerTotals' => $receivedOfficerTotals,
            'leaveManpower' => $leaveManpower,
            'leaveOfficerTotals' => $leaveOfficerTotals,
            'withoutLeaveManpower' => $withoutLeaveManpower,
            'withoutLeaveOfficerTotals' => $withoutLeaveOfficerTotals,
            'leaveTypeManpower' => $leaveTypeManpower,
            'leaveTypeTotals' => $leaveTypeTotals,
            'leaveTypeCompanyTotals' => $leaveTypeCompanyTotals,
            'additionalStatuses' => $additionalStatuses,
        ];
    }

    /**
     * Fetch additional soldier statuses (cadres, courses, ex_areas, att, cmds)
     *
     * @param string $currentDate
     * @param \Illuminate\Support\Collection $companies
     * @return array
     */
    private function getAdditionalStatuses($currentDate, $companies)
    {
        $statuses = [
            'cadres' => $this->getStatusCount('soldier_cadres', $currentDate, $companies),
            'courses' => $this->getStatusCount('soldier_courses', $currentDate, $companies),
            'ex_areas' => $this->getStatusCount('soldier_ex_areas', $currentDate, $companies),
            'att' => $this->getStatusCount('soldiers_att', $currentDate, $companies),
            'cmds' => $this->getStatusCount('soldiers_cmds', $currentDate, $companies),
        ];

        // Calculate totals for each status type
        $statusTotals = [];
        foreach ($statuses as $statusType => $statusData) {
            $total = 0;
            foreach ($companies as $company) {
                if (isset($statusData[$company->id])) {
                    $total += $statusData[$company->id]->count ?? 0;
                }
            }
            $statusTotals[$statusType] = $total;
        }

        // Calculate company totals across all statuses
        $companyTotals = [];
        foreach ($companies as $company) {
            $total = 0;
            foreach ($statuses as $statusData) {
                if (isset($statusData[$company->id])) {
                    $total += $statusData[$company->id]->count ?? 0;
                }
            }
            $companyTotals[$company->id] = $total;
        }

        return [
            'data' => $statuses,
            'totals' => $statusTotals,
            'companyTotals' => $companyTotals,
            'labels' => [
                'cadres' => 'Cadres',
                'courses' => 'Courses',
                'ex_areas' => 'Ex Areas',
                'att' => 'ATT',
                'cmds' => 'CMDs',
            ],
        ];
    }

    /**
     * Get soldier count for a specific status table
     *
     * @param string $tableName
     * @param string $currentDate
     * @param \Illuminate\Support\Collection $companies
     * @return \Illuminate\Support\Collection
     */
    private function getStatusCount($tableName, $currentDate, $companies)
    {
        return Soldier::selectRaw('soldiers.company_id, COUNT(*) as count')
            ->join($tableName, function ($join) use ($currentDate, $tableName) {
                $join->on('soldiers.id', '=', $tableName . '.soldier_id')
                    ->where($tableName . '.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate, $tableName) {
                        $query->whereNull($tableName . '.end_date')
                            ->orWhere($tableName . '.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldiers_ere', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldiers_ere.soldier_id')
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldiers_ere.end_date')
                            ->orWhere('soldiers_ere.end_date', '>=', $currentDate);
                    })
                    ->where('soldiers_ere.start_date', '<=', $currentDate);
            })
            ->whereIn('soldiers.company_id', $companies->pluck('id'))
            ->whereNull('soldiers_ere.id') // Exclude soldiers with active ERE records
            ->groupBy('soldiers.company_id')
            ->get()
            ->keyBy('company_id');
    }
}
