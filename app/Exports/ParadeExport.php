<?php

namespace App\Exports;

use App\Models\Soldier;
use App\Models\SoldierServices;
use App\Models\Company;
use App\Models\Appointment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class ParadeExport implements FromCollection, WithHeadings, WithMapping
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        // Get all appointments
        $appointments = Appointment::pluck('name', 'id');

        // Get all companies
        $companies = Company::pluck('name', 'id');

        // Initialize data structure
        $data = [];

        // Fill the data structure
        foreach ($appointments as $appId => $appName) {
            $row = ['name' => $appName];

            $appointmentTotal = 0; // Initialize total for this appointment

            foreach ($companies as $companyId => $companyName) {
                // Count soldiers with this appointment and company who were active on the given date
                $count = Soldier::whereHas('services', function ($query) use ($appId) {
                    $query->where('appointment_id', $appId)
                        // ->where('appointment_type', 'current')
                        ->where('appointments_from_date', '<=', $this->date)
                        ->where(function ($q) {
                            $q->where('appointments_to_date', '>=', $this->date)
                                ->orWhereNull('appointments_to_date');
                        });
                })->where('company_id', $companyId)->count();

                $row[$companyName] = $count;
                $appointmentTotal += $count; // Add to appointment total
            }

            $row['total'] = $appointmentTotal; // Set the correct total
            $data[] = $row;
        }

        // Add total row
        $totalRow = ['name' => 'Total'];
        $grandTotal = 0; // Initialize grand total

        foreach ($companies as $companyId => $companyName) {
            $companyTotal = 0;
            foreach ($data as $row) {
                $companyTotal += $row[$companyName] ?? 0;
            }
            $totalRow[$companyName] = $companyTotal;
            $grandTotal += $companyTotal; // Add to grand total
        }

        $totalRow['total'] = $grandTotal; // Set the correct grand total
        $data[] = $totalRow;

        return new Collection($data);
    }

    public function headings(): array
    {
        // Get all companies
        $companies = Company::pluck('name')->toArray();

        $headings = ['Appointment Name'];
        foreach ($companies as $company) {
            $headings[] = $company;
        }
        $headings[] = 'Total';

        return $headings;
    }

    public function map($row): array
    {
        $mapped = [$row['name']];

        // Get all companies
        $companies = Company::pluck('name')->toArray();

        foreach ($companies as $company) {
            $mapped[] = $row[$company] ?? 0;
        }

        $mapped[] = $row['total'];

        return $mapped;
    }
}
