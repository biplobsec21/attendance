<?php

namespace App\Exports;

use App\Services\ManpowerDataService;
use App\Services\ManpowerDataCache;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ManpowerExcelExport implements FromView, WithEvents
{
    protected $date;
    protected $manpowerDataService;

    public function __construct($date)
    {
        $this->date = $date;
        $this->manpowerDataService = new ManpowerDataService();
    }

    public function view(): View
    {
        // Check if we have cached data for this date
        $cachedData = ManpowerDataCache::get($this->date);

        if ($cachedData === null) {
            // Get fresh data and cache it
            $cachedData = $this->manpowerDataService->getManpowerData($this->date);
            ManpowerDataCache::set($this->date, $cachedData);
        }

        $formattedDate = Carbon::parse($this->date)->format('F d, Y');

        return view('exports.manpower_excel', array_merge($cachedData, [
            'formattedDate' => $formattedDate,
            'date' => $this->date
        ]));
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

                // Set column widths
                for ($colIndex = 1; $colIndex <= $highestColumnIndex; $colIndex++) {
                    $columnLetter = Coordinate::stringFromColumnIndex($colIndex);

                    if ($colIndex === 1) {
                        $sheet->getColumnDimension($columnLetter)->setWidth(10);
                    } else {
                        $sheet->getColumnDimension($columnLetter)->setWidth(4);
                    }
                }

                // Apply vertical text to header rows containing "Coy"
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell('A' . $row)->getValue();

                    if (is_string($cellValue) && trim($cellValue) === 'Coy') {
                        // Apply vertical text to header cells (excluding first column)
                        for ($colIndex = 2; $colIndex <= $highestColumnIndex; $colIndex++) {
                            $columnLetter = Coordinate::stringFromColumnIndex($colIndex);

                            $sheet->getStyle($columnLetter . $row)
                                ->getAlignment()
                                ->setTextRotation(90)
                                ->setVertical(Alignment::VERTICAL_CENTER)
                                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        }

                        $sheet->getRowDimension($row)->setRowHeight(80);
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)
                            ->getFont()
                            ->setBold(true);
                    }
                }

                // Apply borders and styling
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Set font
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)
                    ->getFont()
                    ->setSize(11);
            },
        ];
    }

    /**
     * Helper method to debug manpower counts
     */
    private function getManpowerCounts($manpowerData)
    {
        $counts = [];
        foreach ($manpowerData as $companyId => $ranks) {
            foreach ($ranks as $rankId => $data) {
                $counts["company_{$companyId}_rank_{$rankId}"] = $data->count ?? 0;
            }
        }
        return $counts;
    }
}
