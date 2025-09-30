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

        $companies = Company::active()->orderBy('name')->get();
        $ranks = Rank::active()->orderBy('name')->get();
        $leaveTypes = LeaveType::active()->orderBy('name')->get();

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

        // NEW: Fetch leave type distribution data
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
            ->whereNull('soldiers_ere.id') // Exclude soldiers with active ERE records
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
        ];
    }
}
