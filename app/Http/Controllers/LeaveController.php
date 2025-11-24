<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateLeaveApplication;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\Soldier;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    //
    public function index()
    {
        $leaveType = LeaveType::get();
        $profiles = Soldier::get();

        $query = LeaveApplication::query();
        $leaveDatas = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        // Get counts for each status
        $pendingCount = LeaveApplication::where('application_current_status', 'pending')->count();
        $approvedCount = LeaveApplication::where('application_current_status', 'approved')->count();
        $rejectedCount = LeaveApplication::where('application_current_status', 'rejected')->count();

        return view('mpm.page.leave.leaveList', compact(
            'profiles',
            'leaveType',
            'leaveDatas',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }
    // Ajax filtering for leave applications
    public function filter(Request $request)
    {
        try {
            $query = LeaveApplication::with(['soldier', 'leaveType', 'soldier.rank']);

            // Your existing filter logic...
            if ($request->filled('from_date')) {
                $query->where(function ($q) use ($request) {
                    $q->whereDate('start_date', '>=', $request->from_date)
                        ->orWhereDate('created_at', '>=', $request->from_date);
                });
            }

            if ($request->filled('to_date')) {
                $query->where(function ($q) use ($request) {
                    $q->whereDate('end_date', '<=', $request->to_date)
                        ->orWhereDate('created_at', '<=', $request->to_date);
                });
            }

            if ($request->filled('leave_type_id')) {
                $query->where('leave_type_id', $request->leave_type_id);
            }

            if ($request->filled('status')) {
                $query->where('application_current_status', $request->status);
            }

            if ($request->filled('soldier_search')) {
                $searchTerm = $request->soldier_search;
                $query->whereHas('soldier', function ($q) use ($searchTerm) {
                    $q->where('full_name', 'like', "%{$searchTerm}%")
                        ->orWhere('army_no', 'like', "%{$searchTerm}%");
                });
            }

            $query->orderBy('created_at', 'desc');
            $leaveDatas = $query->paginate(30)->withQueryString();

            // Get counts for filtered data
            $pendingCount = (clone $query)->where('application_current_status', 'pending')->count();
            $approvedCount = (clone $query)->where('application_current_status', 'approved')->count();
            $rejectedCount = (clone $query)->where('application_current_status', 'rejected')->count();

            if ($request->ajax()) {
                $table = view('mpm.components.leave-table', compact('leaveDatas'))->render();
                $pagination = $leaveDatas->links()->render();

                return response()->json([
                    'success' => true,
                    'table' => $table,
                    'pagination' => $pagination,
                    'total' => $leaveDatas->total(),
                    'counts' => [
                        'pending' => $pendingCount,
                        'approved' => $approvedCount,
                        'rejected' => $rejectedCount,
                    ]
                ]);
            }

            return back();
        } catch (\Exception $e) {
            \Log::error('Error filtering leave applications: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error filtering data. Please try again.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error filtering data. Please try again.');
        }
    }
    public function leaveApplicationSubmit(StoreUpdateLeaveApplication $request)
    {
        try {
            $data = $request->validated();

            // Handle multiple soldiers for new applications
            $soldierIds = $request->input('soldier_ids', []);

            // Handle image upload
            $filePath = null;
            if ($request->hasFile('application_file')) {
                $filePath = $request->file('application_file')->store('leave_files', 'public');
            }

            // Create leave application for each selected soldier
            $createdCount = 0;
            foreach ($soldierIds as $soldierId) {
                $leaveData = [
                    'soldier_id' => $soldierId,
                    'leave_type_id' => $data['leave_type_id'],
                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'],
                    'total_days' => $data['total_days'] ?? $this->calculateTotalDays($data['start_date'], $data['end_date']),
                    'reason' => $data['reason'] ?? null,
                    'hard_copy' => $filePath,
                    'application_current_status' => 'pending',
                ];

                LeaveApplication::create($leaveData);
                $createdCount++;
            }

            // Redirect to next step with success message
            return redirect()->route('leave.index')
                ->with('success', "Leave Application submitted successfully for {$createdCount} soldier(s).");
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error saving leave application: ' . $e->getMessage());

            // Redirect back with an error message
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while saving. Please try again.');
        }
    }

    // Helper method to calculate total days
    private function calculateTotalDays($startDate, $endDate)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        return $start->diffInDays($end) + 1;
    }
    public function changeStatus(Request $request)
    {
        $validated = $request->validate([
            'application_current_status' => ['required', 'string'],
            'status_reason' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->application_current_status === 'rejected' && empty($value)) {
                        $fail('Reason is required when rejecting.');
                    }
                },
            ],
        ]);

        $leave = LeaveApplication::findOrFail($request->leave_id);

        $leave->update([
            'application_current_status' => $validated['application_current_status'],
            'reject_reason' => $request->status_reason,
            'reject_status_date' => now(),
        ]);

        return back()->with('success', 'Leave status updated successfully.');
    }

    public function update(StoreUpdateLeaveApplication $request, $id)
    {
        $leave = LeaveApplication::findOrFail($id);

        // If user uploaded a new file
        if ($request->hasFile('application_file')) {
            // Delete old file if exists
            if ($leave->hard_copy && \Storage::exists('public/' . $leave->hard_copy)) {
                \Storage::delete('public/' . $leave->hard_copy);
            }

            $path = $request->file('application_file')->store('leave_files', 'public');
            $leave->hard_copy = $path;
        }

        // If user clicked "remove file"
        if ($request->remove_hard_copy == "1") {
            if ($leave->hard_copy && \Storage::exists('public/' . $leave->hard_copy)) {
                \Storage::delete('public/' . $leave->hard_copy);
            }
            $leave->hard_copy = null; // Remove from DB
        }

        // Other fields update
        $leave->reason = $request->reason;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->leave_type_id = $request->leave_type_id;
        $leave->soldier_id = $request->soldier_id;

        $leave->save();

        return redirect()->route('leave.index')->with('success', 'Leave updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $leave = LeaveApplication::findOrFail($id);

            // Delete associated file if exists
            if ($leave->hard_copy && \Storage::exists('public/' . $leave->hard_copy)) {
                \Storage::delete('public/' . $leave->hard_copy);
            }

            $leave->delete();

            return redirect()->route('leave.index')->with('success', 'Leave Application deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting leave application: ' . $e->getMessage());
            return redirect()->route('leave.index')->with('error', 'Error deleting leave application. Please try again.');
        }
    }

    public function approvalList()
    {
        // Implementation for approval list
    }

    public function approvalAction()
    {
        // Implementation for approval action
    }
    // Add this method to your LeaveController
    public function bulkStatusUpdate(Request $request)
    {
        $validated = $request->validate([
            'leave_ids' => ['required', 'array'],
            'leave_ids.*' => ['exists:soldier_leave_applications,id'],
            'application_current_status' => ['required', 'string', 'in:pending,approved,rejected'],
            'status_reason' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->application_current_status === 'rejected' && empty($value)) {
                        $fail('Reason is required when rejecting.');
                    }
                },
            ],
        ]);

        try {
            DB::beginTransaction();

            $leaveIds = $validated['leave_ids'];
            $status = $validated['application_current_status'];
            $reason = $validated['status_reason'] ?? null;

            $updateData = [
                'application_current_status' => $status,
                'reject_reason' => $reason,
                'reject_status_date' => now(),
            ];

            // Get leave applications before update
            $leaveApplications = LeaveApplication::whereIn('id', $leaveIds)->get();

            // Bulk update for performance
            LeaveApplication::whereIn('id', $leaveIds)->update($updateData);

            // If status is approved, handle notifications manually
            if ($status === 'approved') {
                $this->handleBulkApprovedNotifications($leaveApplications);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($leaveIds) . ' leave application(s) updated successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in bulk status update: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating leave applications. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle notifications for bulk approved leaves
     */
    private function handleBulkApprovedNotifications($leaveApplications)
    {
        $users = \App\Models\User::get();

        // Refresh the leave applications to get updated data
        $leaveApplications->each->refresh();

        foreach ($leaveApplications as $leaveApplication) {
            // Send notifications to all users
            foreach ($users as $user) {
                $user->notify(new \App\Notifications\LeaveApprovedNotification($leaveApplication));
            }

            // Check and trigger leave completed event if applicable
            if ($leaveApplication->end_date->isToday()) {
                event(new \App\Events\LeaveCompleted($leaveApplication));
            }
        }

        // Log the bulk approval
        \Log::info('Bulk leave approvals processed', [
            'count' => $leaveApplications->count(),
            'leave_ids' => $leaveApplications->pluck('id')->toArray()
        ]);
    }
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'leave_ids' => ['required', 'array'],
            'leave_ids.*' => ['exists:soldier_leave_applications,id'],
        ]);

        try {
            DB::beginTransaction();

            $leaveIds = $validated['leave_ids'];
            $leaveApplications = LeaveApplication::whereIn('id', $leaveIds)->get();

            $deletedCount = 0;
            foreach ($leaveApplications as $leaveApplication) {
                // Delete associated file if exists
                if ($leaveApplication->hard_copy && \Storage::exists('public/' . $leaveApplication->hard_copy)) {
                    \Storage::delete('public/' . $leaveApplication->hard_copy);
                }

                $leaveApplication->delete();
                $deletedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $deletedCount . ' leave application(s) deleted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in bulk delete: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting leave applications. Please try again.',
            ], 500);
        }
    }
}
