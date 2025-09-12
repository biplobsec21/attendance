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
                    return "
                        <a href='javascript:void(0);' onclick='showDetails({$log->id})'
                            class='bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition duration-300 ease-in-out'>
                            Show
                        </a>";
                })

                ->rawColumns(['action_badge', 'show', 'user'])
                ->make(true);
        }

        // Normal page load
        return view('mpm.page.audit.index');
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
