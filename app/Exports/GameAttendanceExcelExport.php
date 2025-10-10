<?php

namespace App\Exports;

use App\Services\GameAttendanceService;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class GameAttendanceExcelExport implements FromView, WithEvents
{
    protected $date;
    protected $service;
    protected $reportType;

    public function __construct($date, $reportType = 'game')
    {
        $this->date = $date;
        $this->reportType = $reportType;
        $this->service = new GameAttendanceService($reportType);
    }

    public function view(): View
    {
        $format1Data = $this->service->getFormat1Data($this->date);
        $format2Data = $this->service->getFormat2Data($this->date);
        $rankTypes = $this->service->getRankTypes();
        $formattedDate = Carbon::parse($this->date)->format('d M Y');
        $reportTitle = $this->service->getReportTitle();

        // Get companies for Format2
        $companies = [];
        if (!empty($format2Data)) {
            $firstRow = $format2Data[0];
            foreach ($firstRow as $key => $value) {
                if (!in_array($key, ['category', 'type', 'Total'])) {
                    $companies[] = $key;
                }
            }
        }
        $format3Data = $this->service->getFormat3Data($this->date);

        return view('exports.game_attendance_excel_combined', [
            'format1Data' => $format1Data,
            'format2Data' => $format2Data,
            'format3Data' => $format3Data,
            'rankTypes' => $rankTypes,
            'companies' => $companies,
            'date' => $formattedDate,
            'reportTitle' => $reportTitle,
        ]);
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
}
