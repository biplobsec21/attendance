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
                    <button data-tab="manpower"
                        class="tab-btn flex-1 py-3 px-4 text-sm font-medium rounded-md bg-blue-600 text-white">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Manpower Account Details
                    </button>
                    <button data-tab="leave"
                        class="tab-btn flex-1 py-3 px-4 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-calendar-times mr-2"></i>
                        Parade Report</button>
                    <button data-tab="profile"
                        class="hidden tab-btn flex-1 py-3 px-4 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-users mr-2"></i>
                        User Profile
                    </button>
                </nav>
            </div>

            <!-- Parade report -->
            <div id="manpower-tab" class="tab-content block">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Manpower Account Details Reports</h2>

                        <div
                            class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mb-4">
                            <div class="flex items-center space-x-2">
                                <label for="manpower-date" class="text-sm font-medium text-gray-700">Date:</label>
                                <input type="date" id="manpower-date" name="manpower_date" max="{{ date('Y-m-d') }}"
                                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="flex space-x-2">
                                <button id="download-manpower-report-excel"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                                    <i class="fas fa-file-excel mr-2"></i> Excel
                                </button>
                                <button id="download-manpower-report-pdf"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                                    <i class="fas fa-file-pdf mr-2"></i> PDF
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-gray-600">Select a date and click "Download Report" to generate report. Only past or
                            current dates can be selected.</p>
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

                // Manpower report download
                document.getElementById('download-manpower-report-excel').addEventListener('click', function() {
                    downloadManpowerReport('xl');
                });

                document.getElementById('download-manpower-report-pdf').addEventListener('click', function() {
                    downloadManpowerReport('pdf');
                });

                // Parade report download
                document.getElementById('download-parade-report-excel').addEventListener('click', function() {
                    downloadReport('excel');
                });

                document.getElementById('download-parade-report-pdf').addEventListener('click', function() {
                    downloadReport('pdf');
                });

                function downloadManpowerReport(type) {
                    const date = document.getElementById('manpower-date').value;

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

                    if (selectedDate > today) {
                        alert('Future dates cannot be selected');
                        return;
                    }

                    // Create a temporary form to submit the request
                    const form = document.createElement('form');
                    form.method = 'GET';
                    form.action = '{{ route('export.manpower', ['type' => 'xl']) }}'.replace('xl', type);

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

                    if (selectedDate > today) {
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
        </script>
    @endpush
