@extends('mpm.layouts.app')

@section('title', 'Duty Details - ' . $duty->duty_name)

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-6 px-4">
        <div class="container mx-auto max-w-6xl">
            <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />

            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Duty Details</h1>
                            <p class="text-sm text-gray-500">Complete information about this duty assignment</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('duty.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to List
                        </a>
                        <a href="{{ route('duty.edit', $duty) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Duty
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Basic Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
                            <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Duty Name</label>
                                    <p class="text-lg font-semibold text-gray-900">{{ $duty->duty_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $duty->status == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        @if ($duty->status == 'Active')
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        @endif
                                        {{ $duty->status }}
                                    </span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Schedule</label>
                                    <p class="text-gray-900">
                                        {{ $duty->start_time->format('H:i') }} - {{ $duty->end_time->format('H:i') }}
                                        @if ($duty->duration_days > 1)
                                            <span class="text-blue-600 text-sm ml-2">({{ $duty->duration_days }}
                                                days)</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">{{ $scheduleDescription }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Hours</label>
                                    <p class="text-gray-900">{{ number_format($totalHours, 1) }} hours total</p>
                                    <p class="text-sm text-gray-500">
                                        {{ number_format($totalHours / $duty->duration_days, 1) }} hours daily
                                    </p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                                    <p class="text-gray-900">{{ $duty->remark ?: 'No remarks provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assignments Overview Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
                            <h2 class="text-lg font-semibold text-gray-900">Assignments Overview</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="text-center">
                                    <div
                                        class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $duty->manpower }}</h3>
                                    <p class="text-sm text-gray-600">Total Manpower</p>
                                </div>
                                <div class="text-center">
                                    <div
                                        class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $fixedAssignments->count() }}</h3>
                                    <p class="text-sm text-gray-600">Fixed Soldiers</p>
                                </div>
                                <div class="text-center">
                                    <div
                                        class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $rosterAssignments->count() }}</h3>
                                    <p class="text-sm text-gray-600">Roster Assignments</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fixed Soldiers Card -->
                    @if ($fixedAssignments->count() > 0)
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-green-50">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-semibold text-gray-900">Fixed Soldiers</h2>
                                    <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                                        {{ $fixedAssignments->count() }} assigned
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach ($fixedAssignments as $assignment)
                                        <div
                                            class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200">
                                            <div class="flex items-center space-x-4">
                                                <div
                                                    class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-900">
                                                        {{ $assignment->soldier->full_name }}</h4>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $assignment->soldier->army_no }} •
                                                        {{ $assignment->soldier->rank->name }} •
                                                        {{ $assignment->soldier->company->name ?? 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                {{-- <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    Priority: {{ $assignment->priority }}
                                                </span> --}}
                                                @if ($assignment->remarks)
                                                    <p class="text-xs text-gray-500 mt-1">{{ $assignment->remarks ?? '' }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Roster Assignments Card -->
                    @if ($rosterAssignments->count() > 0)
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-purple-50">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-semibold text-gray-900">Roster Assignments</h2>
                                    <span class="bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                                        {{ $rosterAssignments->count() }} assignments
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach ($rosterAssignments->groupBy('rank_id') as $rankId => $assignments)
                                        @php
                                            $assignment = $assignments->first();
                                            $totalManpower = $assignments->sum('manpower');
                                        @endphp
                                        <div
                                            class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border border-purple-200">
                                            <div class="flex items-center space-x-4">
                                                <div
                                                    class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-900">{{ $assignment->rank->name }}
                                                    </h4>
                                                    <p class="text-sm text-gray-600">
                                                        {{ $totalManpower }} soldier(s) required
                                                        @if ($assignment->group_id)
                                                            <span class="text-purple-600">(Group Assignment)</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    {{ $totalManpower }} manpower
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Meta Information -->
                <div class="space-y-6">
                    <!-- Duty Statistics Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-orange-50">
                            <h2 class="text-lg font-semibold text-gray-900">Duty Statistics</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Created</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $duty->created_at->format('M j, Y g:i A') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Last Updated</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $duty->updated_at->format('M j, Y g:i A') }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Duration</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $duty->duration_days }}
                                        day(s)</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Daily Hours</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ number_format($totalHours / $duty->duration_days, 1) }}h</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Hours</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ number_format($totalHours, 1) }}h</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
                            <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <a href="{{ route('duty.edit', $duty) }}"
                                    class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Duty
                                </a>

                                <form action="{{ route('duty.duplicate', $duty) }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Duplicate Duty
                                    </button>
                                </form>

                                <form action="{{ route('duty.destroy', $duty) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this duty? This action cannot be undone.');"
                                    class="w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full flex items-center justify-center px-4 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete Duty
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment Summary Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-indigo-50">
                            <h2 class="text-lg font-semibold text-gray-900">Assignment Summary</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Fixed Soldiers</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $fixedAssignments->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Roster Assignments</span>
                                    <span
                                        class="text-sm font-medium text-gray-900">{{ $rosterAssignments->count() }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Manpower</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $duty->manpower }}</span>
                                </div>
                                {{-- <div class="pt-3 border-t border-gray-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-700">Coverage</span>
                                        <span
                                            class="text-sm font-medium {{ $duty->manpower > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $duty->manpower > 0 ? 'Fully Staffed' : 'Understaffed' }}
                                        </span>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
