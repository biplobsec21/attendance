<?php

namespace App\Exports;

use App\Models\Soldier;
use App\Models\SoldierServices;
use App\Models\Company;
use App\Models\Rank;
use App\Models\Appointment;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class ParadePdfExport implements FromView, WithTitle
{
    protected $date;
    protected $companies;
    protected $rankTypes;
    protected $appointments;

    public function __construct($date)
    {
        $this->date = $date;
        $this->companies = Company::orderBy('name')->pluck('name', 'id');
        $this->rankTypes = Rank::distinct()->pluck('type')->sort();
        $this->appointments = Appointment::orderBy('name')->pluck('name', 'id');
    }

    public function view(): View
    {
        $companyRankData = [];

        // Fill the data structure for each company and rank type
        foreach ($this->companies as $companyId => $companyName) {
            $row = ['company_name' => $companyName];

            // For each rank type, get the count of soldiers
            foreach ($this->rankTypes as $rankType) {
                // Count total soldiers in this company and rank type
                $totalSoldiers = Soldier::where('company_id', $companyId)
                    ->whereHas('rank', function ($query) use ($rankType) {
                        $query->where('type', $rankType);
                    })
                    ->count();

                $row[$rankType] = $totalSoldiers;
            }

            // Calculate total soldiers for this company (across all rank types)
            $totalForCompany = Soldier::where('company_id', $companyId)->count();
            $row['total'] = $totalForCompany;

            // Count appointed soldiers (who have an appointment on the given date)
            $appointedSoldiers = Soldier::where('company_id', $companyId)
                ->whereHas('services', function ($query) {
                    $query->where('appointments_from_date', '<=', $this->date)
                        ->where(function ($q) {
                            $q->where('appointments_to_date', '>=', $this->date)
                                ->orWhereNull('appointments_to_date');
                        });
                })
                ->count();
            $row['appointed'] = $appointedSoldiers;

            // Calculate final total (total soldiers - appointed soldiers)
            $finalTotal = $totalForCompany - $appointedSoldiers;
            $row['final_total'] = $finalTotal;

            $companyRankData[] = $row;
        }

        // Add a total row (summing all companies)
        $totalRow = ['company_name' => 'Total'];
        foreach ($this->rankTypes as $rankType) {
            $rankTotal = Soldier::whereHas('rank', function ($query) use ($rankType) {
                $query->where('type', $rankType);
            })->count();
            $totalRow[$rankType] = $rankTotal;
        }

        $grandTotalSoldiers = Soldier::count();
        $totalRow['total'] = $grandTotalSoldiers;

        $grandAppointedSoldiers = Soldier::whereHas('services', function ($query) {
            $query->where('appointments_from_date', '<=', $this->date)
                ->where(function ($q) {
                    $q->where('appointments_to_date', '>=', $this->date)
                        ->orWhereNull('appointments_to_date');
                });
        })->count();
        $totalRow['appointed'] = $grandAppointedSoldiers;

        $grandFinalTotal = $grandTotalSoldiers - $grandAppointedSoldiers;
        $totalRow['final_total'] = $grandFinalTotal;

        $companyRankData[] = $totalRow;

        // Get Parade data
        $paradeData = [];

        // Fill the data structure
        foreach ($this->appointments as $appId => $appName) {
            $row = ['appointment_name' => $appName];

            foreach ($this->companies as $companyId => $companyName) {
                // Count soldiers with this appointment and company who were active on the given date
                $count = Soldier::whereHas('services', function ($query) use ($appId) {
                    $query->where('appointment_id', $appId)
                        ->where('appointments_from_date', '<=', $this->date)
                        ->where(function ($q) {
                            $q->where('appointments_to_date', '>=', $this->date)
                                ->orWhereNull('appointments_to_date');
                        });
                })->where('company_id', $companyId)->count();

                $row[$companyName] = $count;
            }

            // Calculate total for this appointment - FIXED
            $appointmentTotal = 0;
            foreach ($this->companies as $companyName) {
                $appointmentTotal += $row[$companyName] ?? 0;
            }
            $row['total'] = $appointmentTotal;

            $paradeData[] = $row;
        }

        // Add total row - FIXED
        $totalRow = ['appointment_name' => 'Total'];
        foreach ($this->companies as $companyId => $companyName) {
            $companyTotal = 0;
            foreach ($paradeData as $row) {
                $companyTotal += $row[$companyName] ?? 0;
            }
            $totalRow[$companyName] = $companyTotal;
        }

        // Calculate grand total for the appointment report - FIXED
        $grandTotal = 0;
        foreach ($this->companies as $companyName) {
            $grandTotal += $totalRow[$companyName] ?? 0;
        }
        $totalRow['total'] = $grandTotal;

        $paradeData[] = $totalRow;

        // Format the date for the title
        $formattedDate = date('d M Y', strtotime($this->date));

        return view('exports.parade_report', [
            'companyRankData' => $companyRankData,
            'paradeData' => $paradeData,
            'rankTypes' => $this->rankTypes,
            'companies' => $this->companies->values(), // Use values() to get just the company names
            'formattedDate' => $formattedDate,
            'date' => $this->date
        ]);
    }

    public function title(): string
    {
        return 'Parade Report';
    }
}
