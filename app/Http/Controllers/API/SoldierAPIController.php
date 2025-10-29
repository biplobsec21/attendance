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
    // In App\Http\Controllers\API\SoldierAPIController

    public function getHistory($id, Request $request)
    {
        $type = $request->get('type'); // duty, leave, appointment, or att

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

            case 'att':
                $query->with([
                    'att' => function ($q) {
                        $q->orderBy('pivot_start_date', 'desc')
                            ->select('atts.id', 'atts.name');
                    }
                ]);
                break;

            case 'cmd':
                $query->with([
                    'cmds' => function ($q) {
                        $q->orderBy('pivot_start_date', 'desc')
                            ->select('cmds.id', 'cmds.name', 'cmds.status');
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
            'att' => $formatter->formatAttHistory($soldier),
            'cmd' => $formatter->formatCmdHistory($soldier),

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

    /**
     * Add new ATT record for a soldier
     */
    // In App\Http\Controllers\API\SoldierAPIController


    public function addAttRecord($id, Request $request)
    {
        try {
            $request->validate([
                'att_id' => 'required|exists:atts,id',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'remarks' => 'nullable|string|max:500'
            ]);

            $soldier = Soldier::findOrFail($id);

            // Check if ATT record already exists for this period
            // Use the exact column names from your table
            $existingAtt = DB::table('soldiers_att')
                ->where('soldier_id', $soldier->id)
                ->where('atts_id', $request->att_id) // Note: column is 'atts_id' not 'att_id'
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date ?? $request->start_date])
                        ->orWhereBetween('end_date', [$request->start_date, $request->end_date ?? $request->start_date])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->start_date);
                        });
                })
                ->exists();

            if ($existingAtt) {
                return response()->json([
                    'error' => 'ATT record already exists for this period'
                ], 422);
            }

            // Insert directly into pivot table using correct column names
            DB::table('soldiers_att')->insert([
                'soldier_id' => $soldier->id,
                'atts_id' => $request->att_id, // Column is 'atts_id'
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'remarks' => $request->remarks,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Reload the soldier with ATT data
            $soldier->load(['att' => function ($q) {
                $q->orderBy('soldiers_att.start_date', 'desc');
            }]);

            $formatter = new SoldierDataFormatter();
            $attHistory = $formatter->formatAttHistory($soldier);

            return response()->json([
                'message' => 'ATT record added successfully',
                'data' => $attHistory,
                'count' => count($attHistory)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('ATT Record Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to add ATT record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available ATT types for dropdown
     */
    // In App\Http\Controllers\API\SoldierAPIController

    /**
     * Get available ATT types for dropdown
     */
    public function getAttTypes()
    {
        try {
            $attTypes = \App\Models\Atts::where('status', true) // Use where instead of scope for clarity
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json($attTypes);
        } catch (\Exception $e) {
            \Log::error('Get ATT Types Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch ATT types'
            ], 500);
        }
    }
    /**
     * Add new CMD record for a soldier
     */
    public function addCmdRecord($id, Request $request)
    {
        try {
            $request->validate([
                'cmd_id' => 'required|exists:cmds,id',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'remarks' => 'nullable|string|max:500'
            ]);

            $soldier = Soldier::findOrFail($id);

            // Check if CMD record already exists for this period
            $existingCmd = DB::table('soldiers_cmds')
                ->where('soldier_id', $soldier->id)
                ->where('cmd_id', $request->cmd_id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_date', [$request->start_date, $request->end_date ?? $request->start_date])
                        ->orWhereBetween('end_date', [$request->start_date, $request->end_date ?? $request->start_date])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->start_date);
                        });
                })
                ->exists();

            if ($existingCmd) {
                return response()->json([
                    'error' => 'CMD record already exists for this period'
                ], 422);
            }

            // Insert into pivot table
            DB::table('soldiers_cmds')->insert([
                'soldier_id' => $soldier->id,
                'cmd_id' => $request->cmd_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'remarks' => $request->remarks,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Reload the soldier with CMD data
            $soldier->load(['cmds' => function ($q) {
                $q->orderBy('soldiers_cmds.start_date', 'desc');
            }]);

            $formatter = new SoldierDataFormatter();
            $cmdHistory = $formatter->formatCmdHistory($soldier);

            return response()->json([
                'message' => 'CMD record added successfully',
                'data' => $cmdHistory,
                'count' => count($cmdHistory)
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('CMD Record Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to add CMD record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available CMD types for dropdown
     */
    public function getCmdTypes()
    {
        try {
            $cmdTypes = \App\Models\Cmd::where('status', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json($cmdTypes);
        } catch (\Exception $e) {
            \Log::error('Get CMD Types Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch CMD types'
            ], 500);
        }
    }
    /**
     * Delete ATT record for a soldier
     */
    public function deleteAttRecord($soldierId, $attRecordId)
    {
        try {
            $soldier = Soldier::findOrFail($soldierId);

            // Delete the ATT record from pivot table
            $deleted = DB::table('soldiers_att')
                ->where('soldier_id', $soldierId)
                ->where('atts_id', $attRecordId)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'error' => 'ATT record not found'
                ], 404);
            }

            // Reload the soldier with ATT data
            $soldier->load(['att' => function ($q) {
                $q->orderBy('soldiers_att.start_date', 'desc');
            }]);

            $formatter = new SoldierDataFormatter();
            $attHistory = $formatter->formatAttHistory($soldier);

            return response()->json([
                'message' => 'ATT record deleted successfully',
                'data' => $attHistory,
                'count' => count($attHistory)
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete ATT Record Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete ATT record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete CMD record for a soldier
     */
    public function deleteCmdRecord($soldierId, $cmdRecordId)
    {
        try {
            $soldier = Soldier::findOrFail($soldierId);

            // Delete the CMD record from pivot table
            $deleted = DB::table('soldiers_cmds')
                ->where('soldier_id', $soldierId)
                ->where('cmd_id', $cmdRecordId)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'error' => 'CMD record not found'
                ], 404);
            }

            // Reload the soldier with CMD data
            $soldier->load(['cmds' => function ($q) {
                $q->orderBy('soldiers_cmds.start_date', 'desc');
            }]);

            $formatter = new SoldierDataFormatter();
            $cmdHistory = $formatter->formatCmdHistory($soldier);

            return response()->json([
                'message' => 'CMD record deleted successfully',
                'data' => $cmdHistory,
                'count' => count($cmdHistory)
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete CMD Record Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete CMD record: ' . $e->getMessage()
            ], 500);
        }
    }
}
