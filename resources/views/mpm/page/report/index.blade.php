@extends('mpm.layouts.app')

@section('title', 'Report Panel')
@section('content')
    <div class="container mx-auto p-4">
        <div class="mt-3 mb-8">
            <h1 class="text-2xl font-bold text-gray-900">
                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                Report Dashboard
            </h1>
        </div>

        <div class="min-h-screen bg-gray-50">
            <!-- Tab Navigation -->
            <div class="mb-8">
                <nav class="flex space-x-1 bg-white rounded-lg p-1 shadow-sm">
                    <button data-tab="duty"
                        class="tab-btn flex-1 py-3 px-4 text-sm font-medium rounded-md bg-blue-600 text-white">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Duty Report
                    </button>
                    <button data-tab="leave"
                        class="tab-btn flex-1 py-3 px-4 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-calendar-times mr-2"></i>
                        Parade Report</button>
                    <button data-tab="profile"
                        class="tab-btn flex-1 py-3 px-4 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-users mr-2"></i>
                        User Profile
                    </button>
                </nav>
            </div>

            <!-- Parade report -->
            <div id="duty-tab" class="tab-content block">
                <div class="bg-white rounded-lg shadow-sm">
                    <!-- Controls -->
                    <div
                        class="p-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                        <h2 class="text-xl font-semibold text-gray-900">Duty Assignment Reports</h2>
                        <div
                            class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">

                            {{-- <!-- Single Date Filter -->
                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-700">Filter by Date:</label>
                                <input type="date" id="duty-date-filter"
                                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div> --}}

                            <!-- Date Range Filter -->
                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-700">From:</label>
                                <input type="date" id="duty-start-date"
                                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-700">To:</label>
                                <input type="date" id="duty-end-date"
                                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button id="apply-date-range"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                Apply
                            </button>
                            <!-- Reset Button -->
                            <button id="reset-filters"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-md hover:bg-gray-600">
                                Reset Filters
                            </button>
                            <div class="flex space-x-2">
                                <button
                                    onclick="window.location='{{ route('export.duties', ['type' => 'excel']) }}?start_date=' + document.getElementById('duty-start-date').value + '&end_date=' + document.getElementById('duty-end-date').value"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                                    <i class="fas fa-file-excel mr-2"></i> Excel
                                </button>
                                <button
                                    nclick="window.location='{{ route('export.duties', ['type' => 'csv']) }}?start_date=' + document.getElementById('duty-start-date').value + '&end_date=' + document.getElementById('duty-end-date').value"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                    <i class="fas fa-file-csv mr-2"></i> CSV
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        onclick="sortTable('duty', 0)">
                                        # ID <i class="fas fa-sort ml-1"></i>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                        onclick="sortTable('duty', 2)">
                                        Aasigned Date <i class="fas fa-sort ml-1"></i>
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Number of Person
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Number of Duties</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="duty-table-body" class="bg-white divide-y divide-gray-200">
                                <!-- Sample Row -->
                                @foreach ($dutyReport as $key => $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ ++$key }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $row->assigned_date }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $row->total_soldiers }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $row->total_duties }}</td>
                                        <td class="px-6 py-4">
                                            <button data-id="{{ $row->assigned_date }}" class="view-duty-btn"><span
                                                    class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">View</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- In the parade report tab (leave-tab) -->
            <!-- In the parade report tab (leave-tab) -->
            <!-- In the parade report tab (leave-tab) -->
            <!-- In the parade report tab (leave-tab) -->
            <div id="leave-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Parade Management Reports</h2>

                        <div
                            class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mb-4">
                            <div class="flex items-center space-x-2">
                                <label for="parade-date" class="text-sm font-medium text-gray-700">Date:</label>
                                <input type="date" id="parade-date" name="date" max="{{ date('Y-m-d') }}"
                                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="flex space-x-2">
                                <button id="download-parade-report-excel"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                                    <i class="fas fa-file-excel mr-2"></i> Excel
                                </button>
                                <button id="download-parade-report-pdf"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                                    <i class="fas fa-file-pdf mr-2"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-gray-600">Select a date and click "Download Report" to generate the parade report.
                            Only past or current dates can be selected.</p>
                    </div>
                </div>
            </div>

            <!-- Profile Tab -->
            <div id="profile-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">User Profile Reports</h2>
                        <div class="flex space-x-2">
                            <button onclick="downloadData('profile', 'excel')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                                <i class="fas fa-file-excel mr-2"></i> Excel
                            </button>
                            <button onclick="downloadData('profile', 'csv')"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
                                <i class="fas fa-file-csv mr-2"></i> CSV
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Full Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Join Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">EMP001</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">John Smith</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">john.smith@company.com</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">IT</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Software Engineer</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">2023-01-15</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <!-- Duty Modal -->
    <!-- Duty Details Modal -->
    <div id="dutyModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center overflow-auto z-[9999] hidden">
        <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-3xl my-20"> <!-- Changed my-12 to my-20 -->
            <div class="flex justify-between items-center p-4 border-b sticky top-0 bg-white z-10">
                <h3 class="text-lg font-semibold">Duty Details </h3>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <div class="p-4 content">
            </div>
        </div>

    @endsection

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const modal = document.getElementById('dutyModal');
                const closeBtn = document.getElementById('closeModal');
                const tbody = document.getElementById('dutyStatsBody');

                // Tabs
                const buttons = document.querySelectorAll(".tab-btn");
                const tabs = document.querySelectorAll(".tab-content");

                buttons.forEach(btn => {
                    btn.addEventListener("click", function() {
                        const tabId = this.dataset.tab;

                        tabs.forEach(tab => tab.classList.add("hidden"));
                        buttons.forEach(b => {
                            b.classList.remove("bg-blue-600", "text-white");
                            b.classList.add("text-gray-500", "hover:text-gray-700",
                                "hover:bg-gray-50");
                        });

                        document.getElementById(tabId + "-tab").classList.remove("hidden");
                        this.classList.add("bg-blue-600", "text-white");
                        this.classList.remove("text-gray-500", "hover:text-gray-700",
                            "hover:bg-gray-50");
                    });

                });

                // Date filter
                // Single-date filter
                // document.getElementById('duty-date-filter').addEventListener('change', filterDutyTable);

                // Date-range filter
                document.getElementById('apply-date-range').addEventListener('click', filterDutyTable);
                // Reset Filters
                document.getElementById('reset-filters').addEventListener('click', () => {
                    // Clear filter inputs
                    // document.getElementById('duty-date-filter').value = '';
                    document.getElementById('duty-start-date').value = '';
                    document.getElementById('duty-end-date').value = '';

                    // Show all rows
                    const tableBody = document.getElementById('duty-table-body');
                    tableBody.querySelectorAll('tr').forEach(row => {
                        row.style.display = '';
                    });
                });
                // Handle View buttons
                document.querySelectorAll('.view-duty-btn').forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const assignedDate = this.getAttribute('data-id');

                        try {
                            const res = await fetch(`/report/duties/${assignedDate}`);
                            const html = await res.text(); // Get the rendered HTML

                            const modalContent = document.querySelector('#dutyModal .content');
                            modalContent.innerHTML = html;

                            document.getElementById('dutyModal').classList.remove('hidden');
                        } catch (err) {
                            console.error(err);
                            alert('Failed to fetch duty details');
                        }
                    });
                });
                closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) modal.classList.add('hidden');
                });

                // Update the JavaScript for the parade report download
                // Update the JavaScript for the parade report download
                // Update the JavaScript for the parade report download
                document.getElementById('download-parade-report-excel').addEventListener('click', function() {
                    downloadReport('excel');
                });

                document.getElementById('download-parade-report-pdf').addEventListener('click', function() {
                    downloadReport('pdf');
                });

                function downloadReport(type) {
                    const date = document.getElementById('parade-date').value;

                    if (!date) {
                        alert('Please select a date');
                        return;
                    }

                    // Check if the date is in the future
                    const selectedDate = new Date(date);
                    const today = new Date();

                    // Normalize both dates to midnight for a day-to-day comparison
                    selectedDate.setHours(0, 0, 0, 0);
                    today.setHours(0, 0, 0, 0);

                    console.log(selectedDate, today);

                    if (selectedDate > today) {
                        // Note: It's better to use a modal or message box instead of alert()
                        alert('Future dates cannot be selected');
                        return;
                    }

                    // Create a temporary form to submit the request
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.action = '{{ route('export.parade', ['type' => 'excel']) }}'.replace('excel', type);

                    // Add date
                    const dateInput = document.createElement('input');
                    dateInput.type = 'hidden';
                    dateInput.name = 'date';
                    dateInput.value = date;
                    form.appendChild(dateInput);

                    // Submit the form
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                }
            });

            let sortDirection = {};

            function sortTable(module, columnIndex) {
                const tableBody = document.getElementById(module + '-table-body');
                const rows = Array.from(tableBody.querySelectorAll('tr'));
                const key = `${module}-${columnIndex}`;

                sortDirection[key] = sortDirection[key] === 'asc' ? 'desc' : 'asc';

                rows.sort((a, b) => {
                    const aValue = a.cells[columnIndex].textContent.trim();
                    const bValue = b.cells[columnIndex].textContent.trim();

                    let comparison = columnIndex === 2 ?
                        new Date(aValue) - new Date(bValue) :
                        aValue.localeCompare(bValue);

                    return sortDirection[key] === 'asc' ? comparison : -comparison;
                });

                tableBody.innerHTML = '';
                rows.forEach(r => tableBody.appendChild(r));
            }

            function downloadData(module, format) {
                const filename = `${module}_report_${new Date().toISOString().split('T')[0]}.${format}`;
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Downloading...';
                btn.disabled = true;

                setTimeout(() => {
                    alert(`Downloading ${filename}...`);
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }, 1500);
            }

            function filterDutyTable() {
                // const singleDate = document.getElementById('duty-date-filter').value;
                const singleDate = false;
                const startDate = document.getElementById('duty-start-date').value;
                const endDate = document.getElementById('duty-end-date').value;

                const tableBody = document.getElementById('duty-table-body');
                const rows = tableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    const rowDateStr = row.cells[1].textContent.trim(); // Assigned Date column
                    const rowDate = new Date(rowDateStr);

                    let show = true;

                    // Single-date filter has highest priority
                    if (singleDate) {
                        show = rowDateStr === singleDate;
                    } else {
                        // Apply range filter if single-date is not selected
                        if (startDate && rowDate < new Date(startDate)) show = false;
                        if (endDate && rowDate > new Date(endDate)) show = false;
                    }

                    row.style.display = show ? '' : 'none';
                });
            }
        </script>
    @endpush
