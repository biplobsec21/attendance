<?php

namespace App\Http\Controllers;

use App\Exports\DutyExport;
use App\Exports\CombinedSingleSheetExport;
use App\Exports\ParadePdfExport;
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
            $pdf = PDF::loadView('exports.parade_report', [
                'companyRankData' => (new ParadePdfExport($date))->view()->getData()['companyRankData'],
                'paradeData' => (new ParadePdfExport($date))->view()->getData()['paradeData'],
                'rankTypes' => (new ParadePdfExport($date))->view()->getData()['rankTypes'],
                'companies' => (new ParadePdfExport($date))->view()->getData()['companies'],
                'formattedDate' => (new ParadePdfExport($date))->view()->getData()['formattedDate'],
                'date' => $date
            ]);

            // Set paper size to legal
            $pdf->setPaper('legal', 'portrait');

            return $pdf->download($fileName);
        } else {
            $fileName = 'parade_report_' . now()->format('Ymd_His') . '.' . ($type === 'excel' ? 'xlsx' : $type);
            return Excel::download(new CombinedSingleSheetExport($date), $fileName);
        }
    }
}
