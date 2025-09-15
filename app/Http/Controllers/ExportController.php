<?php

namespace App\Http\Controllers;

use App\Exports\DutyExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
}
