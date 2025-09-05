<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateLeaveApplication;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\Soldier;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    //
    // leave lists
    public function index()
    {
        $leaveType = LeaveType::get();
        $profiles = Soldier::get();

        return view('mpm.page.leave.leaveList', compact('profiles', 'leaveType'));
    }
    public function leaveApplicationSubmit(StoreUpdateLeaveApplication $request)
    {
        try {
            $data = $request->validated();
            // Handle image upload
            if ($request->hasFile('application_file')) {
                $data['hard_copy'] = $request->file('application_file')->store('leave_applications', 'public');
            }
            LeaveApplication::create($data);
            // Redirect to next step with success message
            return redirect()->route('leave.index')
                ->with('success', 'Leave Application submitted successfully.');
        } catch (\Exception $e) {
            // Log the error for debugging (optional)
            \Log::error('Error saving personal profile: ' . $e->getMessage());

            // Redirect back with an error message
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while saving. Please try again.');
        }
    }
    public function approvalList() {}
    public function approvalAction() {}
}
