@extends('mpm.layouts.app')

@section('title', 'Absent List')

@section('content')

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
            <!-- Absent Filters Component -->
            <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Filter Absent Applications</h2>

                <form id="absentFiltersForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

                    <!-- Absent Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Absent Type</label>
                        <select name="absent_type_id" id="filterAbsentType"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none">
                            <option value="">All Absent Types</option>
                            @foreach ($absentType as $type)
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
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">Absent Lists</h1>

                <!-- Button to open modal -->
                <button id="openAbsentModal"
                    class="flex items-center gap-2 px-4 py-2 border border-orange-400 text-black font-bold rounded-lg
                           hover:bg-orange-50 hover:border-orange-500
                           transition-colors duration-200">
                    + Add new Absent
                </button>
            </div>

            <!-- Absent Table Container (will be updated via AJAX) -->
            <div id="absentTableContainer">
                @include('mpm.components.absent-table', ['absentDatas' => $absentDatas])
            </div>

            <!-- Pagination Container (will be updated via AJAX) -->
            <div id="paginationContainer" class="mt-6">
                @if ($absentDatas->hasPages())
                    <div class="flex justify-center items-center space-x-2 flex-wrap">
                        {{ $absentDatas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Absent Modal -->
    <div id="absentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b px-6 py-4 rounded-t-xl z-10">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">New Absent Application</h2>
                    <button id="closeAbsentModal"
                        class="text-gray-400 hover:text-gray-600 text-3xl font-light transition-colors">&times;</button>
                </div>
            </div>

            <!-- Modal Form -->
            <form id="absentForm" action="{{ route('absent.absentApplicationSubmit') }}" method="POST"
                enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                <input type="hidden" name="absent_id" id="absent_id">

                <!-- Enhanced Soldier Selection Section -->
                <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl p-6 border border-orange-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-orange-500 text-white rounded-lg p-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Select Soldiers</h3>
                                <p class="text-sm text-gray-600">Choose one or multiple soldiers for this application</p>
                            </div>
                        </div>
                        <div id="selectedCountBadge"
                            class="bg-orange-500 text-white px-4 py-2 rounded-full font-semibold text-sm shadow-lg">
                            0 Selected
                        </div>
                    </div>

                    <!-- Search Bar -->
                    <div class="relative mb-4">
                        <input type="text" id="soldierSearch" placeholder="Search by name, army number, or rank..."
                            class="w-full border-2 border-orange-300 rounded-lg px-4 py-3 pl-11 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 focus:outline-none transition-all">
                        <svg class="absolute left-3 top-3.5 h-5 w-5 text-orange-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Quick Action Buttons -->
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-orange-200">
                        <div class="flex gap-2">
                            <button type="button" id="selectAllBtn"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm bg-white border-2 border-orange-300 text-orange-700 rounded-lg hover:bg-orange-50 transition-colors font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Select All
                            </button>
                            <button type="button" id="clearSelectionBtn"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear All
                            </button>
                        </div>
                        <span id="visibleCount" class="text-sm text-gray-600 font-medium">
                            Showing <span id="visibleCountNumber">{{ count($profiles) }}</span> soldiers
                        </span>
                    </div>

                    <!-- Soldiers Grid with Checkboxes -->
                    <div id="soldiersCheckboxList" class="max-h-96 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                        @foreach ($profiles as $profile)
                            <label
                                class="soldier-checkbox-label flex items-center gap-3 p-4 bg-white rounded-lg border-2 border-gray-200 hover:border-orange-300 hover:shadow-md transition-all cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" name="soldier_ids[]" value="{{ $profile->id }}"
                                        class="soldier-checkbox w-5 h-5 rounded border-gray-300 text-orange-500 focus:ring-2 focus:ring-orange-400 focus:ring-offset-2 cursor-pointer">
                                    <div
                                        class="absolute inset-0 rounded border-2 border-orange-500 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="soldier-name text-base font-semibold text-gray-800 truncate">{{ $profile->full_name }}</span>
                                        <span
                                            class="selected-indicator hidden inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Selected
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 text-sm text-gray-600">
                                        <span class="soldier-army-no inline-flex items-center gap-1 font-mono">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                            </svg>
                                            {{ $profile->army_no }}
                                        </span>
                                        <span class="text-gray-400">•</span>
                                        <span class="soldier-rank inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                                </path>
                                            </svg>
                                            {{ $profile->rank->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="checkbox-indicator hidden">
                                    <div class="bg-orange-500 text-white rounded-full p-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <!-- No Results Message -->
                    <div id="noResultsMessage" class="hidden text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <p class="text-gray-600 font-medium">No soldiers found</p>
                        <p class="text-sm text-gray-500 mt-1">Try adjusting your search</p>
                    </div>

                    <!-- Selected Soldiers Summary (Collapsible) -->
                    <div id="selectedSummary" class="hidden mt-4 pt-4 border-t border-orange-200">
                        <button type="button" id="toggleSummary"
                            class="w-full flex items-center justify-between text-left p-3 bg-white rounded-lg hover:bg-orange-50 transition-colors">
                            <span class="font-semibold text-gray-800">View Selected Soldiers (<span
                                    id="summaryCount">0</span>)</span>
                            <svg id="summaryChevron" class="w-5 h-5 text-gray-600 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>
                        <div id="summaryList"
                            class="hidden mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-2">
                            <!-- Selected soldiers summary will appear here -->
                        </div>
                    </div>
                </div>

                <!-- Rest of the form fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Absent Type Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Absent Type *</label>
                        <select name="absent_type_id" id="absentTypeSelect"
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 focus:outline-none transition-all"
                            required>
                            <option value="">-- Select Absent Type --</option>
                            @foreach ($absentType as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Total Days (readonly) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Days</label>
                        <input type="number" id="totalDays" name="total_days"
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 bg-gray-50 cursor-not-allowed"
                            readonly>
                    </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date *</label>
                        <input type="date" id="fromDate" name="start_date"
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 focus:outline-none transition-all"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                        <input type="date" id="endDate" name="end_date"
                            class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 focus:outline-none transition-all"
                            required>
                    </div>
                </div>

                <!-- File Upload -->
                <div class="hidden">
                    <label for="application_file" class="block text-sm font-medium text-gray-700 mb-2">Application
                        Copy</label>
                    <input type="file" name="application_file" id="application_file"
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 focus:outline-none transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100"
                        accept="image/*">

                    <!-- Preview -->
                    <div id="filePreviewWrapper" class="mt-3 hidden">
                        <p class="text-xs text-gray-500 mb-2">Preview:</p>
                        <div class="relative inline-block">
                            <img id="filePreview" src=""
                                class="w-40 h-40 rounded-lg border-2 border-gray-200 object-cover shadow-md">
                            <button type="button" id="removeFileBtn"
                                class="absolute -top-2 -right-2 bg-red-500 text-white text-sm px-2.5 py-1.5 rounded-full shadow-lg hover:bg-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea name="reason" id="reasonTextarea" rows="4"
                        class="w-full border-2 border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-orange-400 focus:border-orange-400 focus:outline-none transition-all resize-none"
                        placeholder="Enter the reason for absence..."></textarea>
                </div>
                <input type="hidden" name="remove_hard_copy" id="removeHardCopy" value="0">

                <!-- Modal Actions -->
                <div class="flex justify-end gap-3 pt-6 border-t">
                    <button type="button" id="closeAbsentModal2"
                        class="px-6 py-2.5 border-2 border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="submitBtn"
                        class="px-6 py-2.5 bg-orange-500 text-white font-bold rounded-lg hover:bg-orange-600 transition-colors shadow-lg hover:shadow-xl flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Submit for <span id="submitCount">0</span> Soldier(s)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Status Modal -->
    <div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h2 class="text-lg font-bold text-gray-800">Change Status</h2>
                <button id="closeStatusModal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>

            <form id="statusForm" method="POST" action="{{ route('absent.changeStatusSubmit') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="absent_id" id="statusAbsentId">

                <div>
                    <label class="block text-sm font-medium text-gray-700">New Status</label>
                    <select name="absent_current_status" id="statusSelect"
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
                    <textarea name="status_reason" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-400 focus:border-orange-400 @error('status_reason') border-red-500 @enderror"></textarea>

                    @error('status_reason')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
            <p class="text-gray-600 mb-6">Are you sure you want to delete this absent application? This action cannot be
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

@endsection

@push('styles')
    <style>
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #fb923c;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #f97316;
        }

        /* Checkbox Animation */
        .soldier-checkbox:checked+.checkbox-indicator {
            display: block !important;
        }

        .soldier-checkbox-label:has(.soldier-checkbox:checked) {
            border-color: #fb923c !important;
            background: #fff7ed !important;
        }

        .soldier-checkbox-label:has(.soldier-checkbox:checked) .selected-indicator {
            display: inline-flex !important;
        }

        /* Smooth transitions */
        .soldier-checkbox-label {
            transition: all 0.2s ease;
        }

        /* Loading animation */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Pulse animation for selected count badge */
        @keyframes pulse-badge {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .badge-pulse {
            animation: pulse-badge 0.3s ease;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // =================== ENHANCED SOLDIER SELECTION FUNCTIONALITY ===================
        const soldierSearch = document.getElementById('soldierSearch');
        const soldiersCheckboxList = document.getElementById('soldiersCheckboxList');
        const selectedCountBadge = document.getElementById('selectedCountBadge');
        const submitCount = document.getElementById('submitCount');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const clearSelectionBtn = document.getElementById('clearSelectionBtn');
        const soldierCheckboxes = document.querySelectorAll('.soldier-checkbox');
        const noResultsMessage = document.getElementById('noResultsMessage');
        const visibleCountNumber = document.getElementById('visibleCountNumber');
        const selectedSummary = document.getElementById('selectedSummary');
        const summaryCount = document.getElementById('summaryCount');
        const summaryList = document.getElementById('summaryList');
        const toggleSummary = document.getElementById('toggleSummary');
        const summaryChevron = document.getElementById('summaryChevron');

        // Enhanced search functionality
        soldierSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const labels = soldiersCheckboxList.querySelectorAll('.soldier-checkbox-label');
            let visibleCount = 0;

            labels.forEach(label => {
                const soldierName = label.querySelector('.soldier-name').textContent.toLowerCase();
                const armyNo = label.querySelector('.soldier-army-no').textContent.toLowerCase();
                const rank = label.querySelector('.soldier-rank').textContent.toLowerCase();

                if (soldierName.includes(searchTerm) || armyNo.includes(searchTerm) || rank.includes(
                        searchTerm)) {
                    label.style.display = 'flex';
                    visibleCount++;
                } else {
                    label.style.display = 'none';
                }
            });

            // Update visible count
            visibleCountNumber.textContent = visibleCount;

            // Show/hide no results message
            if (visibleCount === 0) {
                noResultsMessage.classList.remove('hidden');
                soldiersCheckboxList.style.display = 'none';
            } else {
                noResultsMessage.classList.add('hidden');
                soldiersCheckboxList.style.display = 'block';
            }
        });

        // Update selection count and display with animation
        function updateSelection() {
            const selectedCheckboxes = document.querySelectorAll('.soldier-checkbox:checked');
            const count = selectedCheckboxes.length;

            // Animate badge
            selectedCountBadge.classList.add('badge-pulse');
            setTimeout(() => selectedCountBadge.classList.remove('badge-pulse'), 300);

            // Update counts
            selectedCountBadge.textContent = `${count} Selected`;
            submitCount.textContent = count;
            summaryCount.textContent = count;

            // Update summary
            if (count > 0) {
                selectedSummary.classList.remove('hidden');
                updateSummaryList(selectedCheckboxes);
            } else {
                selectedSummary.classList.add('hidden');
            }

            // Change badge color based on selection
            if (count > 0) {
                selectedCountBadge.classList.remove('bg-orange-500');
                selectedCountBadge.classList.add('bg-green-500');
            } else {
                selectedCountBadge.classList.remove('bg-green-500');
                selectedCountBadge.classList.add('bg-orange-500');
            }
        }

        // Update summary list
        function updateSummaryList(selectedCheckboxes) {
            summaryList.innerHTML = '';
            selectedCheckboxes.forEach(checkbox => {
                const label = checkbox.closest('.soldier-checkbox-label');
                const name = label.querySelector('.soldier-name').textContent;
                const armyNo = label.querySelector('.soldier-army-no').textContent.trim();
                const rank = label.querySelector('.soldier-rank').textContent.trim();

                const soldierItem = document.createElement('div');
                soldierItem.className = 'flex items-center gap-2 p-2 bg-orange-50 rounded border border-orange-200';
                soldierItem.innerHTML = `
                    <svg class="w-4 h-4 text-orange-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-800 truncate">${name}</div>
                        <div class="text-xs text-gray-600">${armyNo} • ${rank}</div>
                    </div>
                `;
                summaryList.appendChild(soldierItem);
            });
        }

        // Toggle summary list
        toggleSummary.addEventListener('click', function() {
            summaryList.classList.toggle('hidden');
            summaryChevron.classList.toggle('rotate-180');
        });

        // Select all visible soldiers
        selectAllBtn.addEventListener('click', function() {
            const visibleLabels = Array.from(soldiersCheckboxList.querySelectorAll('.soldier-checkbox-label'))
                .filter(label => label.style.display !== 'none');

            visibleLabels.forEach(label => {
                const checkbox = label.querySelector('.soldier-checkbox');
                checkbox.checked = true;
            });
            updateSelection();
        });

        // Clear all selection
        clearSelectionBtn.addEventListener('click', function() {
            soldierCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelection();
        });

        // Handle checkbox changes
        soldierCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelection);
        });

        // =================== AJAX FILTERING FUNCTIONALITY ===================
        const filtersForm = document.getElementById('absentFiltersForm');
        const applyFiltersBtn = document.getElementById('applyFilters');
        const resetFiltersBtn = document.getElementById('resetFilters');
        const filterLoading = document.getElementById('filterLoading');
        const absentTableContainer = document.getElementById('absentTableContainer');
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
        document.getElementById('filterAbsentType').addEventListener('change', loadFilteredData);
        document.getElementById('filterStatus').addEventListener('change', loadFilteredData);
        document.getElementById('filterFromDate').addEventListener('change', loadFilteredData);
        document.getElementById('filterToDate').addEventListener('change', loadFilteredData);

        function loadFilteredData(page = 1) {
            const formData = new FormData(filtersForm);
            formData.append('page', page);

            filterLoading.classList.remove('hidden');
            applyFiltersBtn.disabled = true;

            fetch('{{ route('absent.filter') }}', {
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
                    absentTableContainer.innerHTML = data.table;
                    paginationContainer.innerHTML = data.pagination;
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

        // Debounce function
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

        // Reinitialize event listeners after AJAX load
        function reinitializeEventListeners() {
            initializeEditButtons();
            initializeDeleteButtons();
            initializeStatusButtons();
            initializeImageModals();

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

        // =================== EDIT FUNCTIONALITY ===================
        function initializeEditButtons() {
            document.querySelectorAll('.editAbsentBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const absentId = this.dataset.id;
                    const soldierId = this.dataset.soldier;
                    const absentTypeId = this.dataset.absenttype;
                    const startDate = this.dataset.start;
                    const endDate = this.dataset.end;
                    const reason = this.dataset.reason ?? '';
                    const hardcopy = this.dataset.hardcopy;

                    // Set form values
                    document.getElementById('absent_id').value = absentId;

                    // For edit, select only the specific soldier
                    soldierCheckboxes.forEach(checkbox => {
                        checkbox.checked = (checkbox.value === soldierId);
                    });
                    updateSelection();

                    document.getElementById('absentTypeSelect').value = absentTypeId;
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

                    document.querySelector('#absentModal h2').innerText = "Edit Absent Application";
                    document.querySelector('#submitBtn').innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Update Application
                    `;
                    absentForm.action = "{{ route('absent.update', ':id') }}".replace(':id', absentId);

                    if (!document.getElementById('_method')) {
                        const methodInput = document.createElement('input');
                        methodInput.type = "hidden";
                        methodInput.name = "_method";
                        methodInput.value = "PUT";
                        methodInput.id = "_method";
                        absentForm.appendChild(methodInput);
                    } else {
                        document.getElementById('_method').value = "PUT";
                    }

                    modal.classList.remove('hidden');
                });
            });
        }

        function initializeDeleteButtons() {
            document.querySelectorAll('.deleteBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const absentId = this.dataset.id;
                    deleteForm.action = `/absent/${absentId}`;
                    deleteModal.classList.remove('hidden');
                });
            });
        }

        function initializeStatusButtons() {
            document.querySelectorAll('.openStatusModal').forEach(btn => {
                btn.addEventListener('click', function() {
                    const status = this.dataset.status;
                    const reason = this.dataset.reject_reason;

                    if (status === 'rejected' && reason.trim() !== '') {
                        this.classList.add('bg-red-200', 'text-red-800');
                        this.title = reason;
                    }

                    statusAbsentId.value = this.dataset.id;
                    statusSelect.value = status;
                    statusReason.value = reason;
                    statusReason.classList.remove('border-red-500');
                    reasonMark.classList.add('hidden');
                    statusModal.classList.remove('hidden');
                    statusSelect.dispatchEvent(new Event('change'));
                });
            });
        }

        function initializeImageModals() {
            document.querySelectorAll('.openImageModal').forEach(img => {
                img.addEventListener('click', function() {
                    modalImage.src = this.dataset.img;
                    imageModal.classList.remove('hidden');
                });
            });
        }

        // =================== ABSENT MODAL ===================
        const openBtn = document.getElementById('openAbsentModal');
        const closeBtn = document.getElementById('closeAbsentModal');
        const closeBtn2 = document.getElementById('closeAbsentModal2');
        const modal = document.getElementById('absentModal');

        openBtn.addEventListener('click', () => {
            // Reset form for new application
            absentForm.reset();
            document.getElementById('absent_id').value = '';
            document.querySelector('#absentModal h2').innerText = "New Absent Application";
            document.querySelector('#submitBtn').innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Submit for <span id="submitCount">0</span> Soldier(s)
            `;
            absentForm.action = "{{ route('absent.absentApplicationSubmit') }}";
            document.getElementById('filePreview').src = "";
            document.getElementById('filePreviewWrapper').classList.add('hidden');
            document.getElementById('removeHardCopy').value = "0";

            // Reset soldier selection
            soldierSearch.value = '';
            soldierCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelection();

            // Reset all soldiers to visible
            const labels = soldiersCheckboxList.querySelectorAll('.soldier-checkbox-label');
            labels.forEach(label => {
                label.style.display = 'flex';
            });
            visibleCountNumber.textContent = labels.length;
            noResultsMessage.classList.add('hidden');
            soldiersCheckboxList.style.display = 'block';

            // Remove method override for new application
            if (document.getElementById('_method')) {
                document.getElementById('_method').remove();
            }

            modal.classList.remove('hidden');
        });

        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
        closeBtn2.addEventListener('click', () => modal.classList.add('hidden'));

        // =================== AUTO CALCULATE DAYS ===================
        const fromDateEl = document.getElementById('fromDate');
        const endDateEl = document.getElementById('endDate');
        const totalDaysEl = document.getElementById('totalDays');
        const absentForm = document.getElementById('absentForm');
        const submitBtn = document.getElementById('submitBtn');

        function calculateDays() {
            const fromDate = new Date(fromDateEl.value);
            const endDate = new Date(endDateEl.value);

            if (!isNaN(fromDate) && !isNaN(endDate) && endDate >= fromDate) {
                const diffTime = endDate - fromDate;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                totalDaysEl.value = diffDays;
            } else {
                totalDaysEl.value = '';
            }
        }

        fromDateEl.addEventListener('change', calculateDays);
        endDateEl.addEventListener('change', calculateDays);

        // =================== FORM VALIDATION ===================
        absentForm.addEventListener('submit', function(e) {
            const selectedCheckboxes = document.querySelectorAll('.soldier-checkbox:checked');

            if (selectedCheckboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one soldier.');
                return false;
            }
            if (!absentForm.absent_type_id.value) {
                e.preventDefault();
                alert('Please select an Absent Type.');
                absentForm.absent_type_id.focus();
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
            const soldierCount = selectedCheckboxes.length;
            submitBtn.innerHTML = `
                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Submitting for ${soldierCount} Soldier${soldierCount !== 1 ? 's' : ''}...
            `;
        });

        // =================== STATUS MODAL ===================
        const statusModal = document.getElementById('statusModal');
        const closeStatusModal = document.getElementById('closeStatusModal');
        const closeStatusModal2 = document.getElementById('closeStatusModal2');
        const statusAbsentId = document.getElementById('statusAbsentId');
        const statusSelect = document.getElementById('statusSelect');
        const statusForm = document.getElementById('statusForm');
        const statusReason = statusForm.querySelector('textarea[name="status_reason"]');
        const reasonMark = document.getElementById('reasonRequiredMark');

        [closeStatusModal, closeStatusModal2].forEach(btn => {
            btn.addEventListener('click', () => statusModal.classList.add('hidden'));
        });

        statusSelect.addEventListener('change', () => {
            if (statusSelect.value === 'rejected') {
                reasonMark.classList.remove('hidden');
                statusReason.classList.add('border-red-500');
                statusReason.setAttribute('required', 'required');
            } else {
                reasonMark.classList.add('hidden');
                statusReason.classList.remove('border-red-500');
                statusReason.removeAttribute('required');
            }
        });

        statusReason.addEventListener('input', () => {
            if (statusSelect.value === 'rejected') {
                if (statusReason.value.trim() === '') {
                    statusReason.classList.add('border-red-500');
                } else {
                    statusReason.classList.remove('border-red-500');
                }
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

        cancelDelete.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                deleteModal.classList.add('hidden');
            }
        });

        // =================== INITIALIZATION ===================
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all event listeners
            initializeEditButtons();
            initializeDeleteButtons();
            initializeStatusButtons();
            initializeImageModals();

            // Initialize selection count
            updateSelection();

            // Initialize date calculation
            calculateDays();
        });
    </script>
@endpush
