<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Rank;
use App\Models\CompanyRankManpower;
use App\Models\Soldier;
use App\Models\SoldierLeaveApplication;
use App\Models\LeaveType;
use App\Models\SoldierAbsent;
use App\Models\AbsentType;
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
        $absentTypes = AbsentType::active()->orderBy('id')->get();

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

        // Fetch soldiers with active leave OR in cadres/courses/ex_areas/att/cmds OR absent (without ERE)
        $leaveManpower = Soldier::selectRaw('soldiers.company_id, soldiers.rank_id, COUNT(DISTINCT soldiers.id) as count')
            ->leftJoin('soldier_leave_applications', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_leave_applications.soldier_id')
                    ->where('soldier_leave_applications.application_current_status', 'approved')
                    ->where('soldier_leave_applications.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_leave_applications.end_date')
                            ->orWhere('soldier_leave_applications.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldier_cadres', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_cadres.soldier_id')
                    ->where('soldier_cadres.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_cadres.end_date')
                            ->orWhere('soldier_cadres.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldier_courses', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_courses.soldier_id')
                    ->where('soldier_courses.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_courses.end_date')
                            ->orWhere('soldier_courses.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldier_ex_areas', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_ex_areas.soldier_id')
                    ->where('soldier_ex_areas.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_ex_areas.end_date')
                            ->orWhere('soldier_ex_areas.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldiers_att', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldiers_att.soldier_id')
                    ->where('soldiers_att.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldiers_att.end_date')
                            ->orWhere('soldiers_att.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldiers_cmds', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldiers_cmds.soldier_id')
                    ->where('soldiers_cmds.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldiers_cmds.end_date')
                            ->orWhere('soldiers_cmds.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldier_absent', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_absent.soldier_id')
                    ->where('soldier_absent.absent_current_status', 'approved')
                    ->where('soldier_absent.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_absent.end_date')
                            ->orWhere('soldier_absent.end_date', '>=', $currentDate);
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
            ->where(function ($query) {
                $query->whereNotNull('soldier_leave_applications.id')
                    ->orWhereNotNull('soldier_cadres.id')
                    ->orWhereNotNull('soldier_courses.id')
                    ->orWhereNotNull('soldier_ex_areas.id')
                    ->orWhereNotNull('soldiers_att.id')
                    ->orWhereNotNull('soldiers_cmds.id')
                    ->orWhereNotNull('soldier_absent.id');
            })
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

        // Fetch soldiers without active leave AND not in cadres/courses/ex_areas/att/cmds AND not absent (without ERE)
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
            ->leftJoin('soldier_cadres', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_cadres.soldier_id')
                    ->where('soldier_cadres.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_cadres.end_date')
                            ->orWhere('soldier_cadres.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldier_courses', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_courses.soldier_id')
                    ->where('soldier_courses.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_courses.end_date')
                            ->orWhere('soldier_courses.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldier_ex_areas', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_ex_areas.soldier_id')
                    ->where('soldier_ex_areas.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_ex_areas.end_date')
                            ->orWhere('soldier_ex_areas.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldiers_att', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldiers_att.soldier_id')
                    ->where('soldiers_att.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldiers_att.end_date')
                            ->orWhere('soldiers_att.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldiers_cmds', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldiers_cmds.soldier_id')
                    ->where('soldiers_cmds.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldiers_cmds.end_date')
                            ->orWhere('soldiers_cmds.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldier_absent', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_absent.soldier_id')
                    ->where('soldier_absent.absent_current_status', 'approved')
                    ->where('soldier_absent.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_absent.end_date')
                            ->orWhere('soldier_absent.end_date', '>=', $currentDate);
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
            ->whereNull('soldier_leave_applications.id') // No active leave
            ->whereNull('soldier_cadres.id') // Not in cadres
            ->whereNull('soldier_courses.id') // Not in courses
            ->whereNull('soldier_ex_areas.id') // Not in ex areas
            ->whereNull('soldiers_att.id') // Not in ATT
            ->whereNull('soldiers_cmds.id') // Not in CMDs
            ->whereNull('soldier_absent.id') // Not absent
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

        // Fetch absent type distribution data
        $absentTypeManpower = Soldier::selectRaw('soldiers.company_id, soldier_absent.absent_type_id, COUNT(*) as count')
            ->join('soldier_absent', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_absent.soldier_id')
                    ->where('soldier_absent.absent_current_status', 'approved')
                    ->where('soldier_absent.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_absent.end_date')
                            ->orWhere('soldier_absent.end_date', '>=', $currentDate);
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
            ->groupBy('soldiers.company_id', 'soldier_absent.absent_type_id')
            ->get()
            ->groupBy('company_id')
            ->map(function ($rows) {
                return $rows->keyBy('absent_type_id');
            });

        // Fetch additional soldier status data (cadres, courses, ex_areas, att, cmds)
        $additionalStatuses = $this->getAdditionalStatuses($currentDate, $companies);

        // Merge leave types with additional statuses and absent types for combined absent details
        $absentDetails = $this->mergeAbsentDetails($leaveTypes, $leaveTypeManpower, $absentTypes, $absentTypeManpower, $additionalStatuses, $companies, $leaveManpower);
        $absentSoldierDetails = $this->getAbsentSoldierDetails($currentDate, $companies);

        return [
            'companies' => $companies,
            'officerRanks' => $officerRanks,
            'otherRanks' => $otherRanks,
            'leaveTypes' => $leaveTypes,
            'absentTypes' => $absentTypes,
            'manpower' => $manpower,
            'officerTotals' => $officerTotals,
            'receivedManpower' => $receivedManpower,
            'receivedOfficerTotals' => $receivedOfficerTotals,
            'leaveManpower' => $leaveManpower,
            'leaveOfficerTotals' => $leaveOfficerTotals,
            'withoutLeaveManpower' => $withoutLeaveManpower,
            'withoutLeaveOfficerTotals' => $withoutLeaveOfficerTotals,
            'absentDetails' => $absentDetails,
            'absentSoldierDetails' => $absentSoldierDetails,

        ];
    }
    /**
     * Get detailed information about absent soldiers
     *
     * @param string $currentDate
     * @param \Illuminate\Support\Collection $companies
     * @return array
     */
    /**
     * Get detailed information about absent soldiers
     *
     * @param string $currentDate
     * @param \Illuminate\Support\Collection $companies
     * @return array
     */
    private function getAbsentSoldierDetails($currentDate, $companies)
    {
        // Create a base query with all necessary joins
        $baseQuery = Soldier::query()
            ->select([
                'soldiers.id',
                'soldiers.army_no',
                'soldiers.full_name',
                'soldiers.rank_id',
                'soldiers.company_id',
                'ranks.name as rank_name',
                'companies.name as company_name',
                \DB::raw('MAX(soldier_leave_applications.leave_type_id) as leave_type_id'),
                \DB::raw('MAX(leave_types.name) as leave_type_name'),
                \DB::raw('MAX(soldier_absent.absent_type_id) as absent_type_id'),
                \DB::raw('MAX(absent_types.name) as absent_type_name'),
                \DB::raw('MAX(soldier_cadres.id) as cadre_id'),
                \DB::raw('MAX(soldier_courses.id) as course_id'),
                \DB::raw('MAX(soldier_ex_areas.id) as ex_area_id'),
                \DB::raw('MAX(soldiers_att.id) as att_id'),
                \DB::raw('MAX(soldiers_cmds.id) as cmd_id')
            ])
            ->leftJoin('ranks', 'soldiers.rank_id', '=', 'ranks.id')
            ->leftJoin('companies', 'soldiers.company_id', '=', 'companies.id')
            // Leave applications
            ->leftJoin('soldier_leave_applications', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_leave_applications.soldier_id')
                    ->where('soldier_leave_applications.application_current_status', 'approved')
                    ->where('soldier_leave_applications.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_leave_applications.end_date')
                            ->orWhere('soldier_leave_applications.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('leave_types', 'soldier_leave_applications.leave_type_id', '=', 'leave_types.id')
            // Absent records
            ->leftJoin('soldier_absent', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_absent.soldier_id')
                    ->where('soldier_absent.absent_current_status', 'approved')
                    ->where('soldier_absent.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_absent.end_date')
                            ->orWhere('soldier_absent.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('absent_types', 'soldier_absent.absent_type_id', '=', 'absent_types.id')
            // Additional statuses
            ->leftJoin('soldier_cadres', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_cadres.soldier_id')
                    ->where('soldier_cadres.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_cadres.end_date')
                            ->orWhere('soldier_cadres.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldier_courses', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_courses.soldier_id')
                    ->where('soldier_courses.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_courses.end_date')
                            ->orWhere('soldier_courses.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldier_ex_areas', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldier_ex_areas.soldier_id')
                    ->where('soldier_ex_areas.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldier_ex_areas.end_date')
                            ->orWhere('soldier_ex_areas.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldiers_att', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldiers_att.soldier_id')
                    ->where('soldiers_att.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldiers_att.end_date')
                            ->orWhere('soldiers_att.end_date', '>=', $currentDate);
                    });
            })
            ->leftJoin('soldiers_cmds', function ($join) use ($currentDate) {
                $join->on('soldiers.id', '=', 'soldiers_cmds.soldier_id')
                    ->where('soldiers_cmds.start_date', '<=', $currentDate)
                    ->where(function ($query) use ($currentDate) {
                        $query->whereNull('soldiers_cmds.end_date')
                            ->orWhere('soldiers_cmds.end_date', '>=', $currentDate);
                    });
            })
            // Exclude ERE records
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
            ->where(function ($query) {
                $query->whereNotNull('soldier_leave_applications.id')
                    ->orWhereNotNull('soldier_absent.id')
                    ->orWhereNotNull('soldier_cadres.id')
                    ->orWhereNotNull('soldier_courses.id')
                    ->orWhereNotNull('soldier_ex_areas.id')
                    ->orWhereNotNull('soldiers_att.id')
                    ->orWhereNotNull('soldiers_cmds.id');
            })
            ->groupBy(
                'soldiers.id',
                'soldiers.army_no',
                'soldiers.full_name',
                'soldiers.rank_id',
                'soldiers.company_id',
                'ranks.name',
                'companies.name'
            )
            ->orderBy('companies.name')
            ->orderBy('ranks.id')
            ->orderBy('soldiers.army_no');

        $absentSoldiers = $baseQuery->get();

        // Process the data to determine absence reason
        $absentSoldiers->each(function ($soldier) {
            $soldier->absence_reason = $this->determineAbsenceReason($soldier);
        });

        return $absentSoldiers->groupBy('company_name');
    }

    /**
     * Determine the absence reason for a soldier
     *
     * @param \App\Models\Soldier $soldier
     * @return string
     */
    private function determineAbsenceReason($soldier)
    {
        if ($soldier->leave_type_name) {
            return $soldier->leave_type_name . ' Leave';
        }
        if ($soldier->absent_type_name) {
            return $soldier->absent_type_name . ' Absent';
        }
        if ($soldier->cadre_id) {
            return 'Cadre';
        }
        if ($soldier->course_id) {
            return 'Course';
        }
        if ($soldier->ex_area_id) {
            return 'Exercise Area';
        }
        if ($soldier->att_id) {
            return 'ATT';
        }
        if ($soldier->cmd_id) {
            return 'Comd';
        }

        return 'Unknown';
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
            'cadres' => [
                'label' => 'Cadres',
                'data' => $this->getStatusCount('soldier_cadres', $currentDate, $companies)
            ],
            'courses' => [
                'label' => 'Courses',
                'data' => $this->getStatusCount('soldier_courses', $currentDate, $companies)
            ],
            'ex_areas' => [
                'label' => 'Ex Areas',
                'data' => $this->getStatusCount('soldier_ex_areas', $currentDate, $companies)
            ],
            'att' => [
                'label' => 'ATT',
                'data' => $this->getStatusCount('soldiers_att', $currentDate, $companies)
            ],
            'cmds' => [
                'label' => 'Comd',
                'data' => $this->getStatusCount('soldiers_cmds', $currentDate, $companies)
            ],
        ];

        return $statuses;
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

    /**
     * Merge leave types with additional statuses and absent types for combined absent details
     *
     * @param \Illuminate\Support\Collection $leaveTypes
     * @param \Illuminate\Support\Collection $leaveTypeManpower
     * @param \Illuminate\Support\Collection $absentTypes
     * @param \Illuminate\Support\Collection $absentTypeManpower
     * @param array $additionalStatuses
     * @param \Illuminate\Support\Collection $companies
     * @param \Illuminate\Support\Collection $leaveManpower
     * @return array
     */
    private function mergeAbsentDetails($leaveTypes, $leaveTypeManpower, $absentTypes, $absentTypeManpower, $additionalStatuses, $companies, $leaveManpower = null)
    {
        $columns = [];
        $columnTotals = [];
        $companyData = [];

        // Build columns list first
        foreach ($leaveTypes as $leaveType) {
            $columns[] = [
                'id' => 'leave_' . $leaveType->id,
                'label' => $leaveType->name,
                'type' => 'leave',
                'type_id' => $leaveType->id,
            ];
        }

        foreach ($additionalStatuses as $key => $status) {
            $columns[] = [
                'id' => 'status_' . $key,
                'label' => $status['label'],
                'type' => 'status',
                'type_key' => $key,
            ];
        }

        foreach ($absentTypes as $absentType) {
            $columns[] = [
                'id' => 'absent_' . $absentType->id,
                'label' => $absentType->name,
                'type' => 'absent',
                'type_id' => $absentType->id,
            ];
        }

        // Get absent soldier details with priority-based categorization
        $absentDetails = $this->getAbsentSoldierDetails(now()->toDateString(), $companies);

        // Track counted soldiers to avoid double counting
        $countedSoldiers = [];  // soldier_id -> column_id

        // Initialize company data structure
        foreach ($companies as $company) {
            $companyData[$company->id] = [];
            foreach ($columns as $column) {
                $companyData[$company->id][$column['id']] = 0;
            }
        }

        // Process absent soldiers with priority: Leave > Absent Type > Status
        foreach ($absentDetails as $companyName => $soldiers) {
            foreach ($soldiers as $soldier) {
                // Skip if already counted
                if (isset($countedSoldiers[$soldier->id])) {
                    continue;
                }

                $company = $companies->firstWhere('name', $companyName);
                if (!$company) {
                    continue;
                }

                $soldierCounted = false;

                // Priority 1: Leave Type
                if ($soldier->leave_type_name && !$soldierCounted) {
                    $columnId = 'leave_' . $soldier->leave_type_id;
                    if (isset($companyData[$company->id][$columnId])) {
                        $companyData[$company->id][$columnId]++;
                        $countedSoldiers[$soldier->id] = $columnId;
                        $soldierCounted = true;
                    }
                }

                // Priority 2: Absent Type
                if ($soldier->absent_type_name && !$soldierCounted) {
                    $columnId = 'absent_' . $soldier->absent_type_id;
                    if (isset($companyData[$company->id][$columnId])) {
                        $companyData[$company->id][$columnId]++;
                        $countedSoldiers[$soldier->id] = $columnId;
                        $soldierCounted = true;
                    }
                }

                // Priority 3-7: Status (Cadres, Courses, Ex Areas, ATT, Comd)
                if (!$soldierCounted) {
                    if ($soldier->cadre_id) {
                        $columnId = 'status_cadres';
                        $companyData[$company->id][$columnId]++;
                        $countedSoldiers[$soldier->id] = $columnId;
                        $soldierCounted = true;
                    } elseif ($soldier->course_id) {
                        $columnId = 'status_courses';
                        $companyData[$company->id][$columnId]++;
                        $countedSoldiers[$soldier->id] = $columnId;
                        $soldierCounted = true;
                    } elseif ($soldier->ex_area_id) {
                        $columnId = 'status_ex_areas';
                        $companyData[$company->id][$columnId]++;
                        $countedSoldiers[$soldier->id] = $columnId;
                        $soldierCounted = true;
                    } elseif ($soldier->att_id) {
                        $columnId = 'status_att';
                        $companyData[$company->id][$columnId]++;
                        $countedSoldiers[$soldier->id] = $columnId;
                        $soldierCounted = true;
                    } elseif ($soldier->cmd_id) {
                        $columnId = 'status_cmds';
                        $companyData[$company->id][$columnId]++;
                        $countedSoldiers[$soldier->id] = $columnId;
                        $soldierCounted = true;
                    }
                }
            }
        }

        // Calculate column totals from companyData
        foreach ($columns as $column) {
            $total = 0;
            foreach ($companies as $company) {
                $total += $companyData[$company->id][$column['id']];
            }
            $columnTotals[$column['id']] = $total;
        }

        // Calculate company totals - use the leaveManpower data to avoid double counting
        $companyTotals = [];
        foreach ($companies as $company) {
            $total = 0;
            // Sum up the leaveManpower counts for this company (unique soldiers)
            if ($leaveManpower && isset($leaveManpower[$company->id])) {
                foreach ($leaveManpower[$company->id] as $rankData) {
                    $total += $rankData->count;
                }
            }
            $companyTotals[$company->id] = $total;
        }

        return [
            'columns' => $columns,
            'companyData' => $companyData,
            'columnTotals' => $columnTotals,
            'companyTotals' => $companyTotals,
        ];
    }
}
