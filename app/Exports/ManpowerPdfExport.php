<?php

namespace App\Exports;

use App\Services\ManpowerDataService;
use App\Services\ManpowerDataCache;
use Illuminate\Contracts\View\View;
use Carbon\Carbon;

class ManpowerPdfExport
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

        // Debug: Log the data to check consistency
        \Log::info('PDF Export Data', [
            'date' => $this->date,
            'received_manpower_counts' => $this->getManpowerCounts($cachedData['receivedManpower']),
            'leave_manpower_counts' => $this->getManpowerCounts($cachedData['leaveManpower']),
            'without_leave_manpower_counts' => $this->getManpowerCounts($cachedData['withoutLeaveManpower'])
        ]);

        return view('exports.manpower_pdf', array_merge($cachedData, [
            'formattedDate' => $formattedDate,
            'date' => $this->date
        ]));
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
