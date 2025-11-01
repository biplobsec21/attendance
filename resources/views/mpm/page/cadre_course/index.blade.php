@extends('mpm.layouts.app')

@section('title', 'Course/Cadre/Ex-Area Lists Manager')

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
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Military Course/Cadre/Ex-Area Management</h1>
                <p class="text-gray-600">Manage soldier course, cadre, and ex-area assignments</p>
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
                    <div class="relative">
                        <input type="text" id="searchInput"
                            placeholder="Search by soldier name, rank, company, or course..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <button id="clearSearch"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 hidden">
                        Clear
                    </button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto">
                        <button
                            class="tab-button active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600 whitespace-nowrap"
                            data-tab="current-courses" data-original-count="{{ $currentCourses->count() }}">
                            Current Courses
                            <span class="ml-2 bg-blue-100 text-blue-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $currentCourses->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                            data-tab="previous-courses" data-original-count="{{ $previousCourses->count() }}">
                            Previous Courses
                            <span class="ml-2 bg-gray-100 text-gray-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $previousCourses->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                            data-tab="current-cadres" data-original-count="{{ $currentCadres->count() }}">
                            Current Cadres
                            <span class="ml-2 bg-green-100 text-green-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $currentCadres->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                            data-tab="previous-cadres" data-original-count="{{ $previousCadres->count() }}">
                            Previous Cadres
                            <span class="ml-2 bg-gray-100 text-gray-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $previousCadres->count() }}
                            </span>
                        </button>
                        <!-- Ex-Areas Tabs -->
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                            data-tab="current-ex-areas" data-original-count="{{ $currentExAreas->count() }}">
                            Current Ex-Areas
                            <span class="ml-2 bg-purple-100 text-purple-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $currentExAreas->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                            data-tab="previous-ex-areas" data-original-count="{{ $previousExAreas->count() }}">
                            Previous Ex-Areas
                            <span class="ml-2 bg-gray-100 text-gray-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $previousExAreas->count() }}
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
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 searchable-row">
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
                                                    <div class="text-sm font-medium text-gray-900 soldier-name">
                                                        {{ $assignment->soldier->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 soldier-details">
                                                        {{ $assignment->soldier->rank->name ?? 'No Rank' }} |
                                                        {{ $assignment->soldier->company->name ?? 'No Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 assignment-name">
                                                {{ $assignment->course->name ?? 'N/A' }}
                                            </div>
                                            @if ($assignment->remarks)
                                                <div class="text-sm text-gray-500 truncate max-w-xs assignment-remarks"
                                                    title="{{ $assignment->remarks }}">
                                                    {{ Str::limit($assignment->remarks, 30) }}
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
                                    <tr class="empty-row">
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
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 searchable-row">
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
                                                    <div class="text-sm font-medium text-gray-900 soldier-name">
                                                        {{ $assignment->soldier->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 soldier-details">
                                                        {{ $assignment->soldier->rank->name ?? 'No Rank' }} |
                                                        {{ $assignment->soldier->company->name ?? 'No Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 assignment-name">
                                                {{ $assignment->course->name ?? 'N/A' }}
                                            </div>
                                            @if ($assignment->remarks)
                                                <div class="text-sm text-gray-500 truncate max-w-xs assignment-remarks"
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
                                    <tr class="empty-row">
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
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 searchable-row">
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
                                                    <div class="text-sm font-medium text-gray-900 soldier-name">
                                                        {{ $assignment->soldier->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 soldier-details">
                                                        {{ $assignment->soldier->rank->name ?? 'No Rank' }} |
                                                        {{ $assignment->soldier->company->name ?? 'No Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 assignment-name">
                                                {{ $assignment->cadre->name ?? 'N/A' }}
                                            </div>
                                            @if ($assignment->remarks)
                                                <div class="text-sm text-gray-500 truncate max-w-xs assignment-remarks"
                                                    title="{{ $assignment->remarks }}">
                                                    {{ Str::limit($assignment->remarks, 30) }}
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
                                    <tr class="empty-row">
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
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 searchable-row">
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
                                                    <div class="text-sm font-medium text-gray-900 soldier-name">
                                                        {{ $assignment->soldier->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 soldier-details">
                                                        {{ $assignment->soldier->rank->name ?? 'No Rank' }} |
                                                        {{ $assignment->soldier->company->name ?? 'No Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 assignment-name">
                                                {{ $assignment->cadre->name ?? 'N/A' }}
                                            </div>
                                            @if ($assignment->remarks)
                                                <div class="text-sm text-gray-500 truncate max-w-xs assignment-remarks"
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
                                    <tr class="empty-row">
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

            <!-- Current Ex-Areas Tab -->
            <div id="current-ex-areas-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <!-- Complete Selected Button (Hidden by default) -->
                    <div class="p-4 bg-gray-50 border-b border-gray-200 hidden" id="completeExAreasButtonContainer">
                        <button onclick="completeSelectedExAreas()"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Complete Selected Ex-Areas
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="selectAllExAreas"
                                            class="form-checkbox h-4 w-4 text-indigo-600">
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Soldier Details
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ex-Area
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Start Date
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="currentExAreasTableBody">
                                @forelse($currentExAreas as $index => $assignment)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 searchable-row">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox"
                                                class="ex-area-checkbox form-checkbox h-4 w-4 text-indigo-600"
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
                                                    <div class="text-sm font-medium text-gray-900 soldier-name">
                                                        {{ $assignment->soldier->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 soldier-details">
                                                        {{ $assignment->soldier->rank->name ?? 'No Rank' }} |
                                                        {{ $assignment->soldier->company->name ?? 'No Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 assignment-name">
                                                {{ $assignment->exArea->name ?? 'N/A' }}
                                            </div>
                                            @if ($assignment->remarks)
                                                <div class="text-sm text-gray-500 truncate max-w-xs assignment-remarks"
                                                    title="{{ $assignment->remarks }}">
                                                    {{ Str::limit($assignment->remarks, 30) }}
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
                                                <button onclick="openEditModal('ex_area', {{ $assignment->id }})"
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
                                                    onclick="completeExArea({{ $assignment->id }}, '{{ $assignment->soldier->full_name ?? 'N/A' }}')"
                                                    class="text-orange-600 hover:text-orange-900 transition-colors duration-200"
                                                    title="Complete Ex-Area">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>

                                                <!-- Delete Button -->
                                                <button onclick="deleteAssignment({{ $assignment->id }}, 'ex_area')"
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
                                    <tr class="empty-row">
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium">No current ex-areas found</p>
                                                <p class="text-sm">Get started by creating a new ex-area assignment</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Previous Ex-Areas Tab -->
            <div id="previous-ex-areas-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Soldier Details
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ex-Area
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Period
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="previousExAreasTableBody">
                                @forelse($previousExAreas as $index => $assignment)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200 searchable-row">
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
                                                    <div class="text-sm font-medium text-gray-900 soldier-name">
                                                        {{ $assignment->soldier->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 soldier-details">
                                                        {{ $assignment->soldier->rank->name ?? 'No Rank' }} |
                                                        {{ $assignment->soldier->company->name ?? 'No Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 assignment-name">
                                                {{ $assignment->exArea->name ?? 'N/A' }}
                                            </div>
                                            @if ($assignment->remarks)
                                                <div class="text-sm text-gray-500 truncate max-w-xs assignment-remarks"
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
                                                <button onclick="viewAssignment({{ $assignment->id }}, 'ex_area')"
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
                                    <tr class="empty-row">
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium">No previous ex-areas found</p>
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

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <div>
                    <p class="text-lg font-medium text-gray-900">Loading...</p>
                    <p class="text-sm text-gray-500">Please wait</p>
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

    <!-- Complete Ex-Area Confirmation Modal -->
    <div id="completeExAreaModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Complete Ex-Area</h3>
                    <button onclick="closeCompleteExAreaModal()" class="text-gray-400 hover:text-gray-600">
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
                        Are you sure you want to mark <span id="soldierNameCompleteExArea" class="font-semibold"></span>'s
                        ex-area as completed?
                    </p>
                    <p class="text-center text-sm text-gray-500">
                        This will move their ex-area assignment to previous ex-areas.
                    </p>
                </div>

                <!-- Form -->
                <form id="completeExAreaForm" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="action" value="complete">

                    <!-- Completion Note -->
                    <div class="mb-6">
                        <label for="completeExAreaNote" class="block text-sm font-medium text-gray-700 mb-1">Completion
                            Note (Optional)</label>
                        <textarea name="completion_note" id="completeExAreaNote" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                            placeholder="Result or comments..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCompleteExAreaModal()"
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
                    <div id="editCourseSection" class="hidden">
                        <label for="editCourseId" class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                        <select id="editCourseId" name="course_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <!-- Options will be loaded dynamically -->
                        </select>
                    </div>

                    <!-- Cadre Selection (shown only for cadres) -->
                    <div id="editCadreSection" class="hidden">
                        <label for="editCadreId" class="block text-sm font-medium text-gray-700 mb-2">Cadre</label>
                        <select id="editCadreId" name="cadre_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <!-- Options will be loaded dynamically -->
                        </select>
                    </div>

                    <!-- Ex-Area Selection (shown only for ex-areas) -->
                    <div id="editExAreaSection" class="hidden">
                        <label for="editExAreaId" class="block text-sm font-medium text-gray-700 mb-2">Ex-Area</label>
                        <select id="editExAreaId" name="ex_area_id"
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
@endsection

@push('scripts')
    <script>
        // Global loading overlay functions
        function showLoadingOverlay() {
            document.getElementById('loadingOverlay').classList.remove('hidden');
        }

        function hideLoadingOverlay() {
            document.getElementById('loadingOverlay').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');

            // Get the active tab from localStorage or default to 'current-courses'
            const activeTab = localStorage.getItem('activeCourseCadreTab') || 'current-courses';

            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            // Function to activate a tab
            function activateTab(tabName) {
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });

                const activeButton = Array.from(tabButtons).find(btn => btn.getAttribute('data-tab') === tabName);
                if (activeButton) {
                    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
                    activeButton.classList.remove('border-transparent', 'text-gray-500');
                }

                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });

                const targetContent = document.getElementById(tabName + '-tab');
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }

                localStorage.setItem('activeCourseCadreTab', tabName);

                if (searchInput && searchInput.value) {
                    const searchTerm = searchInput.value.toLowerCase().trim();
                    if (searchTerm) {
                        performSearch(searchTerm);
                    }
                }
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

            // FIXED SEARCH FUNCTIONALITY
            function performSearch(searchTerm) {
                const allTabs = ['current-courses', 'previous-courses', 'current-cadres', 'previous-cadres',
                    'current-ex-areas', 'previous-ex-areas'
                ];

                allTabs.forEach(tabName => {
                    const tableBodyId = tabName.replace(/-/g, '').charAt(0).toUpperCase() +
                        tabName.replace(/-/g, '').slice(1) + 'TableBody';
                    const tableBody = document.getElementById(tableBodyId);

                    if (!tableBody) return;

                    const rows = tableBody.querySelectorAll('tr.searchable-row');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const soldierName = row.querySelector('.soldier-name')?.textContent
                            ?.toLowerCase() || '';
                        const soldierDetails = row.querySelector('.soldier-details')?.textContent
                            ?.toLowerCase() || '';
                        const assignmentName = row.querySelector('.assignment-name')?.textContent
                            ?.toLowerCase() || '';
                        const assignmentRemarks = row.querySelector('.assignment-remarks')
                            ?.textContent?.toLowerCase() || '';

                        const searchableText =
                            `${soldierName} ${soldierDetails} ${assignmentName} ${assignmentRemarks}`;

                        if (searchableText.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Handle empty state
                    const emptyRow = tableBody.querySelector('tr.empty-row');
                    if (emptyRow) {
                        if (visibleCount === 0 && rows.length > 0) {
                            emptyRow.style.display = '';
                            emptyRow.querySelector('td').innerHTML = `
                                <div class="text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No matching results found</p>
                                    <p class="text-sm">Try adjusting your search terms</p>
                                </div>
                            `;
                        } else {
                            emptyRow.style.display = 'none';
                        }
                    }
                });

                updateTabCounts(searchTerm);
            }

            function updateTabCounts(searchTerm) {
                tabButtons.forEach(button => {
                    const tabName = button.getAttribute('data-tab');
                    const countSpan = button.querySelector(
                        '.bg-blue-100, .bg-green-100, .bg-purple-100, .bg-gray-100');
                    const originalCount = button.getAttribute('data-original-count');

                    if (searchTerm) {
                        const tableBodyId = tabName.replace(/-/g, '').charAt(0).toUpperCase() +
                            tabName.replace(/-/g, '').slice(1) + 'TableBody';
                        const tableBody = document.getElementById(tableBodyId);

                        if (tableBody) {
                            const visibleRows = Array.from(tableBody.querySelectorAll('tr.searchable-row'))
                                .filter(row =>
                                    row.style.display !== 'none'
                                );
                            countSpan.textContent = visibleRows.length;
                        }
                    } else {
                        countSpan.textContent = originalCount;
                    }
                });
            }

            function clearSearchResults() {
                const allTabs = ['current-courses', 'previous-courses', 'current-cadres', 'previous-cadres',
                    'current-ex-areas', 'previous-ex-areas'
                ];

                allTabs.forEach(tabName => {
                    const tableBodyId = tabName.replace(/-/g, '').charAt(0).toUpperCase() +
                        tabName.replace(/-/g, '').slice(1) + 'TableBody';
                    const tableBody = document.getElementById(tableBodyId);

                    if (!tableBody) return;

                    const rows = tableBody.querySelectorAll('tr');
                    rows.forEach(row => {
                        row.style.display = '';
                    });

                    const emptyRow = tableBody.querySelector('tr.empty-row');
                    if (emptyRow) {
                        const dataRows = tableBody.querySelectorAll('tr.searchable-row');
                        if (dataRows.length === 0) {
                            emptyRow.style.display = '';
                        } else {
                            emptyRow.style.display = 'none';
                        }
                    }
                });

                updateTabCounts('');
            }

            // Search input event listener
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();

                    if (searchTerm) {
                        if (clearSearch) clearSearch.classList.remove('hidden');
                        performSearch(searchTerm);
                    } else {
                        if (clearSearch) clearSearch.classList.add('hidden');
                        clearSearchResults();
                    }
                });
            }

            // Clear search functionality
            if (clearSearch) {
                clearSearch.addEventListener('click', function() {
                    if (searchInput) searchInput.value = '';
                    clearSearch.classList.add('hidden');
                    clearSearchResults();
                });
            }

            // Checkbox handlers
            setupCheckboxHandlers();
        });

        function setupCheckboxHandlers() {
            // Course checkboxes
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
                if (completeCoursesButtonContainer) {
                    completeCoursesButtonContainer.classList.toggle('hidden', checkedBoxes.length === 0);
                }
            }

            // Cadre checkboxes
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
                if (completeCadresButtonContainer) {
                    completeCadresButtonContainer.classList.toggle('hidden', checkedBoxes.length === 0);
                }
            }

            // Ex-Area checkboxes
            const selectAllExAreasCheckbox = document.getElementById('selectAllExAreas');
            const exAreaCheckboxes = document.querySelectorAll('.ex-area-checkbox');
            const completeExAreasButtonContainer = document.getElementById('completeExAreasButtonContainer');

            if (selectAllExAreasCheckbox) {
                selectAllExAreasCheckbox.addEventListener('change', function() {
                    exAreaCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    toggleCompleteExAreasButton();
                });
            }

            exAreaCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(exAreaCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(exAreaCheckboxes).some(cb => cb.checked);

                    if (selectAllExAreasCheckbox) {
                        selectAllExAreasCheckbox.checked = allChecked;
                        selectAllExAreasCheckbox.indeterminate = someChecked && !allChecked;
                    }

                    toggleCompleteExAreasButton();
                });
            });

            function toggleCompleteExAreasButton() {
                const checkedBoxes = document.querySelectorAll('.ex-area-checkbox:checked');
                if (completeExAreasButtonContainer) {
                    completeExAreasButtonContainer.classList.toggle('hidden', checkedBoxes.length === 0);
                }
            }
        }

        // Modal and action functions
        function openEditModal(type, id) {
            showLoadingOverlay();

            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('editAssignmentForm').action = `/coursecadremanager/${type}/${id}`;
            document.getElementById('editAssignmentId').value = id;
            document.getElementById('editAssignmentType').value = type;

            document.getElementById('editModalTitle').textContent =
                `Edit ${type === 'course' ? 'Course' : type === 'cadre' ? 'Cadre' : 'Ex-Area'} Assignment`;

            const courseSection = document.getElementById('editCourseSection');
            const cadreSection = document.getElementById('editCadreSection');
            const exAreaSection = document.getElementById('editExAreaSection');
            const courseSelect = document.getElementById('editCourseId');
            const cadreSelect = document.getElementById('editCadreId');
            const exAreaSelect = document.getElementById('editExAreaId');

            courseSection.classList.add('hidden');
            cadreSection.classList.add('hidden');
            exAreaSection.classList.add('hidden');

            courseSelect.removeAttribute('required');
            cadreSelect.removeAttribute('required');
            exAreaSelect.removeAttribute('required');

            if (type === 'course') {
                courseSection.classList.remove('hidden');
                courseSelect.setAttribute('required', 'required');
            } else if (type === 'cadre') {
                cadreSection.classList.remove('hidden');
                cadreSelect.setAttribute('required', 'required');
            } else {
                exAreaSection.classList.remove('hidden');
                exAreaSelect.setAttribute('required', 'required');
            }

            document.getElementById('editAssignmentTypeDisplay').textContent = 'Loading...';
            document.getElementById('editStartDate').value = '';
            document.getElementById('editEndDate').value = '';
            document.getElementById('editNote').value = '';
            document.getElementById('editSoldierId').innerHTML = '<option value="">Loading...</option>';

            fetch(`/coursecadremanager/${type}/${id}/edit-data`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editAssignmentTypeDisplay').textContent =
                        `${type === 'course' ? '📚 Course' : type === 'cadre' ? '👥 Cadre' : '🗺️ Ex-Area'} Assignment`;

                    document.getElementById('editStartDate').value = data.start_date;
                    document.getElementById('editEndDate').value = data.end_date || '';
                    document.getElementById('editNote').value = data.remarks || '';

                    if (type === 'course') {
                        populateDropdown('editCourseId', data.courses, data.course_id);
                    } else if (type === 'cadre') {
                        populateDropdown('editCadreId', data.cadres, data.cadre_id);
                    } else {
                        populateDropdown('editExAreaId', data.ex_areas, data.ex_area_id);
                    }

                    populateSoldierDropdown('editSoldierId', data.soldiers, data.soldier_id, data
                        .completed_today_soldiers);

                    hideLoadingOverlay();
                    document.getElementById('editAssignmentModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching assignment data:', error);
                    hideLoadingOverlay();
                    showError('Failed to load assignment data. Please try again.');
                });
        }

        function closeEditModal() {
            document.getElementById('editAssignmentForm').reset();
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

            const availableSoldiers = soldiers.filter(s => !s.has_active_assignments || s.id == selectedValue);
            const assignedSoldiers = soldiers.filter(s => s.has_active_assignments && s.id != selectedValue);

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

        function completeCourse(id, soldierName) {
            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('completeCourseForm').action = `/coursecadremanager/course/${id}/complete`;
            document.getElementById('soldierNameCompleteCourse').textContent = soldierName;
            document.getElementById('completeCourseNote').value = '';
            document.getElementById('completeCourseModal').classList.remove('hidden');
        }

        function closeCompleteCourseModal() {
            document.querySelector('#completeCourseModal h3').textContent = 'Complete Course';
            document.querySelector('#completeCourseModal .text-gray-700').innerHTML =
                'Are you sure you want to mark <span id="soldierNameCompleteCourse" class="font-semibold"></span>\'s course as completed?';
            document.getElementById('completeCourseNote').value = '';

            const existingInput = document.querySelector('input[name="course_ids"]');
            if (existingInput) existingInput.remove();

            document.getElementById('completeCourseModal').classList.add('hidden');
        }

        function completeCadre(id, soldierName) {
            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('completeCadreForm').action = `/coursecadremanager/cadre/${id}/complete`;
            document.getElementById('soldierNameCompleteCadre').textContent = soldierName;
            document.getElementById('completeCadreNote').value = '';
            document.getElementById('completeCadreModal').classList.remove('hidden');
        }

        function closeCompleteCadreModal() {
            document.querySelector('#completeCadreModal h3').textContent = 'Complete Cadre';
            document.querySelector('#completeCadreModal .text-gray-700').innerHTML =
                'Are you sure you want to mark <span id="soldierNameCompleteCadre" class="font-semibold"></span>\'s cadre as completed?';
            document.getElementById('completeCadreNote').value = '';

            const existingInput = document.querySelector('input[name="cadre_ids"]');
            if (existingInput) existingInput.remove();

            document.getElementById('completeCadreModal').classList.add('hidden');
        }

        function completeExArea(id, soldierName) {
            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('completeExAreaForm').action = `/coursecadremanager/ex_area/${id}/complete`;
            document.getElementById('soldierNameCompleteExArea').textContent = soldierName;
            document.getElementById('completeExAreaNote').value = '';
            document.getElementById('completeExAreaModal').classList.remove('hidden');
        }

        function closeCompleteExAreaModal() {
            document.querySelector('#completeExAreaModal h3').textContent = 'Complete Ex-Area';
            document.querySelector('#completeExAreaModal .text-gray-700').innerHTML =
                'Are you sure you want to mark <span id="soldierNameCompleteExArea" class="font-semibold"></span>\'s ex-area as completed?';
            document.getElementById('completeExAreaNote').value = '';

            const existingInput = document.querySelector('input[name="ex_area_ids"]');
            if (existingInput) existingInput.remove();

            document.getElementById('completeExAreaModal').classList.add('hidden');
        }

        function deleteAssignment(id, type) {
            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            if (confirm('Are you sure you want to delete this assignment? This action cannot be undone.')) {
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
                tokenInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';

                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function viewAssignment(id, type) {
            // Implement view logic
        }

        function completeSelectedCourses() {
            const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');
            if (checkedBoxes.length === 0) {
                showError('Please select at least one course to complete.');
                return;
            }

            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('completeCourseForm').action = `/coursecadremanager/courses/bulk-complete`;

            const courseIdsInput = document.createElement('input');
            courseIdsInput.type = 'hidden';
            courseIdsInput.name = 'course_ids';
            const courseIds = Array.from(checkedBoxes).map(cb => cb.dataset.id);
            courseIdsInput.value = JSON.stringify(courseIds);

            const existingInput = document.querySelector('input[name="course_ids"]');
            if (existingInput) existingInput.remove();

            document.getElementById('completeCourseForm').appendChild(courseIdsInput);

            document.querySelector('#completeCourseModal h3').textContent = 'Complete Selected Courses';
            const message =
                `Are you sure you want to mark ${courseIds.length} course${courseIds.length > 1 ? 's' : ''} as completed?`;
            document.querySelector('#completeCourseModal .text-gray-700').innerHTML = message;

            document.getElementById('completeCourseModal').classList.remove('hidden');
        }

        function completeSelectedCadres() {
            const checkedBoxes = document.querySelectorAll('.cadre-checkbox:checked');
            if (checkedBoxes.length === 0) {
                showError('Please select at least one cadre to complete.');
                return;
            }

            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('completeCadreForm').action = `/coursecadremanager/cadres/bulk-complete`;

            const cadreIdsInput = document.createElement('input');
            cadreIdsInput.type = 'hidden';
            cadreIdsInput.name = 'cadre_ids';
            const cadreIds = Array.from(checkedBoxes).map(cb => cb.dataset.id);
            cadreIdsInput.value = JSON.stringify(cadreIds);

            const existingInput = document.querySelector('input[name="cadre_ids"]');
            if (existingInput) existingInput.remove();

            document.getElementById('completeCadreForm').appendChild(cadreIdsInput);

            document.querySelector('#completeCadreModal h3').textContent = 'Complete Selected Cadres';
            const message =
                `Are you sure you want to mark ${cadreIds.length} cadre${cadreIds.length > 1 ? 's' : ''} as completed?`;
            document.querySelector('#completeCadreModal .text-gray-700').innerHTML = message;

            document.getElementById('completeCadreModal').classList.remove('hidden');
        }

        function completeSelectedExAreas() {
            const checkedBoxes = document.querySelectorAll('.ex-area-checkbox:checked');
            if (checkedBoxes.length === 0) {
                showError('Please select at least one ex-area to complete.');
                return;
            }

            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('completeExAreaForm').action = `/coursecadremanager/ex-areas/bulk-complete`;

            const exAreaIdsInput = document.createElement('input');
            exAreaIdsInput.type = 'hidden';
            exAreaIdsInput.name = 'ex_area_ids';
            const exAreaIds = Array.from(checkedBoxes).map(cb => cb.dataset.id);
            exAreaIdsInput.value = JSON.stringify(exAreaIds);

            const existingInput = document.querySelector('input[name="ex_area_ids"]');
            if (existingInput) existingInput.remove();

            document.getElementById('completeExAreaForm').appendChild(exAreaIdsInput);

            document.querySelector('#completeExAreaModal h3').textContent = 'Complete Selected Ex-Areas';
            const message =
                `Are you sure you want to mark ${exAreaIds.length} ex-area${exAreaIds.length > 1 ? 's' : ''} as completed?`;
            document.querySelector('#completeExAreaModal .text-gray-700').innerHTML = message;

            document.getElementById('completeExAreaModal').classList.remove('hidden');
        }

        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.remove('hidden');
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.add('hidden');
        }

        function showSuccess(message) {
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
            setTimeout(() => alert.remove(), 5000);
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = ['completeCourseModal', 'completeCadreModal', 'completeExAreaModal', 'errorModal',
                'editAssignmentModal'
            ];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    if (modalId === 'completeCourseModal') closeCompleteCourseModal();
                    else if (modalId === 'completeCadreModal') closeCompleteCadreModal();
                    else if (modalId === 'completeExAreaModal') closeCompleteExAreaModal();
                    else if (modalId === 'errorModal') closeErrorModal();
                    else if (modalId === 'editAssignmentModal') closeEditModal();
                }
            });
        }

        // Handle edit form submission
        document.addEventListener('DOMContentLoaded', function() {
            const editAssignmentForm = document.getElementById('editAssignmentForm');
            if (editAssignmentForm) {
                editAssignmentForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const type = document.getElementById('editAssignmentType').value;
                    const startDate = document.getElementById('editStartDate').value;
                    const soldierId = document.getElementById('editSoldierId').value;

                    if (!startDate) {
                        showError('Start date is required.');
                        return;
                    }

                    if (!soldierId) {
                        showError('Soldier is required.');
                        return;
                    }

                    if (type === 'course' && !document.getElementById('editCourseId').value) {
                        showError('Course is required.');
                        return;
                    } else if (type === 'cadre' && !document.getElementById('editCadreId').value) {
                        showError('Cadre is required.');
                        return;
                    } else if (type === 'ex_area' && !document.getElementById('editExAreaId').value) {
                        showError('Ex-Area is required.');
                        return;
                    }

                    showLoadingOverlay();

                    const formData = new FormData(this);
                    const id = document.getElementById('editAssignmentId').value;

                    fetch(`/coursecadremanager/${type}/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'X-HTTP-Method-Override': 'PUT'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            hideLoadingOverlay();
                            if (data.success) {
                                closeEditModal();
                                showSuccess(data.message);
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                showError(data.message || 'Failed to update assignment.');
                            }
                        })
                        .catch(error => {
                            hideLoadingOverlay();
                            console.error('Error updating assignment:', error);
                            showError('Failed to update assignment. Please try again.');
                        });
                });
            }

            // Handle completion form submissions
            const completeCourseForm = document.getElementById('completeCourseForm');
            const completeCadreForm = document.getElementById('completeCadreForm');
            const completeExAreaForm = document.getElementById('completeExAreaForm');

            if (completeCourseForm) {
                completeCourseForm.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Completing...';
                });
            }

            if (completeCadreForm) {
                completeCadreForm.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Completing...';
                });
            }

            if (completeExAreaForm) {
                completeExAreaForm.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Completing...';
                });
            }

            // Date adjustment warning handler
            const editSoldierId = document.getElementById('editSoldierId');
            const editStartDate = document.getElementById('editStartDate');

            if (editSoldierId && editStartDate) {
                editSoldierId.addEventListener('change', checkForDateAdjustment);
                editStartDate.addEventListener('change', checkForDateAdjustment);
            }
        });

        function checkForDateAdjustment() {
            const selectedOption = document.getElementById('editSoldierId')?.selectedOptions[0];
            const startDate = document.getElementById('editStartDate')?.value;
            const warning = document.getElementById('editDateAdjustmentWarning');

            if (selectedOption && selectedOption.textContent.includes('[Completed Today]') &&
                startDate === new Date().toISOString().split('T')[0]) {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
    </script>
@endpush
