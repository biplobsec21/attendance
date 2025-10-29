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
    // leave lists
    public function index()
    {
        $leaveType = LeaveType::get();
        $profiles = Soldier::get();

        $query = LeaveApplication::query();
        $leaveDatas = $query->paginate(30)->withQueryString();

        return view('mpm.page.leave.leaveList', compact('profiles', 'leaveType', 'leaveDatas'));
    }

    // Ajax filtering for leave applications
    public function filter(Request $request)
    {
        try {
            $query = LeaveApplication::with(['soldier', 'leaveType', 'soldier.rank']);

            // Date range filter - filter by application date (created_at) or leave dates
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

            // Leave type filter
            if ($request->filled('leave_type_id')) {
                $query->where('leave_type_id', $request->leave_type_id);
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('application_current_status', $request->status);
            }

            // Soldier search filter
            if ($request->filled('soldier_search')) {
                $searchTerm = $request->soldier_search;
                $query->whereHas('soldier', function ($q) use ($searchTerm) {
                    $q->where('full_name', 'like', "%{$searchTerm}%")
                        ->orWhere('army_no', 'like', "%{$searchTerm}%");
                });
            }

            // Order by latest first
            $query->orderBy('created_at', 'desc');

            $leaveDatas = $query->paginate(30)->withQueryString();

            if ($request->ajax()) {
                $table = view('mpm.components.leave-table', compact('leaveDatas'))->render();
                $pagination = $leaveDatas->links()->render();

                return response()->json([
                    'success' => true,
                    'table' => $table,
                    'pagination' => $pagination,
                    'total' => $leaveDatas->total()
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
            // Handle image upload
            if ($request->hasFile('application_file')) {
                $data['hard_copy'] = $request->file('application_file')->store('profiles', 'public');
            }
            LeaveApplication::create($data);
            // Redirect to next step with success message
            return redirect()->route('leave.index')
                ->with('success', 'Leave Application submitted successfully.');
        } catch (\Exception $e) {
            // Log the error for debugging (optional)
            \Log::error('Error saving personal profile: ' . $e->getMessage());

            // dd($e->getMessage());
            // Redirect back with an error message
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while saving. Please try again.');
        }
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
}
