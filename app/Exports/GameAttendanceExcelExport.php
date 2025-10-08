<?php

namespace App\Exports;

use App\Services\GameAttendanceService;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;

class GameAttendanceExcelExport implements FromView
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
        $format3Data = $this->service->getFormat3Data($this->date); // Added Format 3 data

        return view('exports.game_attendance_excel_combined', [
            'format1Data' => $format1Data,
            'format2Data' => $format2Data,
            'format3Data' => $format3Data, // Pass to view
            'rankTypes' => $rankTypes,
            'companies' => $companies,
            'date' => $formattedDate,
            'reportTitle' => $reportTitle,
        ]);
    }
}
