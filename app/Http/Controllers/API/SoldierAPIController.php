<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Soldier;
use Illuminate\Support\Facades\DB;
use App\Services\SoldierDataFormatter;

class SoldierAPIController extends Controller
{
    protected $formatter;

    public function __construct(SoldierDataFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function index(Request $request)
    {
        // Load the ere relationship along with rank and company
        $profiles = Soldier::with(['rank', 'company', 'ere', 'currentLeaveApplications.leaveType'])
            ->withCount(['currentLeaveApplications as current_leave_applications_count'])
            ->get();

        // Format the collection and add ERE status
        $formattedProfiles = $profiles->map(function ($soldier) {
            $formatted = $this->formatter->format($soldier);
            $formatted['has_ere'] = $soldier->ere()->exists();
            return $formatted;
        });

        return response()->json([
            'data' => $formattedProfiles,
            'stats' => [
                'total' => $profiles->count(),
                'active' => $profiles->where('is_on_leave', false)->where('is_sick', false)->count(),
                'leave' => $profiles->where('is_on_leave', true)->count(),
                'medical' => $profiles->where('is_sick', true)->count(),
                'with_ere' => $profiles->filter(function ($soldier) {
                    return $soldier->ere()->exists();
                })->count(),
                'without_ere' => $profiles->filter(function ($soldier) {
                    return !$soldier->ere()->exists();
                })->count()
            ]
        ]);
    }
}
