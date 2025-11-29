<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateAbsentApplication;
use App\Models\Absent;
use App\Models\AbsentType;
use App\Models\Soldier;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsentController extends Controller
{
    // absent lists
    public function index()
    {
        $absentType = AbsentType::get();
        $profiles = Soldier::get();

        $query = Absent::query();
        $absentDatas = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();
        return view('mpm.page.absent.absentList', compact('profiles', 'absentType', 'absentDatas'));
    }

    // Ajax filtering for absent applications
    public function filter(Request $request)
    {
        try {
            $query = Absent::with(['soldier', 'absentType', 'soldier.rank']);

            // Simple date range filter - only by start_date
            if ($request->filled('from_date')) {
                $query->where('start_date', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->where('start_date', '<=', $request->to_date);
            }

            // Absent type filter
            if ($request->filled('absent_type_id')) {
                $query->where('absent_type_id', $request->absent_type_id);
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('absent_current_status', $request->status);
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

            $absentDatas = $query->paginate(10)->withQueryString();

            if ($request->ajax()) {
                $table = view('mpm.components.absent-table', compact('absentDatas'))->render();
                $pagination = $absentDatas->links()->render();

                return response()->json([
                    'success' => true,
                    'table' => $table,
                    'pagination' => $pagination,
                    'total' => $absentDatas->total()
                ]);
            }

            return back();
        } catch (\Exception $e) {
            \Log::error('Error filtering absent applications: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error filtering data. Please try again.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error filtering data. Please Try again.');
        }
    }

    public function absentApplicationSubmit(StoreUpdateAbsentApplication $request)
    {
        try {
            $data = $request->validated();

            // Handle multiple soldiers
            $soldierIds = $request->soldier_ids ?? [];

            // Handle image upload
            if ($request->hasFile('application_file')) {
                $data['hard_copy'] = $request->file('application_file')->store('profiles', 'public');
            }

            // Create absent record for each soldier
            foreach ($soldierIds as $soldierId) {
                $data['soldier_id'] = $soldierId;
                Absent::create($data);
            }

            // Redirect to next step with success message
            return redirect()->route('absent.index')
                ->with('success', 'Absent Application submitted successfully for ' . count($soldierIds) . ' soldier(s).');
        } catch (\Exception $e) {
            // Log the error for debugging (optional)
            \Log::error('Error saving absent application: ' . $e->getMessage());

            // Redirect back with an error message
            return redirect()->back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while saving. Please try again.');
        }
    }

    public function changeStatus(Request $request)
    {
        $validated = $request->validate([
            'absent_current_status' => ['required', 'string'],
            'status_reason' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->absent_current_status === 'rejected' && empty($value)) {
                        $fail('Reason is required when rejecting.');
                    }
                },
            ],
        ]);

        $absent = Absent::findOrFail($request->absent_id);

        $absent->update([
            'absent_current_status' => $validated['absent_current_status'],
            'reject_reason' => $request->status_reason,
            'reject_status_date' => now(),
        ]);

        return back()->with('success', 'Absent status updated successfully.');
    }

    public function update(StoreUpdateAbsentApplication $request, $id)
    {
        $absent = Absent::findOrFail($id);

        // If user uploaded a new file
        if ($request->hasFile('application_file')) {
            // Delete old file if exists
            if ($absent->hard_copy && \Storage::exists('public/' . $absent->hard_copy)) {
                \Storage::delete('public/' . $absent->hard_copy);
            }

            $path = $request->file('application_file')->store('absent_files', 'public');
            $absent->hard_copy = $path;
        }

        // If user clicked "remove file"
        if ($request->remove_hard_copy == "1") {
            if ($absent->hard_copy && \Storage::exists('public/' . $absent->hard_copy)) {
                \Storage::delete('public/' . $absent->hard_copy);
            }
            $absent->hard_copy = null; // Remove from DB
        }

        // Other fields update
        $absent->reason = $request->reason;
        $absent->start_date = $request->start_date;
        $absent->end_date = $request->end_date;
        $absent->absent_type_id = $request->absent_type_id;
        $absent->soldier_id = $request->soldier_ids[0] ?? $absent->soldier_id; // For edit, use first soldier

        $absent->save();

        return redirect()->route('absent.index')->with('success', 'Absent updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $absent = Absent::findOrFail($id);

            // Delete associated file if exists
            if ($absent->hard_copy && \Storage::exists('public/' . $absent->hard_copy)) {
                \Storage::delete('public/' . $absent->hard_copy);
            }

            $absent->delete();

            return redirect()->route('absent.index')->with('success', 'Absent Application deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting absent application: ' . $e->getMessage());
            return redirect()->route('absent.index')->with('error', 'Error deleting absent application. Please try again.');
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
