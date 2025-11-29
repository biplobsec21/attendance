<?php

namespace App\Http\Controllers;

use App\Services\GameAttendanceService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GameAttendancePdfExport;
use App\Exports\GameAttendanceExcelExport;

class AttendanceReportController extends Controller
{
    /**
     * Export attendance report based on report type and export format
     */
    public function exportAttendanceReport(Request $request, $reportType, $exportType)
    {
        // Define allowed report types and their corresponding service constants
        $allowedReportTypes = [
            'game' => GameAttendanceService::REPORT_GAME,
            'pt' => GameAttendanceService::REPORT_PT,
            'roll-call' => GameAttendanceService::REPORT_ROLL_CALL,
            'parade' => GameAttendanceService::REPORT_PARADE,
        ];

        // Define allowed export types
        $allowedExportTypes = ['xl', 'xlsx', 'pdf', 'excel'];

        // Convert to lowercase for consistency
        $reportType = strtolower($reportType);
        $exportType = strtolower($exportType);

        // Validate report type
        if (!isset($allowedReportTypes[$reportType])) {
            abort(400, 'Invalid report type');
        }

        // Validate export type
        if (!in_array($exportType, $allowedExportTypes)) {
            abort(400, 'Invalid export type');
        }

        // Get the date from the request
        $date = $request->query('date');

        if (!$date) {
            return redirect()->back()->with('error', 'Date is required');
        }

        // Validate that the date is not in the future
        $inputDate = Carbon::parse($date);
        $today = Carbon::today();

        // if ($inputDate->gt($today)) {
        //     return redirect()->back()->with('error', 'Future dates cannot be selected');
        // }

        // Map 'excel' to 'xlsx' for processing
        if ($exportType === 'excel') {
            $exportType = 'xlsx';
        }

        // Get the service constant for the report type
        $serviceReportType = $allowedReportTypes[$reportType];

        // Create service instance with the report type
        $service = new GameAttendanceService($serviceReportType);
        $fileName = $service->getFileName($date);

        if ($exportType === 'pdf') {
            // Add .pdf extension to the filename
            $fileName .= '.pdf';

            // Generate PDF directly without using Excel export
            $pdfExport = new GameAttendancePdfExport($date, $serviceReportType);
            $view = $pdfExport->view();
            $data = $view->getData();

            // Load the view and generate PDF with better settings
            $pdf = PDF::loadView($view->getName(), $data)
                ->setPaper('legal', 'landscape');

            return $pdf->download($fileName);
        } else {
            // Add the appropriate extension to the filename
            $fileName .= '.' . $exportType;

            return Excel::download(new GameAttendanceExcelExport($date, $serviceReportType), $fileName);
        }
    }
}
