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
