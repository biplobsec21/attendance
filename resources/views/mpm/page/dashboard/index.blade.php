@extends('mpm.layouts.app')

@section('title', 'Dashboard Reports')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-gradient-to-br from-indigo-50 to-white shadow-lg rounded-xl p-6">
            <!-- Page Header -->
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Reports</h1>
                <span class="text-sm text-gray-500">Last updated: {{ now()->format('d M Y, h:i A') }}</span>
            </div>

            <!-- Reports Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Attendance Reports -->
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition p-5 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Attendance</h2>
                        <span class="bg-indigo-100 text-indigo-600 px-2 py-1 rounded text-xs">Reports</span>
                    </div>
                    <ul class="text-sm text-gray-600 space-y-2 flex-1">
                        <li>📅 Daily</li>
                        <li>📆 Weekly</li>
                        <li>🗓 Monthly</li>
                    </ul>
                    <a href="#" class="mt-4 text-indigo-600 text-sm font-medium hover:underline">View Details →</a>
                </div>

                <!-- Leave Reports -->
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition p-5 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Leave</h2>
                        <span class="bg-green-100 text-green-600 px-2 py-1 rounded text-xs">Reports</span>
                    </div>
                    <ul class="text-sm text-gray-600 space-y-2 flex-1">
                        <li>🏖 Leave Types</li>
                        <li>📊 Balance</li>
                        <li>📈 Trends</li>
                    </ul>
                    <a href="#" class="mt-4 text-green-600 text-sm font-medium hover:underline">View Details →</a>
                </div>

                <!-- Duty Reports -->
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition p-5 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Duty</h2>
                        <span class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded text-xs">Reports</span>
                    </div>
                    <ul class="text-sm text-gray-600 space-y-2 flex-1">
                        <li>📝 Assignments</li>
                        <li>⏱ Hours</li>
                        <li>⭐ Performance</li>
                    </ul>
                    <a href="#" class="mt-4 text-yellow-600 text-sm font-medium hover:underline">View Details →</a>
                </div>

                <!-- Export Reports -->
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition p-5 flex flex-col">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-700">Export</h2>
                        <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-xs">Reports</span>
                    </div>
                    <ul class="text-sm text-gray-600 space-y-2 flex-1">
                        <li>📑 Excel</li>
                        <li>📄 PDF</li>
                    </ul>
                    <a href="#" class="mt-4 text-red-600 text-sm font-medium hover:underline">View Details →</a>
                </div>

            </div>
        </div>
    </div>
@endsection
