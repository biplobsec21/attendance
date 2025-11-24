@extends('mpm.layouts.app')

@section('title', 'Leave List')

@section('content')
    @include('mpm.components.leave-nav')

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container mx-auto p-4">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 formBack">
            <!-- Leave Filters Component -->
            <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Filter Leave Applications</h2>

                <form id="leaveFiltersForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @csrf

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                        <input type="date" name="from_date" id="filterFromDate"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                        <input type="date" name="to_date" id="filterToDate"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    </div>

                    <!-- Leave Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                        <select name="leave_type_id" id="filterLeaveType"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none">
                            <option value="">All Leave Types</option>
                            @foreach ($leaveType as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="filterStatus"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <!-- Soldier Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search Soldier</label>
                        <input type="text" name="soldier_search" id="filterSoldierSearch"
                            placeholder="Search by name or army number..."
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none">
                    </div>

                    <!-- Filter Actions -->
                    <div class="flex gap-2 items-end md:col-span-2">
                        <button type="submit" id="applyFilters"
                            class="px-6 py-2 bg-orange-500 text-white font-bold rounded-lg hover:bg-orange-600 transition-colors">
                            Apply Filters
                        </button>
                        <button type="button" id="resetFilters"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Reset
                        </button>
                        <div id="filterLoading" class="hidden flex items-center text-orange-600">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Loading...
                        </div>
                    </div>
                </form>
            </div>



            <!-- Status Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Pending Card -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 shadow-sm status-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-800">Pending</h3>
                            <p class="text-2xl font-bold text-yellow-600" id="pendingCount">0</p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-yellow-600 mt-2">Awaiting approval</p>
                </div>

                <!-- Approved Card -->
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-green-800">Approved</h3>
                            <p class="text-2xl font-bold text-green-600" id="approvedCount">0</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-green-600 mt-2">Leave granted</p>
                </div>

                <!-- Rejected Card -->
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-red-800">Rejected</h3>
                            <p class="text-2xl font-bold text-red-600" id="rejectedCount">0</p>
                        </div>
                        <div class="bg-red-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-red-600 mt-2">Leave not approved</p>
                </div>
            </div>

            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h1 class="text-3xl font-bold text-gray-800">Leave Application List</h1>

                <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                    <!-- Bulk Actions -->
                    <div id="bulkActions"
                        class="hidden flex items-center gap-3 bg-orange-50 border border-orange-200 rounded-lg p-3">
                        <span id="selectedCount" class="text-sm font-medium text-orange-800">0 selected</span>

                        <select id="bulkStatusSelect"
                            class="border rounded-lg px-3 py-1 text-sm focus:ring-2 focus:ring-orange-400 focus:outline-none">
                            <option value="">Select Action</option>
                            <option value="approved">Approve Selected</option>
                            <option value="rejected">Reject Selected</option>
                            <option value="pending">Mark as Pending</option>
                            <option value="delete">Delete Selected</option>
                        </select>

                        <button id="applyBulkAction"
                            class="px-3 py-1 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors">
                            Apply
                        </button>

                        <button id="clearSelection"
                            class="px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                            Clear
                        </button>
                    </div>

                    <!-- Add new leave application button -->
                    <button id="openLeaveModal"
                        class="flex items-center gap-2 px-4 py-2 border border-orange-400 text-black font-bold rounded-lg hover:bg-orange-50 hover:border-orange-500 transition-colors duration-200">
                        + Add new Leave application
                    </button>
                </div>
            </div>
            <div id="leaveTableContainer">
                @include('mpm.components.leave-table', ['leaveDatas' => $leaveDatas])
            </div>

            <!-- Pagination Container (will be updated via AJAX) -->
            <div id="paginationContainer" class="mt-6">
                @if ($leaveDatas->hasPages())
                    <div class="flex justify-center items-center space-x-2 flex-wrap">
                        {{ $leaveDatas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('modals')
    <!-- Bulk Delete Confirmation Modal -->
    <div id="bulkDeleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirm Bulk Delete</h2>
            <p class="text-gray-600 mb-4">Are you sure you want to delete <span id="bulkDeleteCount"
                    class="font-semibold">0</span> selected leave applications?</p>
            <p class="text-red-600 text-sm mb-6">This action cannot be undone and all associated files will be permanently
                deleted.</p>

            <div class="flex justify-end gap-3">
                <button id="cancelBulkDelete"
                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <form id="bulkDeleteForm" method="POST" action="{{ route('leave.bulkDelete') }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <div id="bulkDeleteIdsContainer">
                        <!-- Leave IDs will be populated here by JavaScript -->
                    </div>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors">
                        Yes, Delete All
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- Leave Modal -->
    <!-- Leave Modal -->
    <div id="leaveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] flex flex-col mx-4">
            <!-- Modal Header -->
            <div class="flex-shrink-0 flex justify-between items-center border-b pb-3 mb-4 p-6">
                <h2 class="text-xl font-bold text-gray-800">New Leave Application</h2>
                <button id="closeLeaveModal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>

            <!-- Modal Form - Scrollable Content -->
            <div class="flex-1 overflow-y-auto px-6">
                <form id="leaveForm" action="{{ route('leave.leaveApplicationSubmit') }}" method="POST"
                    enctype="multipart/form-data" class="space-y-4 pb-4">
                    @csrf
                    <input type="hidden" name="leave_id" id="leave_id">

                    <!-- Profile Dropdown with Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Select Soldiers *</label>

                        <!-- Selected Soldiers Display -->
                        <div id="selectedSoldiersDisplay"
                            class="mb-2 p-3 bg-green-50 border border-green-200 rounded-lg hidden">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-green-800 mb-1">Selected Soldiers:</p>
                                    <div id="selectedSoldiersList" class="flex flex-wrap gap-2 max-h-20 overflow-y-auto">
                                        <!-- Selected soldiers will appear here -->
                                    </div>
                                </div>
                                <button type="button" id="changeSoldiersBtn"
                                    class="text-green-600 hover:text-green-800 text-sm font-medium whitespace-nowrap ml-2">
                                    Clear Search
                                </button>
                            </div>
                            <p class="text-xs text-green-600 mt-2" id="selectedCountDisplay">0 soldiers selected</p>
                        </div>

                        <!-- Search Input and Multi-Select (initially visible) -->
                        <div id="soldiersSearchSection">
                            <!-- Search Input -->
                            <div class="relative mb-2">
                                <input type="text" id="soldiersSearch" placeholder="Search by name or army number..."
                                    class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Soldiers Multi-Select -->
                            <div class="max-h-48 overflow-y-auto border rounded-lg bg-white">
                                <div id="soldiersMultiSelect" class="p-2 space-y-1">
                                    @foreach ($profiles as $profile)
                                        <label
                                            class="flex items-center p-2 hover:bg-orange-50 rounded cursor-pointer transition-colors soldier-item">
                                            <input type="checkbox" name="soldier_ids[]" value="{{ $profile->id }}"
                                                class="soldier-checkbox rounded border-gray-300 text-orange-600 focus:ring-orange-500 mr-3"
                                                data-name="{{ strtolower($profile->full_name) }}"
                                                data-army-no="{{ $profile->army_no }}"
                                                data-rank="{{ $profile->rank->name ?? 'N/A' }}"
                                                data-display-name="{{ $profile->full_name }} ({{ $profile->army_no }}) - {{ $profile->rank->name ?? 'N/A' }}">
                                            <span class="text-sm soldier-info">
                                                {{ $profile->full_name }}
                                                @if ($profile->army_no)
                                                    ({{ $profile->army_no }})
                                                @endif
                                                - {{ $profile->rank->name ?? 'N/A' }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="flex justify-between items-center mt-2">
                                <button type="button" id="selectAllSoldiers"
                                    class="text-xs text-orange-600 hover:text-orange-800 font-medium">
                                    Select All
                                </button>
                                <button type="button" id="clearSoldiersSelection"
                                    class="text-xs text-gray-600 hover:text-gray-800 font-medium">
                                    Clear All
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Type Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Leave Type *</label>
                        <select name="leave_type_id" id="leaveTypeSelect"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"
                            required>
                            <option value="">-- Select Leave Type --</option>
                            @foreach ($leaveType as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">From Date *</label>
                            <input type="date" id="fromDate" name="start_date"
                                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Date *</label>
                            <input type="date" id="endDate" name="end_date"
                                class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"
                                required>
                        </div>
                    </div>

                    <!-- Total Days -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Days</label>
                        <input type="number" id="totalDays" name="total_days"
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed" readonly>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label for="application_file" class="block text-sm font-medium text-gray-700">Application
                            Copy</label>
                        <input type="file" name="application_file" id="application_file"
                            class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            accept="image/*">

                        <!-- Preview -->
                        <div id="filePreviewWrapper" class="mt-3 hidden">
                            <p class="text-xs text-gray-500 mb-1">Preview:</p>
                            <div class="relative inline-block">
                                <img id="filePreview" src=""
                                    class="w-32 h-32 rounded-lg border border-gray-200 object-cover shadow-md">
                                <button type="button" id="removeFileBtn"
                                    class="absolute top-0 right-0 bg-red-500 text-white text-xs px-2 py-1 rounded-full shadow hover:bg-red-600">
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reason</label>
                        <textarea name="reason" id="reasonTextarea" rows="3"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"></textarea>
                    </div>
                    <input type="hidden" name="remove_hard_copy" id="removeHardCopy" value="0">
                </form>
            </div>

            <!-- Modal Actions - Fixed at bottom -->
            <div class="flex-shrink-0 border-t bg-white rounded-b-xl p-6">
                <div class="flex justify-end gap-3">
                    <button type="button" id="closeLeaveModal2"
                        class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn" form="leaveForm"
                        class="px-4 py-2 bg-orange-500 text-white font-bold rounded-lg hover:bg-orange-600 transition-colors">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Status Modal -->
    <div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h2 class="text-lg font-bold text-gray-800">Change Status</h2>
                <button id="closeStatusModal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>

            <form id="statusForm" method="POST" action="{{ route('leave.changeStatusSubmit') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="leave_id" id="statusLeaveId">

                <div>
                    <label class="block text-sm font-medium text-gray-700">New Status</label>
                    <select name="application_current_status" id="statusSelect"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"
                        required>
                        <option value="pending">Pending</option>
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="status_reason" class="block text-sm font-medium text-gray-700">
                        Reason <span id="reasonRequiredMark" class="text-red-500 hidden">*</span>
                    </label>
                    <textarea name="status_reason" id="statusReason" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-400 focus:border-orange-400"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Required only when rejecting applications.</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" id="closeStatusModal2"
                        class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-orange-500 text-white font-bold rounded-lg hover:bg-orange-600 transition-colors">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
        <div class="relative bg-white rounded-xl shadow-lg max-w-3xl p-4">
            <button id="closeImageModal"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            <img id="modalImage" src="" alt="Preview"
                class="max-h-[80vh] max-w-full object-contain rounded-lg shadow">
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirm Delete</h2>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this leave application? This action cannot be
                undone.</p>

            <div class="flex justify-end gap-3">
                <button id="cancelDelete"
                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Status Modal -->
    <div id="bulkStatusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h2 class="text-lg font-bold text-gray-800" id="bulkModalTitle">Bulk Status Update</h2>
                <button id="closeBulkStatusModal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>

            <form id="bulkStatusForm" method="POST" action="{{ route('leave.bulkStatusUpdate') }}" class="space-y-4">
                @csrf
                <div id="bulkLeaveIdsContainer">
                    <!-- Leave IDs will be populated here by JavaScript -->
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">New Status</label>
                    <select name="application_current_status" id="bulkModalStatusSelect"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"
                        required>
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                        <option value="pending">Mark as Pending</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="bulk_status_reason" class="block text-sm font-medium text-gray-700">
                        Reason <span id="bulkReasonRequiredMark" class="text-red-500 hidden">*</span>
                    </label>
                    <textarea name="status_reason" id="bulkStatusReason" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-400 focus:border-orange-400"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Required only when rejecting applications.</p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" id="closeBulkStatusModal2"
                        class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">Cancel</button>
                    <button type="submit" id="submitBulkAction"
                        class="px-4 py-2 bg-orange-500 text-white font-bold rounded-lg hover:bg-orange-600 transition-colors">
                        Update <span id="bulkActionCount">0</span> Applications
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Loading animation */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Bulk selection styles */
        input[type="checkbox"]:indeterminate {
            background-color: #f97316;
            border-color: #f97316;
        }

        .bulk-actions {
            transition: all 0.3s ease;
        }

        /* Ensure checkboxes are properly sized and aligned */
        .leave-checkbox,
        .soldier-checkbox {
            width: 1.1rem;
            height: 1.1rem;
        }

        #selectAll {
            width: 1.1rem;
            height: 1.1rem;
        }

        /* Multiple Soldier Selection Styles */
        #selectedSoldiersList {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        #selectedSoldiersList::-webkit-scrollbar {
            width: 6px;
        }

        #selectedSoldiersList::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 3px;
        }

        #selectedSoldiersList::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }

        #selectedSoldiersList::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .soldier-checkbox:checked+.soldier-info {
            font-weight: 600;
            color: #ea580c;
        }

        /* Smooth transitions */
        .soldier-item {
            transition: all 0.2s ease-in-out;
        }

        /* Hover effects */
        .soldier-item:hover {
            transform: translateX(2px);
        }

        /* Smooth scrolling for modal */
        .modal-content {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .modal-content::-webkit-scrollbar {
            width: 6px;
        }

        .modal-content::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 3px;
        }

        .modal-content::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }

        .modal-content::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        /* Status Summary Cards Animation */
        .status-card {
            transition: all 0.3s ease;
        }

        .status-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@push('scripts')
    <script>
        // =================== AJAX FILTERING FUNCTIONALITY ===================
        const filtersForm = document.getElementById('leaveFiltersForm');
        const applyFiltersBtn = document.getElementById('applyFilters');
        const resetFiltersBtn = document.getElementById('resetFilters');
        const filterLoading = document.getElementById('filterLoading');
        const leaveTableContainer = document.getElementById('leaveTableContainer');
        const paginationContainer = document.getElementById('paginationContainer');

        // Apply filters
        filtersForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadFilteredData();
        });

        // Reset filters
        resetFiltersBtn.addEventListener('click', function() {
            filtersForm.reset();
            loadFilteredData();
        });

        // Auto-apply filters when inputs change
        document.getElementById('filterSoldierSearch').addEventListener('input', debounce(loadFilteredData, 500));
        document.getElementById('filterLeaveType').addEventListener('change', loadFilteredData);
        document.getElementById('filterStatus').addEventListener('change', loadFilteredData);
        document.getElementById('filterFromDate').addEventListener('change', loadFilteredData);
        document.getElementById('filterToDate').addEventListener('change', loadFilteredData);

        function loadFilteredData(page = 1) {
            const formData = new FormData(filtersForm);
            formData.append('page', page);

            filterLoading.classList.remove('hidden');
            applyFiltersBtn.disabled = true;

            fetch('{{ route('leave.filter') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    leaveTableContainer.innerHTML = data.table;
                    paginationContainer.innerHTML = data.pagination;

                    // Update status counts if available
                    if (data.counts) {
                        updateStatusCounts(data.counts);
                    }

                    reinitializeEventListeners();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading filtered data. Please try again.');
                })
                .finally(() => {
                    filterLoading.classList.add('hidden');
                    applyFiltersBtn.disabled = false;
                });
        }
        // Function to update status counts
        function updateStatusCounts(counts) {
            const pendingCountEl = document.getElementById('pendingCount');
            const approvedCountEl = document.getElementById('approvedCount');
            const rejectedCountEl = document.getElementById('rejectedCount');

            if (pendingCountEl) pendingCountEl.textContent = counts.pending || 0;
            if (approvedCountEl) approvedCountEl.textContent = counts.approved || 0;
            if (rejectedCountEl) rejectedCountEl.textContent = counts.rejected || 0;
        }
        // Debounce function to limit API calls
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // =================== LEAVE MODAL ===================
        const openBtn = document.getElementById('openLeaveModal');
        const closeBtn = document.getElementById('closeLeaveModal');
        const closeBtn2 = document.getElementById('closeLeaveModal2');
        const modal = document.getElementById('leaveModal');
        const leaveForm = document.getElementById('leaveForm');
        const submitBtn = document.getElementById('submitBtn');

        openBtn.addEventListener('click', () => modal.classList.remove('hidden'));
        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
        closeBtn2.addEventListener('click', () => modal.classList.add('hidden'));

        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
        // =================== MULTIPLE SOLDIER SELECTION FUNCTIONALITY ===================
        const soldiersSearch = document.getElementById('soldiersSearch');
        const soldiersMultiSelect = document.getElementById('soldiersMultiSelect');
        const selectedSoldiersDisplay = document.getElementById('selectedSoldiersDisplay');
        const selectedSoldiersList = document.getElementById('selectedSoldiersList');
        const selectedCountDisplay = document.getElementById('selectedCountDisplay');
        const changeSoldiersBtn = document.getElementById('changeSoldiersBtn');
        const soldiersSearchSection = document.getElementById('soldiersSearchSection');
        const selectAllSoldiersBtn = document.getElementById('selectAllSoldiers');
        const clearSoldiersBtn = document.getElementById('clearSoldiersSelection');
        const soldierCheckboxes = document.querySelectorAll('.soldier-checkbox');

        // Soldier search functionality
        soldiersSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const labels = soldiersMultiSelect.querySelectorAll('.soldier-item');

            labels.forEach(label => {
                const checkbox = label.querySelector('.soldier-checkbox');
                const soldierName = checkbox.getAttribute('data-name') || '';
                const armyNo = checkbox.getAttribute('data-army-no') || '';
                const displayText = label.querySelector('.soldier-info').textContent.toLowerCase();

                if (soldierName.includes(searchTerm) || armyNo.includes(searchTerm) || displayText.includes(
                        searchTerm)) {
                    label.style.display = 'flex';
                } else {
                    label.style.display = 'none';
                }
            });
        });

        // Update selected soldiers display
        function updateSelectedSoldiersDisplay() {
            const selectedCheckboxes = document.querySelectorAll('.soldier-checkbox:checked');
            const selectedCount = selectedCheckboxes.length;

            // Update count display
            selectedCountDisplay.textContent = `${selectedCount} soldier${selectedCount !== 1 ? 's' : ''} selected`;

            // Update selected soldiers list
            selectedSoldiersList.innerHTML = '';

            selectedCheckboxes.forEach(checkbox => {
                const displayName = checkbox.getAttribute('data-display-name');
                const soldierId = checkbox.value;

                const badge = document.createElement('div');
                badge.className =
                    'flex items-center gap-1 bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs';
                badge.innerHTML = `
            ${displayName}
            <button type="button" onclick="deselectSoldier('${soldierId}')"
                class="text-green-600 hover:text-green-800 ml-1 text-xs">
                ×
            </button>
        `;
                selectedSoldiersList.appendChild(badge);
            });

            // ALWAYS SHOW BOTH SECTIONS - Never hide the search section
            if (selectedCount > 0) {
                selectedSoldiersDisplay.classList.remove('hidden');
            } else {
                selectedSoldiersDisplay.classList.add('hidden');
            }
            // Keep soldiersSearchSection always visible
            soldiersSearchSection.classList.remove('hidden');
        }

        // Deselect individual soldier
        window.deselectSoldier = function(soldierId) {
            const checkbox = document.querySelector(`.soldier-checkbox[value="${soldierId}"]`);
            if (checkbox) {
                checkbox.checked = false;
                updateSelectedSoldiersDisplay();
            }
        };

        // Remove the changeSoldiersBtn functionality since we don't need to toggle anymore
        // Instead, let's use it to clear the search and show all soldiers
        changeSoldiersBtn.addEventListener('click', function() {
            soldiersSearch.value = '';

            // Reset search display
            const labels = soldiersMultiSelect.querySelectorAll('.soldier-item');
            labels.forEach(label => {
                label.style.display = 'flex';
            });

            // Focus on search input for better UX
            soldiersSearch.focus();
        });

        // Select all VISIBLE soldiers
        selectAllSoldiersBtn.addEventListener('click', function() {
            const visibleLabels = soldiersMultiSelect.querySelectorAll('.soldier-item');
            let selectedCount = 0;

            visibleLabels.forEach(label => {
                if (label.style.display !== 'none') {
                    const checkbox = label.querySelector('.soldier-checkbox');
                    if (checkbox && !checkbox.checked) {
                        checkbox.checked = true;
                        selectedCount++;
                    }
                }
            });

            if (selectedCount > 0) {
                updateSelectedSoldiersDisplay();
            }
        });

        // Clear all selection
        clearSoldiersBtn.addEventListener('click', function() {
            soldierCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedSoldiersDisplay();
        });

        // Handle checkbox changes
        soldierCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedSoldiersDisplay);
        });

        // Clear everything when modal opens for new application
        openBtn.addEventListener('click', () => {
            soldiersSearch.value = '';
            soldierCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            selectedSoldiersDisplay.classList.add('hidden');
            // Keep soldiersSearchSection always visible - no need to change

            // Reset search display
            const labels = soldiersMultiSelect.querySelectorAll('.soldier-item');
            labels.forEach(label => {
                label.style.display = 'flex';
            });
        });

        // =================== AUTO CALCULATE DAYS ===================
        const fromDateEl = document.getElementById('fromDate');
        const endDateEl = document.getElementById('endDate');
        const totalDaysEl = document.getElementById('totalDays');

        function calculateDays() {
            const fromDate = new Date(fromDateEl.value);
            const endDate = new Date(endDateEl.value);

            if (!isNaN(fromDate) && !isNaN(endDate) && endDate >= fromDate) {
                const diffTime = endDate - fromDate;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // include both dates
                totalDaysEl.value = diffDays;
            } else {
                totalDaysEl.value = '';
            }
        }

        fromDateEl.addEventListener('change', calculateDays);
        endDateEl.addEventListener('change', calculateDays);

        // =================== FORM VALIDATION ===================
        // =================== FORM VALIDATION ===================
        leaveForm.addEventListener('submit', function(e) {
            const selectedSoldiers = document.querySelectorAll('.soldier-checkbox:checked');

            // For new applications, check multiple soldiers
            if (!document.getElementById('_method')) { // New application
                if (selectedSoldiers.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one soldier.');
                    return false;
                }
            } else { // Edit application
                // For edits, we already have single soldier selected
                if (selectedSoldiers.length === 0) {
                    e.preventDefault();
                    alert('Please select a soldier.');
                    return false;
                }
            }

            if (!leaveForm.leave_type_id.value) {
                e.preventDefault();
                alert('Please select a Leave Type.');
                leaveForm.leave_type_id.focus();
                return false;
            }

            if (!fromDateEl.value) {
                e.preventDefault();
                alert('Please select a From Date.');
                fromDateEl.focus();
                return false;
            }

            if (!endDateEl.value) {
                e.preventDefault();
                alert('Please select an End Date.');
                endDateEl.focus();
                return false;
            }

            if (!totalDaysEl.value || totalDaysEl.value <= 0) {
                e.preventDefault();
                alert('Total days must be greater than 0.');
                return false;
            }

            submitBtn.disabled = true;
            submitBtn.innerText = 'Submitting...';
        });

        // =================== STATUS MODAL ===================
        const statusModal = document.getElementById('statusModal');
        const closeStatusModal = document.getElementById('closeStatusModal');
        const closeStatusModal2 = document.getElementById('closeStatusModal2');
        const statusLeaveId = document.getElementById('statusLeaveId');
        const statusSelect = document.getElementById('statusSelect');
        const statusForm = document.getElementById('statusForm');
        const statusReason = document.getElementById('statusReason');
        const reasonMark = document.getElementById('reasonRequiredMark');

        function initializeStatusButtons() {
            document.querySelectorAll('.openStatusModal').forEach(btn => {
                btn.addEventListener('click', function() {
                    const status = this.dataset.status;
                    const reason = this.dataset.reject_reason || '';

                    statusLeaveId.value = this.dataset.id;
                    statusSelect.value = status;
                    statusReason.value = reason;
                    statusReason.classList.remove('border-red-500');
                    reasonMark.classList.add('hidden');
                    statusModal.classList.remove('hidden');
                    statusSelect.dispatchEvent(new Event('change'));
                });
            });
        }

        [closeStatusModal, closeStatusModal2].forEach(btn => {
            btn.addEventListener('click', () => statusModal.classList.add('hidden'));
        });

        statusSelect.addEventListener('change', () => {
            if (statusSelect.value === 'rejected') {
                reasonMark.classList.remove('hidden');
                statusReason.setAttribute('required', 'required');
            } else {
                reasonMark.classList.add('hidden');
                statusReason.removeAttribute('required');
            }
        });

        statusForm.addEventListener('submit', function(e) {
            if (statusSelect.value === 'rejected' && statusReason.value.trim() === '') {
                e.preventDefault();
                alert('Please provide a reason for rejection.');
                statusReason.focus();
            }
        });

        // =================== IMAGE MODAL ===================
        const imageModal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const closeImageModal = document.getElementById('closeImageModal');

        function initializeImageModals() {
            document.querySelectorAll('.openImageModal').forEach(img => {
                img.addEventListener('click', () => {
                    modalImage.src = img.dataset.img;
                    imageModal.classList.remove('hidden');
                });
            });
        }
        closeImageModal.addEventListener('click', () => imageModal.classList.add('hidden'));

        // =================== FILE PREVIEW ===================
        document.getElementById('application_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('filePreview').src = event.target.result;
                    document.getElementById('filePreviewWrapper').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
        document.getElementById('removeFileBtn').addEventListener('click', function() {
            document.getElementById('application_file').value = "";
            document.getElementById('filePreview').src = "";
            document.getElementById('filePreviewWrapper').classList.add('hidden');
            document.getElementById('removeHardCopy').value = "1";
        });

        // =================== DELETE MODAL ===================
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const cancelDelete = document.getElementById('cancelDelete');

        function initializeDeleteButtons() {
            document.querySelectorAll('.deleteBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const leaveId = this.dataset.id;
                    deleteForm.action = `/leave/${leaveId}`;
                    deleteModal.classList.remove('hidden');
                });
            });
        }

        cancelDelete.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                deleteModal.classList.add('hidden');
            }
        });

        // =================== BULK SELECTION FUNCTIONALITY ===================
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');
        const bulkStatusSelect = document.getElementById('bulkStatusSelect');
        const applyBulkAction = document.getElementById('applyBulkAction');
        const clearSelection = document.getElementById('clearSelection');
        const bulkStatusModal = document.getElementById('bulkStatusModal');
        const bulkStatusForm = document.getElementById('bulkStatusForm');
        const bulkModalStatusSelect = document.getElementById('bulkModalStatusSelect');
        const bulkStatusReason = document.getElementById('bulkStatusReason');
        const bulkReasonRequiredMark = document.getElementById('bulkReasonRequiredMark');
        const bulkActionCount = document.getElementById('bulkActionCount');
        const closeBulkStatusModal = document.getElementById('closeBulkStatusModal');
        const closeBulkStatusModal2 = document.getElementById('closeBulkStatusModal2');

        // Bulk Delete Elements
        const bulkDeleteModal = document.getElementById('bulkDeleteModal');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
        const bulkDeleteCount = document.getElementById('bulkDeleteCount');
        const cancelBulkDelete = document.getElementById('cancelBulkDelete');
        const bulkDeleteIdsContainer = document.getElementById('bulkDeleteIdsContainer');

        // Select All functionality
        function initializeBulkSelection() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.leave-checkbox');

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkSelectionUI();
                });
            }

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkSelectionUI);
            });

            // Initialize bulk action button
            applyBulkAction.addEventListener('click', function() {
                const checkedBoxes = document.querySelectorAll('.leave-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    alert('Please select at least one leave application.');
                    return;
                }

                const action = bulkStatusSelect.value;
                if (!action) {
                    alert('Please select an action.');
                    return;
                }

                if (action === 'delete') {
                    openBulkDeleteModal(checkedBoxes);
                } else {
                    openBulkStatusModal(checkedBoxes, action);
                }
            });

            // Clear selection
            clearSelection.addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                if (selectAll) selectAll.checked = false;
                updateBulkSelectionUI();
            });
        }

        function updateBulkSelectionUI() {
            const checkedBoxes = document.querySelectorAll('.leave-checkbox:checked');
            const checkedCount = checkedBoxes.length;
            const selectAll = document.getElementById('selectAll');

            if (selectedCount) {
                selectedCount.textContent = `${checkedCount} selected`;
            }

            if (bulkActions) {
                if (checkedCount > 0) {
                    bulkActions.classList.remove('hidden');
                } else {
                    bulkActions.classList.add('hidden');
                }
            }

            // Update select all checkbox state
            if (selectAll) {
                selectAll.checked = checkedCount > 0 && checkedCount === document.querySelectorAll('.leave-checkbox')
                    .length;
                selectAll.indeterminate = checkedCount > 0 && checkedCount < document.querySelectorAll('.leave-checkbox')
                    .length;
            }
        }

        function openBulkStatusModal(checkedBoxes, status) {
            const leaveIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);

            // Populate hidden inputs for leave IDs
            const container = document.getElementById('bulkLeaveIdsContainer');
            container.innerHTML = ''; // Clear existing

            leaveIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'leave_ids[]';
                input.value = id;
                container.appendChild(input);
            });

            // Set the status
            bulkModalStatusSelect.value = status;
            bulkActionCount.textContent = leaveIds.length;

            // Reset reason field
            bulkStatusReason.value = '';
            bulkReasonRequiredMark.classList.add('hidden');

            // Show modal
            bulkStatusModal.classList.remove('hidden');

            // Trigger change event to update UI
            bulkModalStatusSelect.dispatchEvent(new Event('change'));
        }

        function openBulkDeleteModal(checkedBoxes) {
            const leaveIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);

            // Update delete count
            bulkDeleteCount.textContent = leaveIds.length;

            // Populate hidden inputs for leave IDs
            bulkDeleteIdsContainer.innerHTML = ''; // Clear existing

            leaveIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'leave_ids[]';
                input.value = id;
                bulkDeleteIdsContainer.appendChild(input);
            });

            // Show modal
            bulkDeleteModal.classList.remove('hidden');
        }

        // Bulk status modal event listeners
        [closeBulkStatusModal, closeBulkStatusModal2].forEach(btn => {
            btn.addEventListener('click', () => bulkStatusModal.classList.add('hidden'));
        });

        // Bulk delete modal event listeners
        cancelBulkDelete.addEventListener('click', () => {
            bulkDeleteModal.classList.add('hidden');
        });

        bulkDeleteModal.addEventListener('click', (e) => {
            if (e.target === bulkDeleteModal) {
                bulkDeleteModal.classList.add('hidden');
            }
        });

        bulkModalStatusSelect.addEventListener('change', function() {
            if (this.value === 'rejected') {
                bulkReasonRequiredMark.classList.remove('hidden');
                bulkStatusReason.setAttribute('required', 'required');
            } else {
                bulkReasonRequiredMark.classList.add('hidden');
                bulkStatusReason.removeAttribute('required');
            }
        });

        bulkStatusForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBulkAction');

            // Client-side validation
            if (bulkModalStatusSelect.value === 'rejected' && !bulkStatusReason.value.trim()) {
                alert('Please provide a reason for rejection.');
                bulkStatusReason.focus();
                return;
            }

            // Disable submit button and show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Updating...';

            fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert(data.message);
                        // Close modal
                        bulkStatusModal.classList.add('hidden');
                        // Reload the filtered data (which will update counts)
                        loadFilteredData();
                        // Clear selection
                        document.querySelectorAll('.leave-checkbox:checked').forEach(checkbox => {
                            checkbox.checked = false;
                        });
                        document.getElementById('selectAll').checked = false;
                        updateBulkSelectionUI();
                    } else {
                        alert(data.message || 'Error updating leave applications.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating leave applications. Please try again.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML =
                        `Update <span id="bulkActionCount">${formData.getAll('leave_ids[]').length}</span> Applications`;
                });
        });

        // Bulk delete form submission
        bulkDeleteForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');

            // Disable submit button and show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Deleting...';

            fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert(data.message);
                        // Close modal
                        bulkDeleteModal.classList.add('hidden');
                        // Reload the filtered data (which will update counts)
                        loadFilteredData();
                        // Clear selection
                        document.querySelectorAll('.leave-checkbox:checked').forEach(checkbox => {
                            checkbox.checked = false;
                        });
                        document.getElementById('selectAll').checked = false;
                        updateBulkSelectionUI();
                    } else {
                        alert(data.message || 'Error deleting leave applications.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting leave applications. Please try again.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Yes, Delete All';
                });
        });

        // =================== EDIT FUNCTIONALITY ===================
        function initializeEditButtons() {
            document.querySelectorAll('.editLeaveBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const leaveId = this.dataset.id;
                    const soldierId = this.dataset.soldier;
                    const leaveTypeId = this.dataset.leavetype;
                    const startDate = this.dataset.start;
                    const endDate = this.dataset.end;
                    const reason = this.dataset.reason ?? '';
                    const hardcopy = this.dataset.hardcopy;

                    // Set form values - for edit, we only support single soldier
                    document.getElementById('leave_id').value = leaveId;

                    // Clear all selections first
                    soldierCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Select the single soldier
                    if (soldierId) {
                        const soldierCheckbox = document.querySelector(
                            `.soldier-checkbox[value="${soldierId}"]`);
                        if (soldierCheckbox) {
                            soldierCheckbox.checked = true;
                        }
                    }

                    updateSelectedSoldiersDisplay();

                    document.getElementById('leaveTypeSelect').value = leaveTypeId;
                    document.getElementById('fromDate').value = startDate;
                    document.getElementById('endDate').value = endDate;
                    document.getElementById('reasonTextarea').value = reason;

                    calculateDays();

                    if (hardcopy && hardcopy !== 'null') {
                        document.getElementById('filePreview').src = `/storage/${hardcopy}`;
                        document.getElementById('filePreviewWrapper').classList.remove('hidden');
                    } else {
                        document.getElementById('filePreview').src = "";
                        document.getElementById('filePreviewWrapper').classList.add('hidden');
                    }

                    document.querySelector('#leaveModal h2').innerText = "Edit Leave Application";
                    submitBtn.innerText = "Update";
                    leaveForm.action = "{{ route('leave.update', ':id') }}".replace(':id', leaveId);

                    // Add method override for PUT
                    let methodInput = document.getElementById('_method');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = "hidden";
                        methodInput.name = "_method";
                        methodInput.value = "PUT";
                        methodInput.id = "_method";
                        leaveForm.appendChild(methodInput);
                    } else {
                        methodInput.value = "PUT";
                    }

                    modal.classList.remove('hidden');
                });
            });
        }

        // =================== INITIALIZATION ===================
        function reinitializeEventListeners() {
            initializeEditButtons();
            initializeDeleteButtons();
            initializeStatusButtons();
            initializeImageModals();
            initializeBulkSelection();

            // Reattach pagination event listeners
            document.querySelectorAll('#paginationContainer a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page');
                    loadFilteredData(page);
                });
            });
        }

        // Initial initialization
        document.addEventListener('DOMContentLoaded', function() {
            initializeEditButtons();
            initializeDeleteButtons();
            initializeStatusButtons();
            initializeImageModals();
            initializeBulkSelection();
            updateStatusCounts({
                pending: {{ $pendingCount }},
                approved: {{ $approvedCount }},
                rejected: {{ $rejectedCount }}
            });

            // Reset form for new application
            openBtn.addEventListener('click', () => {
                leaveForm.reset();
                document.getElementById('leave_id').value = '';
                document.querySelector('#leaveModal h2').innerText = "New Leave Application";
                submitBtn.innerText = "Submit";
                leaveForm.action = "{{ route('leave.leaveApplicationSubmit') }}";
                document.getElementById('filePreview').src = "";
                document.getElementById('filePreviewWrapper').classList.add('hidden');
                document.getElementById('removeHardCopy').value = "0";

                // Clear soldier selection
                soldierCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateSelectedSoldiersDisplay();

                // Remove method override for new application
                const methodInput = document.getElementById('_method');
                if (methodInput) {
                    methodInput.remove();
                }
            });
        });
    </script>
@endpush
