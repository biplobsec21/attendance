<?php

namespace App\Http\Controllers;

use App\Models\SoldierDuty;
use App\Services\DutyAssignmentService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DutyAssignmentController extends Controller
{
    protected $service;

    public function __construct(DutyAssignmentService $service)
    {
        $this->service = $service;
    }

    /**
     * Generate assignments for today
     */
    public function showForm()
    {
        $assignments = SoldierDuty::with([
            'soldier.rank.dutyRanks', // rank → dutyRanks
            'duty.dutyRanks'         // duty → dutyRanks
        ])
            ->latest('assigned_date')
            ->take(20)
            ->get();

        return view('mpm.page.duty_assignment_to_soldiers.generate', compact('assignments'));
    }


    public function generateToday()
    {
        $today = Carbon::today()->toDateString();
        $this->service->assignDutiesForDate($today);

        return redirect()->back()->with('success', "Duties assigned for {$today}");
    }

    /**
     * Generate assignments for a custom date
     */
    public function generateForDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = Carbon::parse($request->date)->toDateString();
        $this->service->assignDutiesForDate($date);

        return redirect()->back()->with('success', "Duties assigned for {$date}");
    }
}
