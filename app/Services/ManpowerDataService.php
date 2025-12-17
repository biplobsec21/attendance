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

        // Get detailed information about absent soldiers (must be called before mergeAbsentDetails)
        $absentSoldierDetails = $this->getAbsentSoldierDetails($currentDate, $companies);

        // Merge leave types with additional statuses and absent types for combined absent details
        // $absentDetails = $this->mergeAbsentDetails($leaveTypes, $leaveTypeManpower, $absentTypes, $absentTypeManpower, $additionalStatuses, $companies, $leaveManpower);
        $absentDetails = $this->mergeAbsentDetails($leaveTypes, $leaveTypeManpower, $absentTypes, $absentTypeManpower, $additionalStatuses, $companies, $absentSoldierDetails);

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
            // this is ATT removed as per request ---- ....---
            // 'att' => [
            //     'label' => 'ATT',
            //     'data' => $this->getStatusCount('soldiers_att', $currentDate, $companies)
            // ],
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
    private function mergeAbsentDetails($leaveTypes, $leaveTypeManpower, $absentTypes, $absentTypeManpower, $additionalStatuses, $companies, $absentSoldierDetails)
    {
        $columns = [];
        $columnTotals = [];
        $companyData = [];

        // Initialize arrays for all companies
        foreach ($companies as $company) {
            $companyData[$company->id] = [];
        }

        // Build columns in the correct order
        // 1. Leave types
        foreach ($leaveTypes as $leaveType) {
            $columns[] = [
                'id' => 'leave_' . $leaveType->id,
                'label' => $leaveType->name,
                'type' => 'leave',
                'type_id' => $leaveType->id,
            ];

            // Initialize column totals
            $columnTotals['leave_' . $leaveType->id] = 0;

            // Initialize company data
            foreach ($companies as $company) {
                $companyData[$company->id]['leave_' . $leaveType->id] = 0;
            }
        }

        // 2. Additional statuses
        foreach ($additionalStatuses as $key => $status) {
            $columns[] = [
                'id' => 'status_' . $key,
                'label' => $status['label'],
                'type' => 'status',
                'type_key' => $key,
            ];

            $columnTotals['status_' . $key] = 0;

            foreach ($companies as $company) {
                $companyData[$company->id]['status_' . $key] = 0;
            }
        }

        // 3. Absent types
        foreach ($absentTypes as $absentType) {
            $columns[] = [
                'id' => 'absent_' . $absentType->id,
                'label' => $absentType->name,
                'type' => 'absent',
                'type_id' => $absentType->id,
            ];

            $columnTotals['absent_' . $absentType->id] = 0;

            foreach ($companies as $company) {
                $companyData[$company->id]['absent_' . $absentType->id] = 0;
            }
        }

        // Calculate company totals and category breakdown from the absent soldier details
        $companyTotals = [];

        // Initialize company totals
        foreach ($companies as $company) {
            $companyTotals[$company->id] = 0;
        }

        // Process absent soldier details to categorize each soldier
        foreach ($absentSoldierDetails as $companyName => $soldiers) {
            // Find the company ID
            $company = $companies->firstWhere('name', $companyName);
            if (!$company) continue;

            $companyId = $company->id;
            $companyTotals[$companyId] = count($soldiers);

            // Categorize each soldier
            foreach ($soldiers as $soldier) {
                $reason = $soldier->absence_reason;

                // Parse the reason to determine category
                if (str_contains($reason, 'Leave')) {
                    // Extract leave type name (e.g., "P Lve" from "P Lve Leave")
                    $leaveTypeName = trim(str_replace('Leave', '', $reason));

                    // Find matching leave type
                    $leaveType = $leaveTypes->firstWhere('name', $leaveTypeName);
                    if ($leaveType) {
                        $companyData[$companyId]['leave_' . $leaveType->id]++;
                        $columnTotals['leave_' . $leaveType->id]++;
                    }
                } elseif (str_contains($reason, 'Absent')) {
                    // Extract absent type name (e.g., "403 BG" from "403 BG Absent")
                    $absentTypeName = trim(str_replace('Absent', '', $reason));

                    // Find matching absent type
                    $absentType = $absentTypes->firstWhere('name', $absentTypeName);
                    if ($absentType) {
                        $companyData[$companyId]['absent_' . $absentType->id]++;
                        $columnTotals['absent_' . $absentType->id]++;
                    }
                } else {
                    // It's an additional status (Cadre, Course, etc.)
                    $statusKey = $this->mapReasonToStatusKey($reason);
                    if ($statusKey) {
                        $companyData[$companyId]['status_' . $statusKey]++;
                        $columnTotals['status_' . $statusKey]++;
                    }
                }
            }
        }

        // Validate that the sum of categories equals the total for each company
        $this->validateCategorySums($companies, $companyData, $companyTotals);

        return [
            'columns' => $columns,
            'companyData' => $companyData,
            'columnTotals' => $columnTotals,
            'companyTotals' => $companyTotals,
        ];
    }
    /**
     * Map reason string to status key
     */
    private function mapReasonToStatusKey($reason)
    {
        $mapping = [
            'Cadre' => 'cadres',
            'Course' => 'courses',
            'Exercise Area' => 'ex_areas',
            'ATT' => 'att',
            'Comd' => 'cmds',
        ];

        return $mapping[$reason] ?? null;
    }

    /**
     * Validate that category sums match company totals
     */
    private function validateCategorySums($companies, $companyData, $companyTotals)
    {
        foreach ($companies as $company) {
            $companyId = $company->id;
            $categorySum = 0;

            if (isset($companyData[$companyId])) {
                foreach ($companyData[$companyId] as $count) {
                    $categorySum += $count;
                }
            }

            $distinctTotal = $companyTotals[$companyId] ?? 0;

            if ($categorySum != $distinctTotal) {
                \Log::warning("Company {$company->name}: Category sum ({$categorySum}) doesn't match distinct total ({$distinctTotal})");
            }
        }
    }
}
