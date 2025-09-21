<?php

namespace App\Exports;

use App\Models\Soldier;
use App\Models\SoldierServices;
use App\Models\Company;
use App\Models\Rank;
use App\Models\Appointment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class CombinedSingleSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithEvents
{
    protected $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    public function collection()
    {
        // Get Company and Rank data
        $companies = Company::orderBy('name')->pluck('name', 'id');
        $rankTypes = Rank::distinct()->pluck('type')->sort();

        $companyRankData = [];

        // Fill the data structure for each company and rank type
        foreach ($companies as $companyId => $companyName) {
            $row = ['company_name' => $companyName];

            // For each rank type, get the count of soldiers
            foreach ($rankTypes as $rankType) {
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
        foreach ($rankTypes as $rankType) {
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
        $appointments = Appointment::orderBy('name')->pluck('name', 'id');
        $companies = Company::orderBy('name')->pluck('name', 'id');

        $paradeData = [];

        // Fill the data structure
        foreach ($appointments as $appId => $appName) {
            $row = ['appointment_name' => $appName];

            foreach ($companies as $companyId => $companyName) {
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

            // Calculate total for this appointment
            $row['total'] = array_sum(array_slice($row, 1, -1));

            $paradeData[] = $row;
        }

        // Add total row
        $totalRow = ['appointment_name' => 'Total'];
        foreach ($companies as $companyId => $companyName) {
            $total = 0;
            foreach ($paradeData as $row) {
                $total += $row[$companyName] ?? 0;
            }
            $totalRow[$companyName] = $total;
        }
        $totalRow['total'] = array_sum(array_slice($totalRow, 1, -1));
        $paradeData[] = $totalRow;

        // Combine both data sets with a separator row and header row
        $combinedData = [];

        // Add Company and Rank data
        foreach ($companyRankData as $row) {
            $combinedData[] = $row;
        }

        // Add a separator row
        $combinedData[] = ['separator' => true];

        // Add a header row for the second report
        $headerRow = ['header' => true];
        $combinedData[] = $headerRow;

        // Add Parade data
        foreach ($paradeData as $row) {
            $combinedData[] = $row;
        }

        return new Collection($combinedData);
    }

    public function headings(): array
    {
        // Get all distinct rank types
        $rankTypes = Rank::distinct()->pluck('type')->sort()->toArray();

        $headings = ['Company Name'];

        // Add each rank type as a column
        foreach ($rankTypes as $rankType) {
            $headings[] = $rankType;
        }

        // Add the calculated columns
        $headings[] = 'Total';
        $headings[] = 'Appointed';
        $headings[] = 'Final Total';

        return $headings;
    }

    public function map($row): array
    {
        // Check if this is a separator row
        if (isset($row['separator']) && $row['separator']) {
            // Return an empty row for separator
            return [''];
        }

        // Check if this is a header row for the second report
        if (isset($row['header']) && $row['header']) {
            // Get all companies for the second report's headers
            $companies = Company::orderBy('name')->pluck('name')->toArray();

            $mapped = ['Appointment Name'];

            foreach ($companies as $company) {
                $mapped[] = $company;
            }

            $mapped[] = 'Total';

            return $mapped;
        }

        // Check if this row is from Company and Rank data
        if (isset($row['company_name'])) {
            // Get all distinct rank types
            $rankTypes = Rank::distinct()->pluck('type')->sort()->toArray();

            $mapped = [$row['company_name']];

            // Add each rank type count
            foreach ($rankTypes as $rankType) {
                $mapped[] = $row[$rankType] ?? 0;
            }

            // Add the calculated columns
            $mapped[] = $row['total'];
            $mapped[] = $row['appointed'];
            $mapped[] = $row['final_total'];

            return $mapped;
        }

        // Otherwise, it's from Parade data
        // Get all companies
        $companies = Company::orderBy('name')->pluck('name')->toArray();

        $mapped = [$row['appointment_name']];

        foreach ($companies as $company) {
            $mapped[] = $row[$company] ?? 0;
        }

        $mapped[] = $row['total'];

        return $mapped;
    }

    public function title(): string
    {
        return 'Parade Report';
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Find the header row for the second report
                $highestRow = $sheet->getHighestRow();
                $headerRow = null;

                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell('A' . $row)->getValue();
                    if ($cellValue === 'Appointment Name') {
                        $headerRow = $row;
                        break;
                    }
                }

                if ($headerRow) {
                    // Style the header row
                    $sheet->getStyle('A' . $headerRow . ':' . $sheet->getHighestColumn() . $headerRow)
                        ->getFont()->setBold(true);

                    // Add a title for the second report
                    $sheet->setCellValue('A' . ($headerRow - 1), 'Parade Report');
                    $sheet->getStyle('A' . ($headerRow - 1))
                        ->getFont()
                        ->setBold(true)
                        ->setSize(14);

                    // Merge the title across all columns
                    $sheet->mergeCells('A' . ($headerRow - 1) . ':' . $sheet->getHighestColumn() . ($headerRow - 1));

                    // Center the title
                    $sheet->getStyle('A' . ($headerRow - 1))
                        ->getAlignment()
                        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }
            }
        ];
    }
}
