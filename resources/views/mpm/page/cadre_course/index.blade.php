@extends('mpm.layouts.app')

@section('title', 'Course/Cadre Lists Manager')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs_auto()" />

        <!-- Enhanced error display -->
        @if ($errors->any())
            <div class="mb-4">
                <div class="bg-red-50 border-l-4 border-red-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                There were errors with your submission:
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @include('mpm.components.alerts')

        <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Military Course/Cadre Management</h1>
                <p class="text-gray-600">Manage soldier course and cadre assignments</p>
            </div>

            <!-- Action Buttons -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('coursecadremanager.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Assignment
                    </a>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text" id="searchInput" placeholder="Search soldiers..."
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button
                            class="tab-button active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600"
                            data-tab="current-courses">
                            Current Courses
                            <span class="ml-2 bg-blue-100 text-blue-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $currentCourses->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                            data-tab="previous-courses">
                            Previous Courses
                            <span class="ml-2 bg-gray-100 text-gray-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $previousCourses->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                            data-tab="current-cadres">
                            Current Cadres
                            <span class="ml-2 bg-green-100 text-green-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $currentCadres->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                            data-tab="previous-cadres">
                            Previous Cadres
                            <span class="ml-2 bg-gray-100 text-gray-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $previousCadres->count() }}
                            </span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Current Courses Tab -->
            <div id="current-courses-tab" class="tab-content">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <!-- Complete Selected Button (Hidden by default) -->
                    <div class="p-4 bg-gray-50 border-b border-gray-200 hidden" id="completeCoursesButtonContainer">
                        <button onclick="completeSelectedCourses()"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Complete Selected Courses
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAllCourses"
                                            class="form-checkbox h-4 w-4 text-indigo-600">
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Soldier Details</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Course</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Start Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="currentCoursesTableBody">
                                @forelse($currentCourses as $index => $assignment)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox"
                                                class="course-checkbox form-checkbox h-4 w-4 text-indigo-600"
                                                data-id="{{ $assignment->id }}"
                                                data-soldier-name="{{ $assignment->soldier->full_name ?? 'N/A' }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ strtoupper(substr($assignment->soldier->full_name ?? 'N/A', 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $assignment->soldier->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $assignment->soldier->rank->name ?? 'No Rank' }} |
                                                        {{ $assignment->soldier->company->name ?? 'No Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $assignment->course->name ?? 'N/A' }}
                                            </div>
                                            @if ($assignment->note)
                                                <div class="text-sm text-gray-500 truncate max-w-xs"
                                                    title="{{ $assignment->note }}">
                                                    {{ Str::limit($assignment->note, 30) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $assignment->start_date ? \Carbon\Carbon::parse($assignment->start_date)->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($assignment->status === 'active')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @elseif ($assignment->status === 'scheduled')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Scheduled
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <!-- Edit Button -->
                                                <button onclick="openEditModal('course', {{ $assignment->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200"
                                                    title="Edit Assignment">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <!-- Complete Button -->
                                                <button
                                                    onclick="completeCourse({{ $assignment->id }}, '{{ $assignment->soldier->full_name ?? 'N/A' }}')"
                                                    class="text-orange-600 hover:text-orange-900 transition-colors duration-200"
                                                    title="Complete Course">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>

                                                <!-- Delete Button -->
                                                <button onclick="deleteAssignment({{ $assignment->id }}, 'course')"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                    title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium">No current courses found</p>
                                                <p class="text-sm">Get started by creating a new course assignment</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Previous Courses Tab -->
            <div id="previous-courses-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Soldier Details</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Course</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Period</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="previousCoursesTableBody">
                                @forelse($previousCourses as $index => $assignment)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ strtoupper(substr($assignment->soldier->full_name ?? 'N/A', 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $assignment->soldier->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $assignment->soldier->rank->name ?? 'No Rank' }} |
                                                        {{ $assignment->soldier->company->name ?? 'No Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $assignment->course->name ?? 'N/A' }}
                                            </div>
                                            @if ($assignment->note)
                                                <div class="text-sm text-gray-500 truncate max-w-xs"
                                                    title="{{ $assignment->note }}">
                                                    {{ Str::limit($assignment->note, 30) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>
                                                <div class="text-xs text-gray-500">From:</div>
                                                {{ $assignment->start_date ? \Carbon\Carbon::parse($assignment->start_date)->format('M d, Y') : 'N/A' }}
                                            </div>
                                            @if ($assignment->end_date)
                                                <div class="mt-1">
                                                    <div class="text-xs text-gray-500">To:</div>
                                                    {{ \Carbon\Carbon::parse($assignment->end_date)->format('M d, Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Completed
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <!-- View Button -->
                                                <button onclick="viewAssignment({{ $assignment->id }}, 'course')"
                                                    class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                    title="View Details">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium">No previous courses found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Current Cadres Tab -->
            <div id="current-cadres-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <!-- Complete Selected Button (Hidden by default) -->
                    <div class="p-4 bg-gray-50 border-b border-gray-200 hidden" id="completeCadresButtonContainer">
                        <button onclick="completeSelectedCadres()"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Complete Selected Cadres
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAllCadres"
                                            class="form-checkbox h-4 w-4 text-indigo-600">
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Soldier Details</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cadre</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Start Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="currentCadresTableBody">
                                @forelse($currentCadres as $index => $assignment)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox"
                                                class="cadre-checkbox form-checkbox h-4 w-4 text-indigo-600"
                                                data-id="{{ $assignment->id }}"
                                                data-soldier-name="{{ $assignment->soldier->full_name ?? 'N/A' }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ strtoupper(substr($assignment->soldier->full_name ?? 'N/A', 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $assignment->soldier->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $assignment->soldier->rank->name ?? 'No Rank' }} |
                                                        {{ $assignment->soldier->company->name ?? 'No Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $assignment->cadre->name ?? 'N/A' }}
                                            </div>
                                            @if ($assignment->remarks)
                                                <div class="text-sm text-gray-500 truncate max-w-xs"
                                                    title="{{ $assignment->remarks }}">
                                                    {{ Str::limit($assignment->remarks, 30) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $assignment->from_date ? \Carbon\Carbon::parse($assignment->from_date)->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <!-- Edit Button -->
                                                <button onclick="openEditModal('cadre', {{ $assignment->id }})"
                                                    class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200"
                                                    title="Edit Assignment">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <!-- Complete Button -->
                                                <button
                                                    onclick="completeCadre({{ $assignment->id }}, '{{ $assignment->soldier->full_name ?? 'N/A' }}')"
                                                    class="text-orange-600 hover:text-orange-900 transition-colors duration-200"
                                                    title="Complete Cadre">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>

                                                <!-- Delete Button -->
                                                <button onclick="deleteAssignment({{ $assignment->id }}, 'cadre')"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                    title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium">No current cadres found</p>
                                                <p class="text-sm">Get started by creating a new cadre assignment</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Previous Cadres Tab -->
            <div id="previous-cadres-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Soldier Details</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cadre</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Period</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="previousCadresTableBody">
                                @forelse($previousCadres as $index => $assignment)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ strtoupper(substr($assignment->soldier->full_name ?? 'N/A', 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $assignment->soldier->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $assignment->soldier->rank->name ?? 'No Rank' }} |
                                                        {{ $assignment->soldier->company->name ?? 'No Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $assignment->cadre->name ?? 'N/A' }}
                                            </div>
                                            @if ($assignment->remarks)
                                                <div class="text-sm text-gray-500 truncate max-w-xs"
                                                    title="{{ $assignment->remarks }}">
                                                    {{ Str::limit($assignment->remarks, 30) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>
                                                <div class="text-xs text-gray-500">From:</div>
                                                {{ $assignment->start_date ? \Carbon\Carbon::parse($assignment->start_date)->format('M d, Y') : 'N/A' }}
                                            </div>
                                            @if ($assignment->end_date)
                                                <div class="mt-1">
                                                    <div class="text-xs text-gray-500">To:</div>
                                                    {{ \Carbon\Carbon::parse($assignment->end_date)->format('M d, Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Completed
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <!-- View Button -->
                                                <button onclick="viewAssignment({{ $assignment->id }}, 'cadre')"
                                                    class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                    title="View Details">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium">No previous cadres found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Course Confirmation Modal -->
    <div id="completeCourseModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Complete Course</h3>
                    <button onclick="closeCompleteCourseModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="mb-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-orange-100 rounded-full">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-center text-gray-700 mb-4">
                        Are you sure you want to mark <span id="soldierNameCompleteCourse" class="font-semibold"></span>'s
                        course as completed?
                    </p>
                    <p class="text-center text-sm text-gray-500">
                        This will move their course assignment to previous courses.
                    </p>
                </div>

                <!-- Form -->
                <form id="completeCourseForm" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="action" value="complete">

                    <!-- Completion Note -->
                    <div class="mb-6">
                        <label for="completeCourseNote" class="block text-sm font-medium text-gray-700 mb-1">Completion
                            Note (Optional)</label>
                        <textarea name="completion_note" id="completeCourseNote" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                            placeholder="Result or comments..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCompleteCourseModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors duration-200">
                            Mark as Completed
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Complete Cadre Confirmation Modal -->
    <div id="completeCadreModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Complete Cadre</h3>
                    <button onclick="closeCompleteCadreModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="mb-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-orange-100 rounded-full">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-center text-gray-700 mb-4">
                        Are you sure you want to mark <span id="soldierNameCompleteCadre" class="font-semibold"></span>'s
                        cadre as completed?
                    </p>
                    <p class="text-center text-sm text-gray-500">
                        This will move their cadre assignment to previous cadres.
                    </p>
                </div>

                <!-- Form -->
                <form id="completeCadreForm" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="action" value="complete">

                    <!-- Completion Note -->
                    <div class="mb-6">
                        <label for="completeCadreNote" class="block text-sm font-medium text-gray-700 mb-1">Completion
                            Note (Optional)</label>
                        <textarea name="completion_note" id="completeCadreNote" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                            placeholder="Result or comments..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCompleteCadreModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors duration-200">
                            Mark as Completed
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Error</h3>
                    <button onclick="closeErrorModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="mb-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-center text-gray-700 mb-4" id="errorMessage">
                        An error occurred while processing your request.
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeErrorModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Assignment Modal -->
    <div id="editAssignmentModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="text-lg font-medium text-gray-900" id="editModalTitle">Edit Assignment</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="mb-6">
                <form id="editAssignmentForm" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editAssignmentId" name="assignment_id">
                    <input type="hidden" id="editAssignmentType" name="type">

                    <!-- Assignment Type Display -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assignment Type</label>
                        <div id="editAssignmentTypeDisplay"
                            class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900 font-medium">
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Start Date -->
                        <div>
                            <label for="editStartDate" class="block text-sm font-medium text-gray-700 mb-2">Start
                                Date</label>
                            <input type="date" id="editStartDate" name="start_date" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="editEndDate" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" id="editEndDate" name="end_date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <!-- Course Selection (shown only for courses) -->
                    <!-- Course Selection (shown only for courses) -->
                    <div id="editCourseSection" class="hidden">
                        <label for="editCourseId" class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                        <select id="editCourseId" name="course_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <!-- Options will be loaded dynamically -->
                        </select>
                    </div>

                    <!-- Cadre Selection (shown only for cadres) -->
                    <!-- Cadre Selection (shown only for cadres) -->
                    <div id="editCadreSection" class="hidden">
                        <label for="editCadreId" class="block text-sm font-medium text-gray-700 mb-2">Cadre</label>
                        <select id="editCadreId" name="cadre_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <!-- Options will be loaded dynamically -->
                        </select>
                    </div>

                    <!-- Soldier Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Soldier</label>
                        <select id="editSoldierId" name="soldier_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <!-- Options will be loaded dynamically -->
                        </select>
                    </div>

                    <!-- Notes Section -->
                    <div>
                        <label for="editNote" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea id="editNote" name="note" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <!-- Date Adjustment Warning -->
                    <div id="editDateAdjustmentWarning" class="hidden p-3 bg-amber-50 rounded-lg border border-amber-200">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-amber-500 mt-0.5 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            <p class="text-sm text-amber-700">
                                The start date will be adjusted to tomorrow if the soldier has completed assignments today.
                            </p>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition-colors duration-200">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the active tab from localStorage or default to 'current-courses'
            const activeTab = localStorage.getItem('activeCourseCadreTab') || 'current-courses';

            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            // Function to activate a tab
            function activateTab(tabName) {
                // Remove active classes from all buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });

                // Find and activate the clicked button
                const activeButton = Array.from(tabButtons).find(btn => btn.getAttribute('data-tab') === tabName);
                if (activeButton) {
                    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
                    activeButton.classList.remove('border-transparent', 'text-gray-500');
                }

                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });

                // Show target tab content
                const targetContent = document.getElementById(tabName + '-tab');
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }

                // Store the active tab in localStorage
                localStorage.setItem('activeCourseCadreTab', tabName);
            }

            // Activate the stored tab on page load
            activateTab(activeTab);

            // Add click event listeners to tab buttons
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetTab = button.getAttribute('data-tab');
                    activateTab(targetTab);
                });
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
                const tableBody = document.getElementById(activeTab.replace('-', '') + 'TableBody');

                if (tableBody) {
                    const rows = tableBody.querySelectorAll('tr');
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }
            });

            // Course selection handling
            const selectAllCoursesCheckbox = document.getElementById('selectAllCourses');
            const courseCheckboxes = document.querySelectorAll('.course-checkbox');
            const completeCoursesButtonContainer = document.getElementById('completeCoursesButtonContainer');

            if (selectAllCoursesCheckbox) {
                selectAllCoursesCheckbox.addEventListener('change', function() {
                    courseCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    toggleCompleteCoursesButton();
                });
            }

            courseCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Update "select all" checkbox state
                    const allChecked = Array.from(courseCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(courseCheckboxes).some(cb => cb.checked);

                    if (selectAllCoursesCheckbox) {
                        selectAllCoursesCheckbox.checked = allChecked;
                        selectAllCoursesCheckbox.indeterminate = someChecked && !allChecked;
                    }

                    toggleCompleteCoursesButton();
                });
            });

            function toggleCompleteCoursesButton() {
                const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');
                if (checkedBoxes.length > 0) {
                    completeCoursesButtonContainer.classList.remove('hidden');
                } else {
                    completeCoursesButtonContainer.classList.add('hidden');
                }
            }

            // Cadre selection handling
            const selectAllCadresCheckbox = document.getElementById('selectAllCadres');
            const cadreCheckboxes = document.querySelectorAll('.cadre-checkbox');
            const completeCadresButtonContainer = document.getElementById('completeCadresButtonContainer');

            if (selectAllCadresCheckbox) {
                selectAllCadresCheckbox.addEventListener('change', function() {
                    cadreCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    toggleCompleteCadresButton();
                });
            }

            cadreCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Update "select all" checkbox state
                    const allChecked = Array.from(cadreCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(cadreCheckboxes).some(cb => cb.checked);

                    if (selectAllCadresCheckbox) {
                        selectAllCadresCheckbox.checked = allChecked;
                        selectAllCadresCheckbox.indeterminate = someChecked && !allChecked;
                    }

                    toggleCompleteCadresButton();
                });
            });

            function toggleCompleteCadresButton() {
                const checkedBoxes = document.querySelectorAll('.cadre-checkbox:checked');
                if (checkedBoxes.length > 0) {
                    completeCadresButtonContainer.classList.remove('hidden');
                } else {
                    completeCadresButtonContainer.classList.add('hidden');
                }
            }
        });

        // Action functions
        function viewAssignment(id, type) {
            // You can redirect to a view page or show a modal
            // window.location.href = `/coursecadremanager/${type}/${id}`;
        }

        function completeCourse(id, soldierName) {
            // Store the current active tab before showing modal
            const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
            localStorage.setItem('activeCourseCadreTab', activeTab);

            // Set form action
            document.getElementById('completeCourseForm').action = `/coursecadremanager/course/${id}/complete`;

            // Set soldier name
            document.getElementById('soldierNameCompleteCourse').textContent = soldierName;

            // Reset completion note
            document.getElementById('completeCourseNote').value = '';

            // Show modal
            document.getElementById('completeCourseModal').classList.remove('hidden');
        }

        function closeCompleteCourseModal() {
            // Reset modal title
            document.querySelector('#completeCourseModal h3').textContent = 'Complete Course';

            // Reset modal message
            document.querySelector('#completeCourseModal .text-gray-700').innerHTML =
                'Are you sure you want to mark <span id="soldierNameCompleteCourse" class="font-semibold"></span>\'s course as completed?';

            // Reset completion note
            document.getElementById('completeCourseNote').value = '';

            // Remove any course_ids input
            const existingInput = document.querySelector('input[name="course_ids"]');
            if (existingInput) {
                existingInput.remove();
            }

            // Hide modal
            document.getElementById('completeCourseModal').classList.add('hidden');
        }

        function completeCadre(id, soldierName) {
            // Store the current active tab before showing modal
            const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
            localStorage.setItem('activeCourseCadreTab', activeTab);

            // Set form action
            document.getElementById('completeCadreForm').action = `/coursecadremanager/cadre/${id}/complete`;

            // Set soldier name
            document.getElementById('soldierNameCompleteCadre').textContent = soldierName;

            // Reset completion note
            document.getElementById('completeCadreNote').value = '';

            // Show modal
            document.getElementById('completeCadreModal').classList.remove('hidden');
        }

        function closeCompleteCadreModal() {
            // Reset modal title
            document.querySelector('#completeCadreModal h3').textContent = 'Complete Cadre';

            // Reset modal message
            document.querySelector('#completeCadreModal .text-gray-700').innerHTML =
                'Are you sure you want to mark <span id="soldierNameCompleteCadre" class="font-semibold"></span>\'s cadre as completed?';

            // Reset completion note
            document.getElementById('completeCadreNote').value = '';

            // Remove any cadre_ids input
            const existingInput = document.querySelector('input[name="cadre_ids"]');
            if (existingInput) {
                existingInput.remove();
            }

            // Hide modal
            document.getElementById('completeCadreModal').classList.add('hidden');
        }

        function deleteAssignment(id, type) {
            // Store the current active tab before deletion
            const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
            localStorage.setItem('activeCourseCadreTab', activeTab);

            if (confirm('Are you sure you want to delete this assignment? This action cannot be undone.')) {
                // Create a form and submit it for DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/coursecadremanager/${type}/${id}`;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';

                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const completeCourseModal = document.getElementById('completeCourseModal');
            const completeCadreModal = document.getElementById('completeCadreModal');
            const errorModal = document.getElementById('errorModal');

            if (event.target === completeCourseModal) {
                closeCompleteCourseModal();
            }
            if (event.target === completeCadreModal) {
                closeCompleteCadreModal();
            }
            if (event.target === errorModal) {
                closeErrorModal();
            }
        }

        // Handle form submissions with loading states
        document.getElementById('completeCourseForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Completing...';
        });

        document.getElementById('completeCadreForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Completing...';
        });

        // Function to complete selected courses
        function completeSelectedCourses() {
            const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');

            if (checkedBoxes.length === 0) {
                showError('Please select at least one course to complete.');
                return;
            }

            // Store the current active tab
            const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
            localStorage.setItem('activeCourseCadreTab', activeTab);

            // Set form action for bulk completion
            document.getElementById('completeCourseForm').action = `/coursecadremanager/courses/bulk-complete`;

            // Add hidden input with course IDs
            const courseIdsInput = document.createElement('input');
            courseIdsInput.type = 'hidden';
            courseIdsInput.name = 'course_ids';

            const courseIds = Array.from(checkedBoxes).map(cb => cb.dataset.id);
            courseIdsInput.value = JSON.stringify(courseIds);

            // Remove any existing course_ids input
            const existingInput = document.querySelector('input[name="course_ids"]');
            if (existingInput) {
                existingInput.remove();
            }

            document.getElementById('completeCourseForm').appendChild(courseIdsInput);

            // Update modal title
            document.querySelector('#completeCourseModal h3').textContent = 'Complete Selected Courses';

            // Update modal message
            const message =
                `Are you sure you want to mark ${courseIds.length} course${courseIds.length > 1 ? 's' : ''} as completed?`;
            document.querySelector('#completeCourseModal .text-gray-700').innerHTML = message;

            // Show modal
            document.getElementById('completeCourseModal').classList.remove('hidden');
        }

        // Function to complete selected cadres
        function completeSelectedCadres() {
            const checkedBoxes = document.querySelectorAll('.cadre-checkbox:checked');

            if (checkedBoxes.length === 0) {
                showError('Please select at least one cadre to complete.');
                return;
            }

            // Store the current active tab
            const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
            localStorage.setItem('activeCourseCadreTab', activeTab);

            // Set form action for bulk completion
            document.getElementById('completeCadreForm').action = `/coursecadremanager/cadres/bulk-complete`;

            // Add hidden input with cadre IDs
            const cadreIdsInput = document.createElement('input');
            cadreIdsInput.type = 'hidden';
            cadreIdsInput.name = 'cadre_ids';

            const cadreIds = Array.from(checkedBoxes).map(cb => cb.dataset.id);
            cadreIdsInput.value = JSON.stringify(cadreIds);

            // Remove any existing cadre_ids input
            const existingInput = document.querySelector('input[name="cadre_ids"]');
            if (existingInput) {
                existingInput.remove();
            }

            document.getElementById('completeCadreForm').appendChild(cadreIdsInput);

            // Update modal title
            document.querySelector('#completeCadreModal h3').textContent = 'Complete Selected Cadres';

            // Update modal message
            const message =
                `Are you sure you want to mark ${cadreIds.length} cadre${cadreIds.length > 1 ? 's' : ''} as completed?`;
            document.querySelector('#completeCadreModal .text-gray-700').innerHTML = message;

            // Show modal
            document.getElementById('completeCadreModal').classList.remove('hidden');
        }

        // Error modal functions
        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.remove('hidden');
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.add('hidden');
        }

        // Edit Assignment Modal Functions
        function openEditModal(type, id) {
            // Store the current active tab before showing modal
            const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
            localStorage.setItem('activeCourseCadreTab', activeTab);

            // Set form action
            document.getElementById('editAssignmentForm').action = `/coursecadremanager/${type}/${id}`;

            // Set hidden fields
            document.getElementById('editAssignmentId').value = id;
            document.getElementById('editAssignmentType').value = type;

            // Set modal title
            document.getElementById('editModalTitle').textContent =
                `Edit ${type === 'course' ? 'Course' : 'Cadre'} Assignment`;

            // Show appropriate sections based on type and handle required attributes
            const courseSection = document.getElementById('editCourseSection');
            const cadreSection = document.getElementById('editCadreSection');
            const courseSelect = document.getElementById('editCourseId');
            const cadreSelect = document.getElementById('editCadreId');

            if (type === 'course') {
                courseSection.classList.remove('hidden');
                cadreSection.classList.add('hidden');

                // Make course required and cadre not required
                courseSelect.setAttribute('required', 'required');
                cadreSelect.removeAttribute('required');
            } else {
                courseSection.classList.add('hidden');
                cadreSection.classList.remove('hidden');

                // Make cadre required and course not required
                courseSelect.removeAttribute('required');
                cadreSelect.setAttribute('required', 'required');
            }

            // Show loading state
            document.getElementById('editAssignmentTypeDisplay').textContent = 'Loading...';
            document.getElementById('editStartDate').value = '';
            document.getElementById('editEndDate').value = '';
            document.getElementById('editNote').value = '';
            document.getElementById('editSoldierId').innerHTML = '<option value="">Loading...</option>';

            // Fetch assignment data
            fetch(`/coursecadremanager/${type}/${id}/edit-data`)
                .then(response => response.json())
                .then(data => {
                    // Populate form fields
                    document.getElementById('editAssignmentTypeDisplay').textContent =
                        `${type === 'course' ? '📚 Course' : '👥 Cadre'} Assignment`;

                    document.getElementById('editStartDate').value = data.start_date;
                    document.getElementById('editEndDate').value = data.end_date || '';
                    document.getElementById('editNote').value = data.remarks || '';

                    // Populate course/cadre dropdown
                    if (type === 'course') {
                        populateDropdown('editCourseId', data.courses, data.course_id);
                    } else {
                        populateDropdown('editCadreId', data.cadres, data.cadre_id);
                    }

                    // Populate soldier dropdown
                    populateSoldierDropdown('editSoldierId', data.soldiers, data.soldier_id, data
                        .completed_today_soldiers);

                    // Show modal
                    document.getElementById('editAssignmentModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching assignment data:', error);
                    showError('Failed to load assignment data. Please try again.');
                });
        }

        function closeEditModal() {
            // Reset form
            document.getElementById('editAssignmentForm').reset();

            // Hide modal
            document.getElementById('editAssignmentModal').classList.add('hidden');
        }

        function populateDropdown(selectId, options, selectedValue) {
            const select = document.getElementById(selectId);
            select.innerHTML = '';

            options.forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option.id;
                optionElement.textContent = option.name;
                optionElement.selected = option.id == selectedValue;
                select.appendChild(optionElement);
            });
        }

        function populateSoldierDropdown(selectId, soldiers, selectedValue, completedTodaySoldiers) {
            const select = document.getElementById(selectId);
            select.innerHTML = '';

            // Group soldiers into available and assigned
            const availableSoldiers = soldiers.filter(s => !s.has_active_assignments || s.id == selectedValue);
            const assignedSoldiers = soldiers.filter(s => s.has_active_assignments && s.id != selectedValue);

            // Add available soldiers
            if (availableSoldiers.length > 0) {
                const optgroup = document.createElement('optgroup');
                optgroup.label = 'Available Soldiers';

                availableSoldiers.forEach(soldier => {
                    const option = document.createElement('option');
                    option.value = soldier.id;
                    option.textContent =
                        `${soldier.full_name} (${soldier.army_no}) - ${soldier.rank.name}, ${soldier.company.name}`;
                    option.selected = soldier.id == selectedValue;

                    if (completedTodaySoldiers.includes(soldier.id)) {
                        option.textContent += ' [Completed Today]';
                    }

                    optgroup.appendChild(option);
                });

                select.appendChild(optgroup);
            }

            // Add assigned soldiers
            if (assignedSoldiers.length > 0) {
                const optgroup = document.createElement('optgroup');
                optgroup.label = 'Assigned Soldiers (Unavailable)';
                optgroup.disabled = true;

                assignedSoldiers.forEach(soldier => {
                    const option = document.createElement('option');
                    option.value = soldier.id;
                    option.textContent =
                        `${soldier.full_name} (${soldier.army_no}) - ${soldier.rank.name}, ${soldier.company.name}`;
                    option.disabled = true;
                    optgroup.appendChild(option);
                });

                select.appendChild(optgroup);
            }
        }

        // Handle form submission
        // Handle form submission
        document.getElementById('editAssignmentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Get the assignment type
            const type = document.getElementById('editAssignmentType').value;

            // Manually validate the form based on the type
            const startDate = document.getElementById('editStartDate').value;
            const soldierId = document.getElementById('editSoldierId').value;
            const note = document.getElementById('editNote').value;

            // Validate required fields
            if (!startDate) {
                showError('Start date is required.');
                return;
            }

            if (!soldierId) {
                showError('Soldier is required.');
                return;
            }

            if (type === 'course') {
                const courseId = document.getElementById('editCourseId').value;
                if (!courseId) {
                    showError('Course is required.');
                    return;
                }
            } else {
                const cadreId = document.getElementById('editCadreId').value;
                if (!cadreId) {
                    showError('Cadre is required.');
                    return;
                }
            }

            const formData = new FormData(this);
            const id = document.getElementById('editAssignmentId').value;

            fetch(`/coursecadremanager/${type}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal
                        closeEditModal();

                        // Show success message
                        showSuccess(data.message);

                        // Reload the page after a short delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showError(data.message || 'Failed to update assignment.');
                    }
                })
                .catch(error => {
                    console.error('Error updating assignment:', error);
                    showError('Failed to update assignment. Please try again.');
                });
        });

        // Handle date adjustment warning
        document.getElementById('editSoldierId').addEventListener('change', function() {
            checkForDateAdjustment();
        });

        document.getElementById('editStartDate').addEventListener('change', function() {
            checkForDateAdjustment();
        });

        function checkForDateAdjustment() {
            const selectedOption = document.getElementById('editSoldierId').selectedOptions[0];
            const startDate = document.getElementById('editStartDate').value;
            const warning = document.getElementById('editDateAdjustmentWarning');

            if (selectedOption && selectedOption.textContent.includes('[Completed Today]') &&
                startDate === new Date().toISOString().split('T')[0]) {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }

        function showSuccess(message) {
            // Create success alert
            const alert = document.createElement('div');
            alert.className =
                'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
            alert.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>${message}</span>
        </div>
    `;

            document.body.appendChild(alert);

            // Remove after 5 seconds
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        function showError(message) {
            // Create error alert
            const alert = document.createElement('div');
            alert.className = 'fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50';
            alert.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>${message}</span>
        </div>
    `;

            document.body.appendChild(alert);

            // Remove after 5 seconds
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    </script>
@endsection
