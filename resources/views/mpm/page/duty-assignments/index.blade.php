@extends('mpm.layouts.app')

@section('title', 'Duty Assignment Dashboard')

@section('content')
    <div class="container mx-auto p-6">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="space-y-6">

            <!-- Page Header -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Duty Assignment Dashboard</h1>
                        <p class="mt-1 text-sm text-gray-600">Manage and monitor soldier duty assignments</p>
                    </div>
                    <button onclick="location.reload()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Assignments</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1" id="totalAssignments">-</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Unique Soldiers</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1" id="uniqueSoldiers">-</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Duties</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1" id="uniqueDuties">-</p>
                        </div>
                        <div class="p-3 bg-indigo-100 rounded-full">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Avg Duties/Soldier</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1" id="avgDuties">-</p>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Selector & Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-64">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Date to View Details</label>
                        <input type="date" id="selectedDate"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <button onclick="loadDutyDetails()"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Load Details
                    </button>
                    <button onclick="showAssignModal()"
                        class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Assign New Duties
                    </button>
                    <button onclick="exportToPDF()"
                        class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export PDF
                    </button>
                </div>
            </div>

            <!-- Detailed Duty Assignments Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Duty Assignments Details</h2>
                    <p class="text-sm text-gray-600 mt-1">Complete overview of all duty assignments for the selected date
                    </p>
                </div>

                <div id="dutyDetailsContainer">
                    <!-- Loading State -->
                    <div class="p-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-4 text-lg font-medium">Select a date and click "Load Details"</p>
                    </div>
                </div>
            </div>

        </div>

        @include('mpm.page.duty-assignments.partials.assignModal')
        @include('mpm.page.duty-assignments.partials.quickActionModal')

        <!-- Loading Overlay -->
        <div id="loadingOverlay"
            class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 flex flex-col items-center">
                <svg class="animate-spin h-10 w-10 text-blue-600 mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <p class="text-gray-700 font-medium">Processing...</p>
            </div>
        </div>

        <!-- Toast Container -->
        <div id="toastContainer" class="fixed z-50 bottom-4 right-4 z-50 space-y-4"></div>
        <div id="duty-container-id" data-base-url="{{ url('/') }}"
            data-show-route="{{ route('duty.show', ['duty' => ':id']) }}"></div>
    </div>
@endsection

@push('scripts')
    <script>
        var GlobalDate = null;
        const container = document.getElementById('duty-container-id');
        const showRouteTemplate = container?.dataset.showRoute || '/duty/:id';

        function getDutyShowUrl(dutyId) {
            return showRouteTemplate.replace(':id', dutyId);
        }
        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('selectedDate').value = today;
            document.querySelectorAll('input[name="date"]').forEach(input => input.value = today);
            loadDutyDetails();
        });

        // Toast Notification
        function showToast(message, type = 'success') {
            const colors = {
                success: 'bg-green-600',
                error: 'bg-red-600',
                warning: 'bg-yellow-600',
                info: 'bg-blue-600'
            };

            const toast = document.createElement('div');
            toast.className =
                `${colors[type]} z-100 text-white px-6 py-4 rounded-lg shadow-lg flex items-center justify-between min-w-80 transform transition-all duration-300`;
            toast.innerHTML = `
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;

            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }

        // Loading Functions
        function showLoading() {
            document.getElementById('loadingOverlay').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.add('hidden');
        }

        // Modal Functions
        function showAssignModal() {
            document.getElementById('assignModal').classList.remove('hidden');
        }

        function closeAssignModal() {
            document.getElementById('assignModal').classList.add('hidden');
        }

        function showQuickActionModal() {
            document.getElementById('quickActionModal').classList.remove('hidden');
        }

        function closeQuickActionModal() {
            document.getElementById('quickActionModal').classList.add('hidden');
        }

        function toggleAssignType() {
            const type = document.querySelector('input[name="assignType"]:checked').value;
            const singleForm = document.getElementById('assignSingleForm');
            const rangeForm = document.getElementById('assignRangeForm');

            if (type === 'single') {
                singleForm.classList.remove('hidden');
                rangeForm.classList.add('hidden');
            } else {
                singleForm.classList.add('hidden');
                rangeForm.classList.remove('hidden');
            }
        }

        // Handle Assign Single Date
        async function handleAssignSingle(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);

            showLoading();
            try {
                const response = await fetch('/duty-assignments/assign', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                hideLoading();

                if (result.success) {
                    showToast('Duties assigned successfully!', 'success');
                    closeAssignModal();
                    event.target.reset();
                    loadDutyDetails();
                } else {
                    showToast(result.message || 'Failed to assign duties', 'error');
                }
            } catch (error) {
                hideLoading();
                showToast('Error: ' + error.message, 'error');
            }
        }

        // Handle Assign Date Range
        async function handleAssignRange(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);

            showLoading();
            try {
                const response = await fetch('/duty-assignments/assign-range', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                hideLoading();

                if (result.success) {
                    showToast(`Assigned ${result.assigned_dates.length} dates. Success rate: ${result.success_rate}`,
                        'success');
                    if (result.errors && result.errors.length > 0) {
                        showToast(`${result.errors.length} dates had errors`, 'warning');
                    }
                    closeAssignModal();
                    event.target.reset();
                    loadDutyDetails();
                } else {
                    showToast(result.message || 'Failed to assign date range', 'error');
                }
            } catch (error) {
                hideLoading();
                showToast('Error: ' + error.message, 'error');
            }
        }

        // Helper function to format time
        function formatTime(dateTimeString) {
            if (!dateTimeString) return 'N/A';
            if (dateTimeString.includes('T')) {
                return dateTimeString.split('T')[1].substring(0, 5);
            }
            return dateTimeString;
        }

        // Helper function to check if duty is overnight
        function checkIfOvernight(startTime, endTime) {
            if (!startTime || !endTime) return false;
            try {
                const start = new Date(startTime);
                const end = new Date(endTime);
                return start.toDateString() !== end.toDateString();
            } catch (e) {
                return false;
            }
        }

        // Process the API response data - UPDATED TO PROPERLY HANDLE FIXED DUTIES
        function processDutyData(data) {
            const dutiesMap = {};

            // Process roster duties first
            if (data.roster_duties && Array.isArray(data.roster_duties)) {
                data.roster_duties.forEach(rosterDuty => {
                    try {
                        const dutyId = rosterDuty.duty_id;

                        // Initialize duty if not exists
                        if (!dutiesMap[dutyId]) {
                            dutiesMap[dutyId] = {
                                duty_id: dutyId,
                                duty_name: rosterDuty.duty_name || 'Unknown Duty',
                                start_time: formatTime(rosterDuty.start_time),
                                end_time: formatTime(rosterDuty.end_time),
                                duration_days: rosterDuty.duration_days || 1,
                                required_manpower: rosterDuty.required_manpower || 0,
                                is_overnight: checkIfOvernight(rosterDuty.start_time, rosterDuty.end_time),
                                roster_soldiers: [],
                                fixed_soldiers: [],
                                has_roster: false,
                                has_fixed: false
                            };
                        }

                        // Mark as having roster assignments
                        dutiesMap[dutyId].has_roster = true;

                        // Handle assigned_soldiers in various formats
                        let assignedSoldiers = [];
                        if (Array.isArray(rosterDuty.assigned_soldiers)) {
                            assignedSoldiers = rosterDuty.assigned_soldiers;
                        } else if (rosterDuty.assigned_soldiers && typeof rosterDuty.assigned_soldiers ===
                            'object') {
                            assignedSoldiers = Object.values(rosterDuty.assigned_soldiers);
                        }

                        // Add roster soldiers
                        assignedSoldiers.forEach(soldier => {
                            dutiesMap[dutyId].roster_soldiers.push({
                                id: soldier.soldier_id || soldier.id || 0,
                                name: soldier.full_name || soldier.name || 'Unknown Soldier',
                                army_no: soldier.army_no || 'N/A',
                                rank: soldier.rank || 'N/A',
                                company: soldier.company || 'N/A',
                                remarks: soldier.remarks || '',
                                assignment_type: 'roster'
                            });
                        });

                    } catch (error) {
                        console.error('Error processing roster duty:', error, rosterDuty);
                    }
                });
            }

            // Process fixed duties and merge with existing duties
            if (data.fixed_duties) {
                let fixedDutiesArray = [];

                // Handle both Collection and Array formats
                if (Array.isArray(data.fixed_duties)) {
                    fixedDutiesArray = data.fixed_duties;
                } else if (data.fixed_duties.constructor && data.fixed_duties.constructor.name === 'Collection') {
                    fixedDutiesArray = Object.values(data.fixed_duties);
                } else if (typeof data.fixed_duties === 'object') {
                    fixedDutiesArray = Object.values(data.fixed_duties);
                }

                console.log('Processing fixed duties:', fixedDutiesArray);

                fixedDutiesArray.forEach(fixedDuty => {
                    try {
                        const dutyId = fixedDuty.duty_id;

                        // Initialize duty if not exists (duty only has fixed assignments)
                        if (!dutiesMap[dutyId]) {
                            dutiesMap[dutyId] = {
                                duty_id: dutyId,
                                duty_name: fixedDuty.duty_name || 'Unknown Fixed Duty',
                                start_time: formatTime(fixedDuty.start_time),
                                end_time: formatTime(fixedDuty.end_time),
                                duration_days: 1,
                                required_manpower: 0,
                                is_overnight: checkIfOvernight(fixedDuty.start_time, fixedDuty.end_time),
                                roster_soldiers: [],
                                fixed_soldiers: [],
                                has_roster: false,
                                has_fixed: false
                            };
                        }

                        // Mark as having fixed assignments
                        dutiesMap[dutyId].has_fixed = true;

                        // Add fixed soldier
                        dutiesMap[dutyId].fixed_soldiers.push({
                            id: fixedDuty.soldier_id,
                            name: fixedDuty.full_name || 'N/A',
                            army_no: fixedDuty.army_no || 'N/A',
                            rank: fixedDuty.rank || 'N/A',
                            company: fixedDuty.company || 'N/A',
                            remarks: 'Fixed Assignment',
                            assignment_type: 'fixed'
                        });

                    } catch (error) {
                        console.error('Error processing fixed duty:', error, fixedDuty);
                    }
                });
            }

            // Convert map to array and calculate totals
            const duties = Object.values(dutiesMap).map(duty => {
                const totalRoster = duty.roster_soldiers.length;
                const totalFixed = duty.fixed_soldiers.length;
                const totalAssigned = totalRoster + totalFixed;

                return {
                    ...duty,
                    roster_count: totalRoster,
                    fixed_count: totalFixed,
                    assigned_count: totalAssigned,
                    fulfillment_rate: duty.required_manpower > 0 ?
                        Math.round((totalAssigned / duty.required_manpower) * 100) : 100
                };
            });

            // Sort duties by name
            duties.sort((a, b) => (a.duty_name || '').localeCompare(b.duty_name || ''));

            console.log('Final processed duties (merged):', duties);
            return duties;
        }

        // Load Duty Details
        async function loadDutyDetails() {
            const date = document.getElementById('selectedDate').value;
            if (!date) {
                showToast('Please select a date', 'warning');
                return;
            }

            showLoading();
            try {
                const [statsResponse, detailsResponse] = await Promise.all([
                    fetch(`/duty-assignments/statistics?date=${date}`),
                    fetch(`/duty-assignments/details?date=${date}`)
                ]);

                const statsResult = await statsResponse.json();
                const detailsResult = await detailsResponse.json();

                console.log('Stats Result:', statsResult);
                console.log('Details Result:', detailsResult);

                // Update statistics
                if (statsResult.success && statsResult.data) {
                    document.getElementById('totalAssignments').textContent = statsResult.data.total_assignments || 0;
                    document.getElementById('uniqueSoldiers').textContent = statsResult.data.unique_soldiers || 0;
                    document.getElementById('uniqueDuties').textContent = statsResult.data.unique_duties || 0;
                    document.getElementById('avgDuties').textContent = statsResult.data.average_duties_per_soldier || 0;
                }

                // Process and render details
                let duties = [];
                if (detailsResult.success && detailsResult.data) {
                    GlobalDate = detailsResult.data.date;
                    console.log('Raw duty data:', detailsResult.data);
                    duties = processDutyData(detailsResult.data);
                    console.log('Processed duties:', duties);
                } else {
                    console.error('Details API error:', detailsResult);
                }

                renderDutyDetailsTable(duties);
                hideLoading();
            } catch (error) {
                hideLoading();
                console.error('Error loading duty details:', error);
                console.error('Error stack:', error.stack);
                showToast('Error loading duty details: ' + error.message, 'error');
            }
        }

        // Unified table rendering - Single table for both roster and fixed duties
        // Unified table rendering - Single row per duty with both roster and fixed soldiers
        function renderDutyDetailsTable(duties) {
            console.log('Rendering duties:', duties);
            const container = document.getElementById('dutyDetailsContainer');

            if (!duties || duties.length === 0) {
                container.innerHTML = `
            <div class="p-12 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="mt-4 text-lg font-medium">No duty assignments found for this date</p>
            </div>
        `;
                return;
            }

            // Calculate totals
            const totalRosterAssignments = duties.reduce((sum, d) => sum + d.roster_count, 0);
            const totalFixedAssignments = duties.reduce((sum, d) => sum + d.fixed_count, 0);
            const totalAssignments = totalRosterAssignments + totalFixedAssignments;

            let html = `
        <div class="overflow-x-auto">
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700">Total Duties: ${duties.length}</span>
                    <span class="text-sm text-gray-600">|</span>
                    <span class="text-sm text-gray-600">Total Assignments: ${totalAssignments}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="5"/>
                        </svg>
                        Roster: ${totalRosterAssignments}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="5"/>
                        </svg>
                        Fixed: ${totalFixedAssignments}
                    </span>
                </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duty Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Soldiers</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fulfillment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
    `;

            // Generate all duty rows
            duties.forEach(duty => {
                html += generateDutyRow(duty);
            });

            html += `
                </tbody>
            </table>
        </div>
    `;

            container.innerHTML = html;
        }

        // Helper function to generate duty row with both roster and fixed soldiers
        function generateDutyRow(duty) {
            // console.log(GlobalDate);
            const fulfillmentPercent = duty.fulfillment_rate || 0;
            const isFulfilled = fulfillmentPercent >= 100;
            const totalAssigned = duty.assigned_count || 0;
            const requiredManpower = duty.required_manpower || 0;

            // Combine both roster and fixed soldiers
            const allSoldiers = [
                ...duty.roster_soldiers.map(s => ({
                    ...s,
                    type: 'roster'
                })),
                ...duty.fixed_soldiers.map(s => ({
                    ...s,
                    type: 'fixed'
                }))
            ];

            let row = `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900"><a href="${getDutyShowUrl(duty.duty_id)}" target="_blank" class="underline">${duty.duty_name || 'N/A'}</a></div>
                <div class="text-sm text-gray-500 flex items-center gap-2">

                    ${duty.has_roster ? `
                                                                                                                                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                                                                                                                                                                Roster
                                                                                                                                                                            </span>
                                                                                                                                                                        ` : ''}
                    ${duty.has_fixed ? `
                                                                                                                                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">
                                                                                                                                                                                Fixed
                                                                                                                                                                            </span>
                                                                                                                                                                        ` : ''}
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm text-gray-900">${duty.start_time || 'N/A'} - ${duty.end_time || 'N/A'}</div>
                ${duty.is_overnight ? '<span class="text-xs text-orange-600 font-medium">Overnight</span>' : ''}
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    ${duty.duration_days || 1} day${(duty.duration_days || 1) > 1 ? 's' : ''}
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm space-y-2">
    `;

            if (allSoldiers.length > 0) {
                allSoldiers.forEach(soldier => {
                    const safeSoldierName = (soldier.name || 'N/A').replace(/'/g, "\\'").replace(/"/g, '\\"');
                    const safeDutyName = (duty.duty_name || 'N/A').replace(/'/g, "\\'").replace(/"/g, '\\"');
                    const isFixed = soldier.type === 'fixed';

                    row += `
                <div class="flex items-center justify-between group">
                    <div class="flex items-center flex-wrap gap-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${isFixed ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'}">
                            ${soldier.rank || 'N/A'}
                        </span>
                        <span class="text-gray-900 font-medium">${soldier.name || 'N/A'}</span>
                        <span class="text-gray-500 text-xs">(${soldier.army_no || 'N/A'})</span>
                        ${soldier.company ? `<span class="text-gray-400 text-xs">• ${soldier.company}</span>` : ''}
                        ${isFixed ? `
                                                                                                                                                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700">
                                                                                                                                                                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                                                                                                                                                    </svg>
                                                                                                                                                                                    Fixed
                                                                                                                                                                                </span>
                                                                                                                                                                            ` : ''}
                        ${soldier.remarks && !isFixed ? `<span class="text-xs text-blue-600 italic">• ${soldier.remarks}</span>` : ''}
                    </div>
                    <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        ${!isFixed ? `
                                                                                                                                                                                <button
                                                                                                                                                                                    data-action="reassign"
                                                                                                                                                                                    data-duty-id="${duty.duty_id}"
                                                                                                                                                                                    data-soldier-id="${soldier.id}"
                                                                                                                                                                                    data-soldier-name="${safeSoldierName}"
                                                                                                                                                                                    data-duty-name="${safeDutyName}"
                                                                                                                                                                                    class="text-yellow-600 hover:text-yellow-800 p-1 rounded hover:bg-yellow-50"
                                                                                                                                                                                    title="Reassign Soldier">
                                                                                                                                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                                                                                                                                                    </svg>
                                                                                                                                                                                </button>
                                                                                                                                                                                <button
                                                                                                                                                                                    data-action="cancel"
                                                                                                                                                                                    data-duty-id="${duty.duty_id}"
                                                                                                                                                                                    data-soldier-id="${soldier.id}"
                                                                                                                                                                                    data-soldier-name="${safeSoldierName}"
                                                                                                                                                                                    data-duty-name="${safeDutyName}"
                                                                                                                                                                                    class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50"
                                                                                                                                                                                    title="Remove Soldier">
                                                                                                                                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                                                                                                                                    </svg>
                                                                                                                                                                                </button>
                                                                                                                                                                            ` : `
                                                                                                                                                                                <span class="text-xs text-purple-600 italic px-2 py-1">Permanent</span>
                                                                                                                                                                            `}
                    </div>
                </div>
            `;
                });
            } else {
                row += `
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="text-red-600 font-medium">No soldiers assigned</span>
            </div>
        `;
            }

            row += `
                </div>
                ${duty.roster_count > 0 || duty.fixed_count > 0 ? `
                                                                                                                                                                        <div class="text-xs text-gray-500 mt-2 flex gap-3">
                                                                                                                                                                            ${duty.roster_count > 0 ? `<span class="text-green-600">Roster: ${duty.roster_count}</span>` : ''}
                                                                                                                                                                            ${duty.fixed_count > 0 ? `<span class="text-purple-600">Fixed: ${duty.fixed_count}</span>` : ''}
                                                                                                                                                                        </div>
                                                                                                                                                                    ` : ''}
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center space-x-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2 min-w-[60px]">
                        <div class="${isFulfilled ? 'bg-green-600' : 'bg-red-600'} h-2 rounded-full transition-all" style="width: ${Math.min(fulfillmentPercent, 100)}%"></div>
                    </div>
                    <span class="text-sm font-medium ${isFulfilled ? 'text-green-600' : 'text-red-600'} min-w-[45px]">${fulfillmentPercent}%</span>
                </div>
                <div class="text-xs text-gray-500 mt-1">${totalAssigned} / ${requiredManpower}</div>
            </td>
            <td class="px-6 py-4 text-sm whitespace-nowrap">
                <button onclick="viewDutyDetails(${duty.duty_id}, '${(duty.start_time || 'N/A').replace(/'/g, "\\'").replace(/"/g, '\\"')}')"
                    class="text-blue-600 hover:text-blue-900 font-medium mr-3">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View
                </button>
                ${duty.has_roster ? `
                                                                                                                                                                        <button onclick="showAddSoldierModal(${duty.duty_id}, '${(duty.duty_name || 'N/A').replace(/'/g, "\\'").replace(/"/g, '\\"')}')"
                                                                                                                                                                            class="text-green-600 hover:text-green-900 font-medium">
                                                                                                                                                                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                                                                                                                                            </svg>
                                                                                                                                                                            Add
                                                                                                                                                                        </button>
                                                                                                                                                                    ` : `
                                                                                                                                                                        <span class="text-xs text-gray-400">Fixed Only</span>
                                                                                                                                                                    `}
            </td>
        </tr>
    `;

            return row;
        }

        // Quick Action: Cancel Assignment
        async function quickCancelAssignment(dutyId, soldierId, soldierName, dutyName) {
            const date = document.getElementById('selectedDate').value;
            const safeSoldierName = soldierName.replace(/'/g, "\\'").replace(/"/g, '\\"');
            const safeDutyName = dutyName.replace(/'/g, "\\'").replace(/"/g, '\\"');

            if (!confirm(`Are you sure you want to remove ${safeSoldierName} from "${safeDutyName}"?`)) {
                return;
            }

            showLoading();
            try {
                const response = await fetch('/duty-assignments/cancel', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        soldier_id: soldierId,
                        duty_id: dutyId,
                        date: date
                    })
                });

                const result = await response.json();
                hideLoading();

                if (result.success) {
                    showToast(`${safeSoldierName} removed from ${safeDutyName}`, 'success');
                    loadDutyDetails();
                } else {
                    showToast(result.message || 'Failed to remove soldier', 'error');
                }
            } catch (error) {
                hideLoading();
                showToast('Error: ' + error.message, 'error');
            }
        }

        // Enhanced Reassign Modal with searchable dropdown
        async function quickReassignSoldier(dutyId, soldierId, soldierName, dutyName) {
            const date = document.getElementById('selectedDate').value;

            showLoading();
            try {
                const response = await fetch(
                    `/duty-assignments/available-duties?date=${date}&soldier_id=${soldierId}&exclude_duty_id=${dutyId}`
                );
                const result = await response.json();
                hideLoading();

                let dutiesOptions = '';
                if (result.success && result.data && result.data.length > 0) {
                    result.data.forEach(duty => {
                        const capacityText = duty.required_manpower > 0 ?
                            ` (${duty.assigned_count}/${duty.required_manpower})` : '';
                        const statusText = duty.is_available ? 'Available' : 'Unavailable';
                        const availabilityInfo = duty.availability_reason ? ` - ${duty.availability_reason}` :
                            '';

                        dutiesOptions += `
                        <option value="${duty.id}" data-available="${duty.is_available}">
                            ${duty.name} ${capacityText} - ${statusText}${availabilityInfo}
                        </option>
                    `;
                    });
                } else {
                    dutiesOptions = '<option value="">No duties available</option>';
                }

                document.getElementById('quickActionTitle').textContent = `Reassign ${soldierName}`;
                document.getElementById('quickActionContent').innerHTML = `
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600">
                            Reassign <strong>${soldierName}</strong> from <strong>"${dutyName}"</strong> to another duty.
                        </p>
                        <form id="quickReassignForm" onsubmit="handleQuickReassign(event, ${soldierId}, ${dutyId})">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Select New Duty</label>
                                    <input type="text" id="dutySearch"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 mb-2"
                                        placeholder="Search duties...">
                                    <select name="to_duty_id" id="dutySelect" required size="6"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        ${dutiesOptions}
                                    </select>
                                    <div class="mt-2 flex items-center space-x-4 text-xs">
                                        <div class="flex items-center">
                                            <span class="w-3 h-3 bg-green-100 rounded-full mr-1"></span>
                                            <span class="text-gray-600">Available</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-3 h-3 bg-red-100 rounded-full mr-1"></span>
                                            <span class="text-gray-600">Unavailable</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                    <input type="date" name="date" value="${date}" required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="mt-6 flex space-x-3">
                                <button type="button" onclick="closeQuickActionModal()"
                                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                                    Reassign
                                </button>
                            </div>
                        </form>
                    </div>
                `;

                setupDutySearch();
                showQuickActionModal();
            } catch (error) {
                hideLoading();
                showToast('Error loading duties: ' + error.message, 'error');
            }
        }

        // Setup duty search functionality
        function setupDutySearch() {
            const searchInput = document.getElementById('dutySearch');
            const select = document.getElementById('dutySelect');
            const allOptions = Array.from(select.options);

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                allOptions.forEach(option => {
                    const text = option.textContent.toLowerCase();
                    option.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

            allOptions.forEach(option => {
                const isAvailable = option.dataset.available === 'true';
                option.style.backgroundColor = isAvailable ? '#f0fdf4' : '#fef2f2';
                option.style.color = isAvailable ? '#166534' : '#991b1b';
            });
        }

        // Handle Quick Reassign
        async function handleQuickReassign(event, soldierId, fromDutyId) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const data = {
                soldier_id: soldierId,
                from_duty_id: fromDutyId,
                to_duty_id: formData.get('to_duty_id'),
                date: formData.get('date')
            };

            showLoading();
            try {
                const response = await fetch('/duty-assignments/reassign', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                hideLoading();

                if (result.success) {
                    showToast('Soldier reassigned successfully!', 'success');
                    closeQuickActionModal();
                    loadDutyDetails();
                } else {
                    let message = result.message;
                    if (result.reasons && result.reasons.length > 0) {
                        message += ': ' + result.reasons.join(', ');
                    }
                    showToast(message, 'warning');
                }
            } catch (error) {
                hideLoading();
                showToast('Error: ' + error.message, 'error');
            }
        }

        // Add Soldier Modal with searchable dropdown
        async function showAddSoldierModal(dutyId, dutyName) {
            const date = document.getElementById('selectedDate').value;

            showLoading();
            try {
                const response = await fetch(`/duty-assignments/available-soldiers?date=${date}&duty_id=${dutyId}`);
                const result = await response.json();
                hideLoading();

                let soldiersOptions = '';
                if (result.success && result.data && result.data.length > 0) {
                    result.data.forEach(soldier => {
                        const statusText = soldier.is_available ? 'Available' : 'Unavailable';
                        const availabilityInfo = soldier.availability_reason ?
                            ` (${soldier.availability_reason})` : '';

                        soldiersOptions += `
                            <option value="${soldier.id}" data-available="${soldier.is_available}">
                                ${soldier.rank} ${soldier.name} (${soldier.army_no}) - ${statusText}${availabilityInfo}
                            </option>
                        `;
                    });
                } else {
                    soldiersOptions = '<option value="">No soldiers available</option>';
                }

                document.getElementById('quickActionTitle').textContent = `Add Soldier to ${dutyName}`;
                document.getElementById('quickActionContent').innerHTML = `
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600">
                            Add a new soldier to <strong>"${dutyName}"</strong>.
                        </p>
                        <form id="addSoldierForm" onsubmit="handleAddSoldier(event, ${dutyId})">
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Soldier</label>
                                    <input type="text" id="soldierSearch"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 mb-2"
                                        placeholder="Search by name, rank, or army number...">
                                    <select name="soldier_id" id="soldierSelect" required size="8"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        ${soldiersOptions}
                                    </select>
                                    <div class="mt-2 flex items-center space-x-4 text-xs">
                                        <div class="flex items-center">
                                            <span class="w-3 h-3 bg-green-100 rounded-full mr-1"></span>
                                            <span class="text-gray-600">Available</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="w-3 h-3 bg-red-100 rounded-full mr-1"></span>
                                            <span class="text-gray-600">Unavailable</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                    <input type="date" name="date" value="${date}" required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="mt-6 flex space-x-3">
                                <button type="button" onclick="closeQuickActionModal()"
                                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Add Soldier
                                </button>
                            </div>
                        </form>
                    </div>
                `;

                setupSoldierSearch();
                showQuickActionModal();
            } catch (error) {
                hideLoading();
                showToast('Error loading soldiers: ' + error.message, 'error');
            }
        }

        // Setup soldier search functionality
        function setupSoldierSearch() {
            const searchInput = document.getElementById('soldierSearch');
            const select = document.getElementById('soldierSelect');
            const allOptions = Array.from(select.options);

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                allOptions.forEach(option => {
                    const text = option.textContent.toLowerCase();
                    option.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });

            allOptions.forEach(option => {
                const isAvailable = option.dataset.available === 'true';
                option.style.backgroundColor = isAvailable ? '#f0fdf4' : '#fef2f2';
                option.style.color = isAvailable ? '#166534' : '#991b1b';
            });
        }

        // Handle Add Soldier
        async function handleAddSoldier(event, dutyId) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const data = {
                soldier_id: formData.get('soldier_id'),
                duty_id: dutyId,
                date: formData.get('date')
            };

            showLoading();
            try {
                const response = await fetch('/duty-assignments/assign-soldier', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                hideLoading();

                if (result.success) {
                    showToast('Soldier added successfully!', 'success');
                    closeQuickActionModal();
                    loadDutyDetails();
                } else {
                    let message = result.message || 'Failed to add soldier';
                    if (result.reasons && result.reasons.length > 0) {
                        message += ': ' + result.reasons.join(', ');
                    }
                    showToast(message, 'error');
                }
            } catch (error) {
                hideLoading();
                showToast('Error: ' + error.message, 'error');
            }
        }

        // Export to CSV
        function exportToPDF() {
            const date = document.getElementById('selectedDate').value;
            if (!date) {
                showToast('Please select a date first', 'warning');
                return;
            }
            window.location.href = `/duty-assignments/export?date=${date}`;
            showToast('Exporting data...', 'info');
        }

        // Simple view details function
        /// <start> showing modal for duty details <start>///
        /// <start> showing modal for duty details <start>///
        /// <start> showing modal for duty details <start>///

        function viewDutyDetails(dutyId, dutyName) {
            // showToast(`Loading details for: ${dutyName}`, 'info');
            // Get the selected date from the date picker
            const date = document.getElementById('selectedDate').value;
            if (!date) {
                showToast('Please select a date first', 'warning');
                return;
            }
            showLoading();
            // Make AJAX request to load duty details
            fetch(`/duty-assignments/duty-details/${dutyId}?date=${date}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(result => {
                    hideLoading();

                    if (result.success) {
                        // Create and show modal with the duty details
                        showDutyDetailsModal(result.data, result.html);
                    } else {
                        showToast(result.message || 'Failed to load duty details', 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error loading duty details:', error);
                    showToast('Error loading duty details: ' + error.message, 'error');
                });
        }

        // Function to create and show duty details modal
        function showDutyDetailsModal(data, htmlContent) {
            // Remove existing modal if any
            const existingModal = document.getElementById('dutyDetailsModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Create modal container
            const modal = document.createElement('div');
            modal.id = 'dutyDetailsModal';
            modal.className = 'fixed inset-0 z-50 overflow-y-auto';
            modal.innerHTML = `
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeDutyDetailsModal()"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-7xl sm:w-full">
                <!-- Close button -->
                <div class="absolute top-4 right-4 z-10">
                    <button type="button" onclick="closeDutyDetailsModal()"
                        class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal content -->
                <div id="dutyDetailsContent" class="max-h-[90vh] overflow-y-auto">
                    ${htmlContent || '<div class="p-8 text-center text-gray-500">No content available</div>'}
                </div>

                <!-- Modal footer -->
                <div class="bg-gray-50 px-6 py-3 flex justify-between items-center border-t">
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeDutyDetailsModal()"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

            // Add modal to body
            document.body.appendChild(modal);

            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';

            // Add smooth entrance animation
            setTimeout(() => {
                const modalPanel = modal.querySelector('.sm\\:align-middle');
                if (modalPanel) {
                    modalPanel.style.opacity = '1';
                    modalPanel.style.transform = 'translateY(0)';
                }
            }, 50);
        }

        // Function to close duty details modal
        function closeDutyDetailsModal() {
            const modal = document.getElementById('dutyDetailsModal');
            if (modal) {
                // Add exit animation
                const modalPanel = modal.querySelector('.sm\\:align-middle');
                if (modalPanel) {
                    modalPanel.style.opacity = '0';
                    modalPanel.style.transform = 'translateY(-20px)';
                }

                setTimeout(() => {
                    modal.remove();
                    // Restore body scroll
                    document.body.style.overflow = '';
                }, 300);
            }
        }


        // Also update the event listener for view buttons to use the new function
        document.addEventListener('click', function(e) {
            // Existing code for cancel and reassign buttons...

            // Add handler for view details buttons if they exist in the table
            if (e.target.closest('[onclick*="viewDutyDetails"]')) {
                e.preventDefault();
                const button = e.target.closest('[onclick*="viewDutyDetails"]');
                const onclickAttr = button.getAttribute('onclick');
                const matches = onclickAttr.match(/viewDutyDetails\((\d+),\s*'([^']+)'\)/);
                if (matches && matches.length >= 3) {
                    const dutyId = parseInt(matches[1]);
                    const dutyName = matches[2].replace(/\\'/g, "'").replace(/\\"/g, '"');
                    viewDutyDetails(dutyId, dutyName);
                }
            }
        });

        /// <end> showing modal for duty details <end>///
        /// <end> showing modal for duty details <end>///
        /// <end> showing modal for duty details <end>///
        /// <end> showing modal for duty details <end>///
        // Event delegation for action buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('[data-action="cancel"]')) {
                const button = e.target.closest('[data-action="cancel"]');
                const dutyId = button.dataset.dutyId;
                const soldierId = button.dataset.soldierId;
                const soldierName = button.dataset.soldierName;
                const dutyName = button.dataset.dutyName;
                quickCancelAssignment(dutyId, soldierId, soldierName, dutyName);
            }

            if (e.target.closest('[data-action="reassign"]')) {
                const button = e.target.closest('[data-action="reassign"]');
                const dutyId = button.dataset.dutyId;
                const soldierId = button.dataset.soldierId;
                const soldierName = button.dataset.soldierName;
                const dutyName = button.dataset.dutyName;
                quickReassignSoldier(dutyId, soldierId, soldierName, dutyName);
            }
        });
    </script>
@endpush

@section('styles')
    <style>
        /* Custom scrollbar for select dropdowns */
        select[size] {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        select[size]::-webkit-scrollbar {
            width: 8px;
        }

        select[size]::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 4px;
        }

        select[size]::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }

        select[size]::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        /* Smooth transitions for hover effects */
        .group:hover .opacity-0 {
            opacity: 1;
        }

        /* Custom tooltip styles */
        [title] {
            position: relative;
        }
    </style>
@endsection
