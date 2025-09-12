<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;

class BackupController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $logs = Activity::with('causer')->select('activity_log.*');

            return DataTables::of($logs)
                ->addColumn('table_name', fn($log) => $log->subject_type ? class_basename($log->subject_type) : '')
                ->addColumn('action_badge', function ($log) {
                    $class = [
                        'created' => 'bg-green-100 text-green-800',
                        'updated' => 'bg-yellow-100 text-yellow-800',
                        'deleted' => 'bg-red-100 text-red-800',
                    ][$log->description ?? 'updated'] ?? 'bg-gray-100 text-gray-800';

                    return "<span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$class}'>"
                        . strtoupper($log->event) .
                        "</span>";
                })
                ->addColumn('user', function ($log) {
                    if ($log->causer) {
                        return "<a href='javascript:void(0);' onclick='showUserProfile({$log->causer->id})' class='text-blue-600 hover:underline'>{$log->causer->name}</a>";
                    }
                    return 'System';
                })
                ->addColumn('date_time', fn($log) => $log->created_at->format('d M Y, h:i A'))
                ->addColumn('show', function ($log) {
                    return '
                        <button onclick="showDetails(' . $log->id . ')"
                            class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" class="w-4 h-4 inline-block">
                                <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 157.1 0 224 0 256s48.6 98.9 95.4 143.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6C527.4 354.9 576 288 576 256s-48.6-98.9-95.4-143.4C433.5 68.8 368.8 32 288 32zm0 400c-106 0-192-86-192-192S182 64 288 64s192 86 192 192-86 192-192 192zm0-256c-35.3 0-64 28.7-64 64s28.7 64 64 64 64-28.7 64-64-28.7-64-64-64z"/>
                            </svg>
                        </button>
                    ';
                })


                ->rawColumns(['action_badge', 'show', 'user'])
                ->make(true);
        }
    }

    public function show($id)
    {
        $log = Activity::with('causer')->findOrFail($id);
        if (!$log) {
            return response()->json(['message' => 'Log not found'], 404);
        }
        return view('mpm.page.audit.show', compact('log'));
    }


    public function downloadDatabase()
    {
        $filename = now()->format('Y-m-d-H-i') . '.sql';
        $path = storage_path("app/{$filename}");

        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');

        $command = "mysqldump -h {$host} -u {$username} -p{$password} {$database} > {$path}";
        exec($command);
        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function showUserProfile($userId)
    {
        $user = User::findOrFail($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return view('mpm.page.audit.user-profile', compact('user'));
    }
}
