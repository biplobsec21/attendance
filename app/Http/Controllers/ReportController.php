<?php

namespace App\Http\Controllers;

use App\Models\SoldierDuty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use DB;

class ReportController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request): View
    {
        $dutyReport = SoldierDuty::selectRaw('assigned_date, COUNT(*) as total_duties, COUNT(DISTINCT soldier_id) as total_soldiers')
            ->groupBy('assigned_date')
            ->orderBy('assigned_date', 'desc')
            ->get();
        return view('mpm.page.report.index', compact('dutyReport'));
    }

    public function getDutiesByDate($date)
    {
        $stats = SoldierDuty::select(
            'duty_id',
            'assigned_date',
            DB::raw('COUNT(*) as total_duties'),
            DB::raw('COUNT(DISTINCT soldier_id) as total_soldiers'),
            DB::raw('MIN(start_time) as start_time'),
            DB::raw('MAX(end_time) as end_time')
        )
            ->where('assigned_date', $date)
            ->groupBy('duty_id', 'assigned_date')
            ->with('duty:id,duty_name')
            ->get();

        // Pass the data to a Blade view
        return view('mpm.page.report.partials.duty_modal', compact('stats', 'date'));
    }
}
