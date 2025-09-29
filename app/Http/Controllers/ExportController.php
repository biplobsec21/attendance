<?php

namespace App\Http\Controllers;

use App\Exports\DutyExport;
use App\Exports\CombinedSingleSheetExport;
use App\Exports\ParadePdfExport;
use App\Exports\ManpowerExcelExport;
use App\Exports\ManpowerPdfExport;
use App\Services\ManpowerDataCache;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use PDF;

class ExportController extends Controller
{
    /**
     * Export duties as CSV or Excel, optionally filtered by date or range.
     */
    public function exportDuties(Request $request, $type = 'csv')
    {
        $allowedTypes = ['csv', 'excel', 'xlsx'];
        $type = strtolower($type);

        if (!in_array($type, $allowedTypes)) {
            abort(400, 'Invalid export type');
        }

        $startDate = $request->query('start_date'); // optional
        $endDate   = $request->query('end_date');   // optional

        $fileName = 'duties_' . now()->format('Ymd_His') . '.' . ($type === 'excel' ? 'xlsx' : $type);

        return Excel::download(new DutyExport($startDate, $endDate), $fileName);
    }

    /**
     * Export parade report as CSV or Excel, filtered by a specific date.
     */
    public function exportParade(Request $request, $type = 'excel')
    {
        $allowedTypes = ['excel', 'xlsx', 'pdf'];
        $type = strtolower($type);

        if (!in_array($type, $allowedTypes)) {
            abort(400, 'Invalid export type');
        }

        $date = $request->query('date');

        if (!$date) {
            return redirect()->back()->with('error', 'Date is required');
        }

        // Validate that the date is not in the future
        $inputDate = Carbon::parse($date);
        $today = Carbon::today();

        if ($inputDate->gt($today)) {
            return redirect()->back()->with('error', 'Future dates cannot be selected');
        }

        if ($type === 'pdf') {
            $fileName = 'parade_report_' . now()->format('Ymd_His') . '.pdf';

            // Create a single instance and get the data once
            $paradePdfExport = new ParadePdfExport($date);
            $view = $paradePdfExport->view();
            $data = $view->getData();

            $pdf = PDF::loadView('exports.parade_report', $data);

            // Set paper size to legal
            $pdf->setPaper('legal', 'portrait');

            return $pdf->download($fileName);
        } else {
            $fileName = 'parade_report_' . now()->format('Ymd_His') . '.' . ($type === 'excel' ? 'xlsx' : $type);
            return Excel::download(new CombinedSingleSheetExport($date), $fileName);
        }
    }

    /**
     * Export manpower distribution as Excel or PDF, filtered by a specific date.
     */
    public function exportManpower(Request $request, $type)
    {
        $allowedTypes = ['xl', 'xlsx', 'pdf'];
        $type = strtolower($type);

        if (!in_array($type, $allowedTypes)) {
            abort(400, 'Invalid export type');
        }

        $date = $request->query('date');

        if (!$date) {
            return redirect()->back()->with('error', 'Date is required');
        }

        // Validate that the date is not in the future
        $inputDate = Carbon::parse($date);
        $today = Carbon::today();

        if ($inputDate->gt($today)) {
            return redirect()->back()->with('error', 'Future dates cannot be selected');
        }

        if ($type === 'pdf') {
            $fileName = 'manpower_distribution_' . now()->format('Ymd_His') . '.pdf';

            // Create a single instance and get the data once
            $manpowerPdfExport = new ManpowerPdfExport($date);
            $view = $manpowerPdfExport->view();
            $data = $view->getData();

            $pdf = PDF::loadView('exports.manpower_pdf', $data);

            // Set paper size to legal
            $pdf->setPaper('legal', 'landscape');

            return $pdf->download($fileName);
        } else {
            // Convert 'xl' to 'xlsx' for Excel export
            $exportType = ($type === 'xl') ? 'xlsx' : $type;
            $fileName = 'manpower_distribution_' . now()->format('Ymd_His') . '.' . $exportType;

            // Clear cache before generating Excel to ensure fresh data
            ManpowerDataCache::clear($date);

            return Excel::download(new ManpowerExcelExport($date), $fileName);
        }
    }
}
