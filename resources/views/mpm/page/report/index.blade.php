@extends('mpm.layouts.app')

@section('title', 'Report Panel')
@section('content')
    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Report Dashboard
                    </h1>
                    <p class="text-gray-600">Generate and download comprehensive reports for your organization</p>
                </div>
                <div class="hidden sm:flex items-center space-x-2 bg-blue-50 px-4 py-2 rounded-lg">
                    <i class="fas fa-chart-bar text-blue-600"></i>
                    <span class="text-sm font-medium text-blue-900">Analytics Hub</span>
                </div>
            </div>
        </div>

        <!-- Tab Navigation with Modern Pills -->
        <div class="mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2">
                <nav class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2">
                    <button data-tab="manpower"
                        class="tab-btn group relative py-4 px-4 text-sm font-medium rounded-xl transition-all duration-200 bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md">
                        <div class="flex flex-col items-center space-y-2">
                            <i class="fas fa-clipboard-list text-lg"></i>
                            <span class="text-xs">Manpower Details</span>
                        </div>
                    </button>
                    <button data-tab="pt"
                        class="tab-btn group relative py-4 px-4 text-sm font-medium rounded-xl transition-all duration-200 text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        <div class="flex flex-col items-center space-y-2">
                            <i class="fas fa-running text-lg"></i>
                            <span class="text-xs">PT </span>
                        </div>
                    </button>
                    <button data-tab="parade"
                        class="tab-btn group relative py-4 px-4 text-sm font-medium rounded-xl transition-all duration-200 text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        <div class="flex flex-col items-center space-y-2">
                            <i class="fas fa-calendar-times text-lg"></i>
                            <span class="text-xs">2nd Fall in</span>
                        </div>
                    </button>
                    <button data-tab="game"
                        class="tab-btn group relative py-4 px-4 text-sm font-medium rounded-xl transition-all duration-200 text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        <div class="flex flex-col items-center space-y-2">
                            <i class="fas fa-gamepad text-lg"></i>
                            <span class="text-xs">Games </span>
                        </div>
                    </button>

                    <button data-tab="roll-call"
                        class="tab-btn group relative py-4 px-4 text-sm font-medium rounded-xl transition-all duration-200 text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                        <div class="flex flex-col items-center space-y-2">
                            <i class="fas fa-clipboard-check text-lg"></i>
                            <span class="text-xs">Roll Call</span>
                        </div>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Report Content Cards -->
        <div class="relative">
            <!-- Manpower Report -->
            <div id="manpower-tab" class="tab-content block">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-gray-100">
                        <div class="flex items-center space-x-3 mb-1">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clipboard-list text-blue-600"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Manpower Account Details</h2>
                        </div>
                        <p class="text-sm text-gray-600 ml-13">Generate detailed personnel and account reports</p>
                    </div>

                    <div class="p-6">
                        <div class="bg-gray-50 rounded-xl p-6 mb-6">
                            <div
                                class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                <div class="flex-1">
                                    <label for="manpower-date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>Select Report Date
                                    </label>
                                    <input type="date" id="manpower-date" name="manpower_date"
                                        class="w-full lg:w-64 border-2 border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">

                                    {{-- <input type="date" id="manpower-date" name="manpower_date" max="{{ date('Y-m-d') }}"
                                        class="w-full lg:w-64 border-2 border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                --}}
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <button id="download-manpower-report-excel"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                        <i class="fas fa-file-excel mr-2"></i> Download Excel
                                    </button>
                                    <button id="download-manpower-report-pdf"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-semibold rounded-lg hover:from-red-600 hover:to-red-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                        <i class="fas fa-file-pdf mr-2"></i> Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-r-lg p-4">
                            <div class="flex">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                                <p class="text-sm text-blue-900">
                                    Select a date and choose your preferred format to generate the report. Only past or
                                    current dates are available for selection.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parade Report -->
            <div id="parade-tab" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 border-b border-gray-100">
                        <div class="flex items-center space-x-3 mb-1">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-times text-purple-600"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Parade Management</h2>
                        </div>
                        <p class="text-sm text-gray-600 ml-13">View and export parade attendance records</p>
                    </div>

                    <div class="p-6">
                        <div class="bg-gray-50 rounded-xl p-6 mb-6">
                            <div
                                class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                <div class="flex-1">
                                    <label for="parade-date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>Select Report Date
                                    </label>
                                    <input type="date" id="parade-date" name="date"
                                        class="w-full lg:w-64 border-2 border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">

                                    {{-- <input type="date" id="parade-date" name="date" max="{{ date('Y-m-d') }}"
                                        class="w-full lg:w-64 border-2 border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                                 --}}
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <button id="download-parade-report-excel"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                        <i class="fas fa-file-excel mr-2"></i> Download Excel
                                    </button>
                                    <button id="download-parade-report-pdf"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-semibold rounded-lg hover:from-red-600 hover:to-red-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                        <i class="fas fa-file-pdf mr-2"></i> Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 border-l-4 border-purple-500 rounded-r-lg p-4">
                            <div class="flex">
                                <i class="fas fa-info-circle text-purple-500 mt-0.5 mr-3"></i>
                                <p class="text-sm text-purple-900">
                                    Select a date and choose your preferred format to generate the parade report. Only past
                                    or current dates are available for selection.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Game Attendance Report -->
            <div id="game-tab" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-50 to-amber-50 p-6 border-b border-gray-100">
                        <div class="flex items-center space-x-3 mb-1">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-gamepad text-orange-600"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Game Attendance</h2>
                        </div>
                        <p class="text-sm text-gray-600 ml-13">Track and export game participation records</p>
                    </div>

                    <div class="p-6">
                        <div class="bg-gray-50 rounded-xl p-6 mb-6">
                            <div
                                class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                <div class="flex-1">
                                    <label for="game-date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>Select Report Date
                                    </label>
                                    <input type="date" id="game-date" name="game_date"
                                        class="w-full lg:w-64 border-2 border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">

                                    {{-- <input type="date" id="game-date" name="game_date" max="{{ date('Y-m-d') }}"
                                        class="w-full lg:w-64 border-2 border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"> --}}


                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <button id="download-game-report-excel"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                        <i class="fas fa-file-excel mr-2"></i> Download Excel
                                    </button>
                                    <button id="download-game-report-pdf"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-semibold rounded-lg hover:from-red-600 hover:to-red-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                        <i class="fas fa-file-pdf mr-2"></i> Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-orange-50 border-l-4 border-orange-500 rounded-r-lg p-4">
                            <div class="flex">
                                <i class="fas fa-info-circle text-orange-500 mt-0.5 mr-3"></i>
                                <p class="text-sm text-orange-900">
                                    Select a date and choose your preferred format to generate the game attendance report.
                                    Only past or current dates are available for selection.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PT Attendance Report -->
            <div id="pt-tab" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-6 border-b border-gray-100">
                        <div class="flex items-center space-x-3 mb-1">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-running text-emerald-600"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">PT Attendance</h2>
                        </div>
                        <p class="text-sm text-gray-600 ml-13">Monitor physical training participation</p>
                    </div>

                    <div class="p-6">
                        <div class="bg-gray-50 rounded-xl p-6 mb-6">
                            <div
                                class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                <div class="flex-1">
                                    <label for="pt-date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>Select Report Date
                                    </label>
                                    <input type="date" id="pt-date" name="pt_date"
                                        class="w-full lg:w-64 border-2 border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                                    {{-- <input type="date" id="pt-date" name="pt_date" max="{{ date('Y-m-d') }}"
                                        class="w-full lg:w-64 border-2 border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"> --}}

                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <button id="download-pt-report-excel"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                        <i class="fas fa-file-excel mr-2"></i> Download Excel
                                    </button>
                                    <button id="download-pt-report-pdf"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-semibold rounded-lg hover:from-red-600 hover:to-red-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                        <i class="fas fa-file-pdf mr-2"></i> Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg p-4">
                            <div class="flex">
                                <i class="fas fa-info-circle text-emerald-500 mt-0.5 mr-3"></i>
                                <p class="text-sm text-emerald-900">
                                    Select a date and choose your preferred format to generate the PT attendance report.
                                    Only past or current dates are available for selection.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roll Call Attendance Report -->
            <div id="roll-call-tab" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-50 to-sky-50 p-6 border-b border-gray-100">
                        <div class="flex items-center space-x-3 mb-1">
                            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clipboard-check text-cyan-600"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900">Roll Call Attendance</h2>
                        </div>
                        <p class="text-sm text-gray-600 ml-13">Generate roll call and attendance reports</p>
                    </div>

                    <div class="p-6">
                        <div class="bg-gray-50 rounded-xl p-6 mb-6">
                            <div
                                class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                                <div class="flex-1">
                                    <label for="roll-call-date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>Select Report Date
                                    </label>
                                    <input type="date" id="roll-call-date" name="roll_call_date" {{-- max="{{ date('Y-m-d') }}" --}}
                                        class="w-full lg:w-64 border-2 border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all">
                                    {{-- <input type="date" id="roll-call-date" name="roll_call_date"
                                        max="{{ date('Y-m-d') }}"
                                        class="w-full lg:w-64 border-2 border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all">
                                --}}

                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <button id="download-roll-call-report-excel"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                        <i class="fas fa-file-excel mr-2"></i> Download Excel
                                    </button>
                                    <button id="download-roll-call-report-pdf"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white text-sm font-semibold rounded-lg hover:from-red-600 hover:to-red-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                        <i class="fas fa-file-pdf mr-2"></i> Download PDF
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-cyan-50 border-l-4 border-cyan-500 rounded-r-lg p-4">
                            <div class="flex">
                                <i class="fas fa-info-circle text-cyan-500 mt-0.5 mr-3"></i>
                                <p class="text-sm text-cyan-900">
                                    Select a date and choose your preferred format to generate the roll call attendance
                                    report. Only past or current dates are available for selection.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Tab -->
            <div id="profile-tab" class="tab-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-50 to-violet-50 p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center space-x-3 mb-1">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-users text-indigo-600"></i>
                                    </div>
                                    <h2 class="text-xl font-bold text-gray-900">User Profiles</h2>
                                </div>
                                <p class="text-sm text-gray-600 ml-13">Export comprehensive user data</p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="downloadData('profile', 'excel')"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-semibold rounded-lg hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                    <i class="fas fa-file-excel mr-2"></i> Excel
                                </button>
                                <button onclick="downloadData('profile', 'csv')"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                    <i class="fas fa-file-csv mr-2"></i> CSV
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Employee ID</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Full Name</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Department</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Position</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Join Date</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">EMP001</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">John Smith</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">john.smith@company.com</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">IT</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Software Engineer</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">2023-01-15</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></span>
                                            Active
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Modal -->
    <div id="dutyModal"
        class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-start justify-center overflow-auto z-[9999] hidden transition-all duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-11/12 max-w-3xl my-20 transform transition-all duration-300">
            <div
                class="flex justify-between items-center p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white rounded-t-2xl">
                <h3 class="text-xl font-bold text-gray-900">Duty Details</h3>
                <button id="closeModal"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 content"></div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Enhanced tab switching with animations
            const buttons = document.querySelectorAll(".tab-btn");
            const tabs = document.querySelectorAll(".tab-content");

            buttons.forEach(btn => {
                btn.addEventListener("click", function() {
                    const tabId = this.dataset.tab;

                    // Hide all tabs with fade effect
                    tabs.forEach(tab => {
                        tab.classList.add("hidden");
                        tab.classList.remove("block");
                    });

                    // Reset all buttons
                    buttons.forEach(b => {
                        b.classList.remove("bg-gradient-to-br", "from-blue-500",
                            "to-blue-600", "text-white", "shadow-md");
                        b.classList.add("text-gray-600", "hover:text-gray-900",
                            "hover:bg-gray-50");
                    });

                    // Show selected tab
                    const selectedTab = document.getElementById(tabId + "-tab");
                    selectedTab.classList.remove("hidden");
                    selectedTab.classList.add("block");

                    // Activate button
                    this.classList.add("bg-gradient-to-br", "from-blue-500", "to-blue-600",
                        "text-white", "shadow-md");
                    this.classList.remove("text-gray-600", "hover:text-gray-900",
                        "hover:bg-gray-50");
                });
            });

            // Button loading states
            function setButtonLoading(button, isLoading) {
                if (isLoading) {
                    button.disabled = true;
                    button.classList.add('opacity-75', 'cursor-not-allowed');
                    const icon = button.querySelector('i');
                    const originalIcon = icon.className;
                    icon.className = 'fas fa-spinner fa-spin mr-2';
                    button.dataset.originalIcon = originalIcon;
                } else {
                    button.disabled = false;
                    button.classList.remove('opacity-75', 'cursor-not-allowed');
                    const icon = button.querySelector('i');
                    icon.className = button.dataset.originalIcon || icon.className.replace('fa-spinner fa-spin',
                        'fa-file-excel');
                }
            }

            // Enhanced notification system
            function showNotification(message, type = 'info') {
                const colors = {
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                    warning: 'bg-yellow-500',
                    info: 'bg-blue-500'
                };

                const icons = {
                    success: 'fa-check-circle',
                    error: 'fa-exclamation-circle',
                    warning: 'fa-exclamation-triangle',
                    info: 'fa-info-circle'
                };

                const notification = document.createElement('div');
                notification.className =
                    `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3 z-[10000] transform transition-all duration-300 translate-x-0`;
                notification.innerHTML = `
                    <i class="fas ${icons[type]} text-lg"></i>
                    <span class="font-medium">${message}</span>
                `;

                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 10);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.style.transform = 'translateX(400px)';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }

            // Manpower report download
            document.getElementById('download-manpower-report-excel').addEventListener('click', function() {
                setButtonLoading(this, true);
                downloadManpowerReport('xl');
                setTimeout(() => setButtonLoading(this, false), 2000);
            });

            document.getElementById('download-manpower-report-pdf').addEventListener('click', function() {
                setButtonLoading(this, true);
                downloadManpowerReport('pdf');
                setTimeout(() => setButtonLoading(this, false), 2000);
            });

            // Parade report download
            document.getElementById('download-parade-report-excel').addEventListener('click', function() {
                setButtonLoading(this, true);
                downloadAttendanceReport('parade', 'excel');
                setTimeout(() => setButtonLoading(this, false), 2000);
            });

            document.getElementById('download-parade-report-pdf').addEventListener('click', function() {
                setButtonLoading(this, true);
                downloadAttendanceReport('parade', 'pdf');
                setTimeout(() => setButtonLoading(this, false), 2000);
            });

            // Game report download
            document.getElementById('download-game-report-excel').addEventListener('click', function() {
                setButtonLoading(this, true);
                downloadAttendanceReport('game', 'excel');
                setTimeout(() => setButtonLoading(this, false), 2000);
            });

            document.getElementById('download-game-report-pdf').addEventListener('click', function() {
                setButtonLoading(this, true);
                downloadAttendanceReport('game', 'pdf');
                setTimeout(() => setButtonLoading(this, false), 2000);
            });

            // PT report download
            document.getElementById('download-pt-report-excel').addEventListener('click', function() {
                setButtonLoading(this, true);
                downloadAttendanceReport('pt', 'excel');
                setTimeout(() => setButtonLoading(this, false), 2000);
            });

            document.getElementById('download-pt-report-pdf').addEventListener('click', function() {
                setButtonLoading(this, true);
                downloadAttendanceReport('pt', 'pdf');
                setTimeout(() => setButtonLoading(this, false), 2000);
            });

            // Roll Call report download
            document.getElementById('download-roll-call-report-excel').addEventListener('click', function() {
                setButtonLoading(this, true);
                downloadAttendanceReport('roll-call', 'excel');
                setTimeout(() => setButtonLoading(this, false), 2000);
            });

            document.getElementById('download-roll-call-report-pdf').addEventListener('click', function() {
                setButtonLoading(this, true);
                downloadAttendanceReport('roll-call', 'pdf');
                setTimeout(() => setButtonLoading(this, false), 2000);
            });

            function downloadManpowerReport(type) {
                const date = document.getElementById('manpower-date').value;

                if (!date) {
                    showNotification('Please select a date', 'warning');
                    return;
                }

                // Check if the date is in the future
                const selectedDate = new Date(date);
                const today = new Date();

                // Normalize both dates to midnight for a day-to-day comparison
                selectedDate.setHours(0, 0, 0, 0);
                today.setHours(0, 0, 0, 0);

                // if (selectedDate > today) {
                //     showNotification('Future dates cannot be selected', 'error');
                //     return;
                // }

                // Show success notification
                showNotification('Generating report...', 'info');

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

            function downloadAttendanceReport(reportType, exportType) {
                // Get the date input based on the report type
                const dateInputId = reportType + '-date';
                const date = document.getElementById(dateInputId).value;

                if (!date) {
                    showNotification('Please select a date', 'warning');
                    return;
                }

                // Check if the date is in the future
                const selectedDate = new Date(date);
                const today = new Date();

                // Normalize both dates to midnight for a day-to-day comparison
                selectedDate.setHours(0, 0, 0, 0);
                today.setHours(0, 0, 0, 0);

                // if (selectedDate > today) {
                //     showNotification('Future dates cannot be selected', 'error');
                //     return;
                // }

                // Show success notification
                showNotification('Generating report...', 'info');

                // Create a temporary form to submit the request
                const form = document.createElement('form');
                form.method = 'GET';
                // Use the new route structure: /export/{reportType}/attendance/{exportType}
                form.action = `/export/${reportType}/attendance/${exportType}`;

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

            // Modal functionality
            const modal = document.getElementById('dutyModal');
            const closeModalBtn = document.getElementById('closeModal');

            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', function() {
                    modal.classList.add('hidden');
                });
            }

            // Close modal on outside click
            modal?.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });

            // Close modal on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    modal.classList.add('hidden');
                }
            });
        });

        function downloadData(module, format) {
            const filename = `${module}_report_${new Date().toISOString().split('T')[0]}.${format}`;
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Downloading...';
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            setTimeout(() => {
                // Show success notification
                const notification = document.createElement('div');
                notification.className =
                    'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3 z-[10000] transform transition-all duration-300';
                notification.innerHTML = `
                    <i class="fas fa-check-circle text-lg"></i>
                    <span class="font-medium">Downloading ${filename}...</span>
                `;
                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.transform = 'translateX(400px)';
                    setTimeout(() => notification.remove(), 300);
                }, 2500);

                btn.innerHTML = originalHTML;
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            }, 1500);
        }
    </script>
@endpush
