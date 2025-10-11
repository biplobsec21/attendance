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
        // Use the optimized query from the formatter with ALL necessary eager loading
        $profiles = SoldierDataFormatter::getOptimizedQuery(Soldier::query())
            ->withCount(['currentLeaveApplications as current_leave_applications_count'])
            ->with(['currentLeaveApplications.leaveType'])
            ->get();

        // Pre-calculate ERE existence to avoid N+1 queries
        // The 'ere' relationship is already loaded, so we just check if it's not empty
        $formattedProfiles = $profiles->map(function ($soldier) {
            $formatted = $this->formatter->format($soldier);
            // Since 'ere' is already eager loaded, just check the collection
            $formatted['has_ere'] = $soldier->relationLoaded('ere') && $soldier->ere->isNotEmpty();
            return $formatted;
        });

        // Calculate stats more efficiently using the already-loaded data
        $stats = [
            'total' => $profiles->count(),
            'active' => $profiles->filter(fn($s) => !$s->is_on_leave && !$s->is_sick)->count(),
            'leave' => $profiles->filter(fn($s) => $s->is_on_leave)->count(),
            'medical' => $profiles->filter(fn($s) => $s->is_sick)->count(),
            'with_ere' => $profiles->filter(fn($s) => $s->relationLoaded('ere') && $s->ere->isNotEmpty())->count(),
            'without_ere' => $profiles->filter(fn($s) => !$s->relationLoaded('ere') || $s->ere->isEmpty())->count(),
        ];

        return response()->json([
            'data' => $formattedProfiles,
            'stats' => $stats
        ]);
    }

    /**
     * OPTIONAL: Use this version if you have 1000+ soldiers for even better performance
     * This uses chunking to process data in batches
     */
    public function indexChunked(Request $request)
    {
        $formattedProfiles = collect([]);
        $statsCounters = [
            'total' => 0,
            'active' => 0,
            'leave' => 0,
            'medical' => 0,
            'with_ere' => 0,
            'without_ere' => 0,
        ];

        // Process in chunks of 100 to reduce memory usage
        SoldierDataFormatter::getOptimizedQuery(Soldier::query())
            ->withCount(['currentLeaveApplications as current_leave_applications_count'])
            ->with(['currentLeaveApplications.leaveType'])
            ->chunk(100, function ($profiles) use (&$formattedProfiles, &$statsCounters) {
                foreach ($profiles as $soldier) {
                    $formatted = $this->formatter->format($soldier);
                    $formatted['has_ere'] = $soldier->relationLoaded('ere') && $soldier->ere->isNotEmpty();
                    $formattedProfiles->push($formatted);

                    // Calculate stats incrementally
                    $statsCounters['total']++;
                    if (!$soldier->is_on_leave && !$soldier->is_sick) $statsCounters['active']++;
                    if ($soldier->is_on_leave) $statsCounters['leave']++;
                    if ($soldier->is_sick) $statsCounters['medical']++;
                    if ($soldier->ere->isNotEmpty()) {
                        $statsCounters['with_ere']++;
                    } else {
                        $statsCounters['without_ere']++;
                    }
                }
            });

        return response()->json([
            'data' => $formattedProfiles,
            'stats' => $statsCounters
        ]);
    }
    public function getHistory($id, Request $request)
    {
        $type = $request->get('type'); // duty, leave, or appointment

        // Base query
        $query = Soldier::with(['rank:id,name']);

        // Add specific eager loading based on history type
        switch ($type) {
            case 'duty':
                $query->with([
                    'dutyRanks' => function ($q) {
                        $q->where('assignment_type', 'fixed')
                            ->with(['duty:id,duty_name,start_time,end_time,duration_days,status'])
                            ->select('id', 'soldier_id', 'duty_id', 'assignment_type', 'priority', 'remarks', 'created_at');
                    },
                    'courses:id,name',
                    'cadres:id,name'
                ]);
                break;

            case 'leave':
                $query->with([
                    'leaveApplications' => function ($q) {
                        $q->with('leaveType:id,name')
                            ->orderBy('start_date', 'desc')
                            ->select(
                                'id',
                                'soldier_id',
                                'leave_type_id',
                                'reason',
                                'start_date',
                                'end_date',
                                'application_current_status',
                                'hard_copy',
                                'created_at'
                            );
                    }
                ]);
                break;

            case 'appointment':
                $query->with([
                    'services' => function ($q) {
                        $q->select(
                            'id',
                            'soldier_id',
                            'appointments_name',
                            'appointment_type',
                            'appointment_id',
                            'appointments_from_date',
                            'appointments_to_date',
                            'status',
                            'note'
                        )
                            ->orderBy('appointments_from_date', 'desc');
                    }
                ]);
                break;

            default:
                return response()->json(['error' => 'Invalid history type'], 400);
        }

        $soldier = $query->find($id);

        if (!$soldier) {
            return response()->json(['error' => 'Soldier not found'], 404);
        }

        // Create formatter instance
        $formatter = new SoldierDataFormatter();

        // Get the specific history data
        $historyData = match ($type) {
            'duty' => $formatter->formatDutiesHistory($soldier),
            'leave' => $formatter->formatLeaveHistory($soldier),
            'appointment' => $formatter->formatAppointmentHistory($soldier),
            default => []
        };

        return response()->json([
            'type' => $type,
            'data' => $historyData,
            'soldier_name' => $soldier->full_name,
            'soldier_army_no' => $soldier->army_no,
            'soldier_rank' => $soldier->rank?->name,
            'count' => count($historyData)
        ]);
    }
}
