@extends('mpm.layouts.app')

@section('title', 'Create Service')

@section('content')
    {{-- Profile Steps Navigation --}}
    @php
        // Get previous appointments from DB
        $dbPreviousAppointments = $profile->services
            ->where('appointment_type', 'previous')
            ->map(function ($s) {
                return [
                    'id' => $s->appointment_id,
                    'name' => $s->appointments_name,
                    'from_date' => $s->appointments_from_date
                        ? \Carbon\Carbon::parse($s->appointments_from_date)->format('Y-m-d')
                        : null,
                    'to_date' => $s->appointments_to_date
                        ? \Carbon\Carbon::parse($s->appointments_to_date)->format('Y-m-d')
                        : null,
                ];
            })
            ->toArray();

        // Get current appointment from DB
        $dbCurrentAppointment = $profile->services->where('appointment_type', 'current')->first();
        $currentId = old(
            'current_appointment_id',
            $dbCurrentAppointment ? $dbCurrentAppointment->appointment_id : null,
        );
        $currentFromDate = old(
            'current_appointment_from_date',
            $dbCurrentAppointment
                ? \Carbon\Carbon::parse($dbCurrentAppointment->appointments_from_date)->format('Y-m-d')
                : null,
        );

        // Use old input if exists, otherwise DB data
        $previousAppointments = old('previous_appointments', $dbPreviousAppointments);
    @endphp

    <x-profile-step-nav :steps="$profileSteps" :profileId="$profile->id ?? null" />

    <main class="container mx-auto px-4 py-6 max-w-7xl">
        <!-- Modern Header Section -->
        <div class="mb-8">
            <div
                class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-2xl shadow-xl">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>

                <div class="relative px-8 py-12">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold text-white">Service Details</h1>
                                    <p class="text-blue-100 mt-1">Manage your service history and appointment records</p>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="flex items-center space-x-6 mt-6">
                                <div class="flex items-center text-white/90">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm">{{ count($dbPreviousAppointments) }} Previous Appointments</span>
                                </div>
                                <div class="flex items-center text-white/90">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-sm">{{ $dbCurrentAppointment ? 'Active Assignment' : 'No Active Assignment' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="hidden lg:block">
                            @include('mpm.components.alerts')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Alerts -->
            <div class="lg:hidden mt-4">
                @include('mpm.components.alerts')
            </div>
        </div>

        <form action="{{ route('soldier.saveService', $profile->id) }}" method="POST" id="service-form" class="space-y-8">
            @csrf
            @php
                $redirectAction = $profile->service_completed;
            @endphp
            <input type="hidden" name="redirect" value="{{ $redirectAction }}" />

            <!-- Service Dates Section -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-8 py-6 border-b border-gray-100">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Service Period</h2>
                            <p class="text-gray-600 text-sm">Your military service duration and timeline</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Date Input -->
                        <div class="space-y-4">
                            <div>
                                <label for="joining_date"
                                    class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Date of Joining
                                </label>
                                <div class="relative">
                                    <x-form.input name="joining_date" type="date"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200"
                                        :value="old(
                                            'joining_date',
                                            $profile?->joining_date
                                                ? \Carbon\Carbon::parse($profile->joining_date)->format('Y-m-d')
                                                : '',
                                        )" />
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Select your military service commencement date</p>
                            </div>
                        </div>

                        <!-- Service Length Display -->
                        <div class="space-y-4">
                            <div>
                                <label class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Total Service Length
                                </label>
                                <div class="relative">
                                    <input id="service-length" type="text" readonly
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl bg-gray-50 text-gray-700 font-medium">
                                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Automatically calculated from joining date</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Appointment Section -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-8 py-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V6m8 0V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Current Appointment</h2>
                                <p class="text-gray-600 text-sm">Your active assignment and responsibilities</p>
                            </div>
                        </div>

                        @if ($dbCurrentAppointment)
                            <div
                                class="flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                Active
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-8">
                    @if ($dbCurrentAppointment)
                        <div class="relative">
                            <div
                                class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 to-indigo-500 rounded-full">
                            </div>
                            <div class="ml-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-800 mb-2">
                                            {{ $dbCurrentAppointment->appointments_name }}</h3>
                                        <div class="flex items-center text-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <span>Started
                                                {{ $dbCurrentAppointment->appointments_from_date ? \Carbon\Carbon::parse($dbCurrentAppointment->appointments_from_date)->format('F j, Y') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-blue-600">
                                            @if ($dbCurrentAppointment->appointments_from_date)
                                                {{ \Carbon\Carbon::parse($dbCurrentAppointment->appointments_from_date)->diffInDays(\Carbon\Carbon::now()) }}
                                            @else
                                                0
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">Days Active</div>
                                    </div>
                                </div>

                                @if ($dbCurrentAppointment->note)
                                    <div class="mt-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                        <div class="flex items-center mb-2">
                                            <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            <span class="text-sm font-semibold text-blue-800">Notes</span>
                                        </div>
                                        <p class="text-sm text-blue-700">{{ $dbCurrentAppointment->note }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">No Current Appointment</h3>
                            <p class="text-gray-500">You don't have any active appointments at the moment</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Previous Appointments Section -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-8 py-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Service History</h2>
                                <p class="text-gray-600 text-sm">Your previous appointments and assignments</p>
                            </div>
                        </div>

                        @if ($dbPreviousAppointments)
                            <!-- Modern Toggle Buttons -->
                            <div class="flex bg-gray-100 rounded-xl p-1">
                                <button type="button" onclick="showCompactView()" id="compactViewBtn"
                                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-white text-gray-800 shadow-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                    Compact
                                </button>
                                <button type="button" onclick="showDetailedView()" id="detailedViewBtn"
                                    class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 text-gray-600 hover:text-gray-800">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                    Detailed
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="p-8">
                    @if ($dbPreviousAppointments)
                        {{-- Compact View (Default) --}}
                        <div id="compactView">
                            <!-- Timeline View -->
                            <div class="relative">
                                <div
                                    class="absolute left-6 top-8 bottom-8 w-0.5 bg-gradient-to-b from-purple-400 to-pink-400 rounded-full">
                                </div>

                                <div class="space-y-6">
                                    @foreach ($dbPreviousAppointments as $index => $prev)
                                        <div class="relative flex items-center group">
                                            <!-- Timeline Dot -->
                                            <div
                                                class="absolute left-4 w-4 h-4 bg-white border-3 border-purple-400 rounded-full z-10 group-hover:border-purple-600 transition-colors duration-200">
                                            </div>

                                            <!-- Content Card -->
                                            <div class="ml-12 flex-1">
                                                <div
                                                    class="bg-gradient-to-r from-white to-purple-50 rounded-xl p-6 border border-purple-100 hover:border-purple-200 hover:shadow-md transition-all duration-200">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <h4 class="text-lg font-semibold text-gray-800">{{ $prev['name'] }}
                                                        </h4>
                                                        <span
                                                            class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                            Completed
                                                        </span>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                        <div class="flex items-center text-gray-600">
                                                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                </path>
                                                            </svg>
                                                            <span>From:
                                                                {{ $prev['from_date'] ? \Carbon\Carbon::parse($prev['from_date'])->format('M j, Y') : 'N/A' }}</span>
                                                        </div>
                                                        <div class="flex items-center text-gray-600">
                                                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                </path>
                                                            </svg>
                                                            <span>To:
                                                                {{ $prev['to_date'] ? \Carbon\Carbon::parse($prev['to_date'])->format('M j, Y') : 'N/A' }}</span>
                                                        </div>
                                                        <div class="flex items-center text-gray-600">
                                                            <svg class="w-4 h-4 mr-2 text-purple-500" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span>
                                                                @if ($prev['from_date'] && $prev['to_date'])
                                                                    {{ \Carbon\Carbon::parse($prev['from_date'])->diffInDays(\Carbon\Carbon::parse($prev['to_date'])) }}
                                                                    days
                                                                @else
                                                                    Duration: N/A
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Enhanced Summary Stats -->
                            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div
                                    class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-3xl font-bold text-blue-700">
                                                {{ count($dbPreviousAppointments) }}</div>
                                            <div class="text-sm font-medium text-blue-600">Total Appointments</div>
                                        </div>
                                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 border border-green-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-3xl font-bold text-green-700">
                                                {{ $dbPreviousAppointments ? \Carbon\Carbon::parse($dbPreviousAppointments[0]['from_date'])->diffInYears(\Carbon\Carbon::now()) : '0' }}
                                            </div>
                                            <div class="text-sm font-medium text-green-600">Years Experience</div>
                                        </div>
                                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 border border-purple-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-3xl font-bold text-purple-700">100%</div>
                                            <div class="text-sm font-medium text-purple-600">Success Rate</div>
                                        </div>
                                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Detailed View (Hidden by default) --}}
                        <div id="detailedView" class="hidden">
                            <div class="space-y-6 max-h-96 overflow-y-auto pr-2">
                                @foreach ($dbPreviousAppointments as $index => $prev)
                                    <div
                                        class="bg-gradient-to-r from-white to-gray-50 border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center mr-4">
                                                    <span class="text-white font-bold text-lg">{{ $index + 1 }}</span>
                                                </div>
                                                <div>
                                                    <h3 class="text-xl font-bold text-gray-800">{{ $prev['name'] }}</h3>
                                                    <div class="flex items-center mt-1">
                                                        <span
                                                            class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                                            Completed
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <div class="text-sm text-gray-500">Duration</div>
                                                <div class="text-lg font-bold text-gray-800">
                                                    @if ($prev['from_date'] && $prev['to_date'])
                                                        {{ \Carbon\Carbon::parse($prev['from_date'])->diffInDays(\Carbon\Carbon::parse($prev['to_date'])) }}
                                                        days
                                                    @else
                                                        N/A
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                                <div class="flex items-center mb-2">
                                                    <svg class="w-4 h-4 text-blue-500 mr-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    <span class="text-sm font-semibold text-blue-700">Start Date</span>
                                                </div>
                                                <p class="text-blue-800 font-medium">
                                                    {{ $prev['from_date'] ? \Carbon\Carbon::parse($prev['from_date'])->format('F j, Y') : 'N/A' }}
                                                </p>
                                            </div>

                                            <div class="bg-red-50 rounded-xl p-4 border border-red-100">
                                                <div class="flex items-center mb-2">
                                                    <svg class="w-4 h-4 text-red-500 mr-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    <span class="text-sm font-semibold text-red-700">End Date</span>
                                                </div>
                                                <p class="text-red-800 font-medium">
                                                    {{ $prev['to_date'] ? \Carbon\Carbon::parse($prev['to_date'])->format('F j, Y') : 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Service History Found</h3>
                            <p class="text-gray-500">Your previous appointments will appear here once they're completed</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modern Navigation - Fixed on Scroll -->
            <div
                class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-gray-200 shadow-lg">
                <div class="container mx-auto px-4 py-4 max-w-7xl">
                    <div class="flex items-right justify-between">

                        <button type="submit" id="next-btn"
                            class="flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-200 hover:shadow-lg transform hover:scale-105">
                            Save & Continue
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Spacer to prevent content overlap -->
            <div class="h-20"></div>
        </form>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const joiningDateInput = document.querySelector('input[name="joining_date"]');
            const serviceLengthInput = document.getElementById("service-length");
            const form = document.getElementById("service-form");

            function calculateServiceLength() {
                const joinDate = new Date(joiningDateInput.value);
                const today = new Date();

                if (!joiningDateInput.value) {
                    serviceLengthInput.value = "Please select joining date";
                    return;
                }

                // Calculate difference in milliseconds
                let diff = today - joinDate;
                if (diff < 0) diff = 0; // joining date in future

                // Convert milliseconds to days
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const years = Math.floor(days / 365);
                const months = Math.floor((days % 365) / 30);
                const remainingDays = (days % 365) % 30;

                serviceLengthInput.value = `${years} years, ${months} months, ${remainingDays} days`;
            }

            // Calculate on page load
            calculateServiceLength();
            joiningDateInput.addEventListener("change", calculateServiceLength);

            // Enhanced View toggle functions with smooth animations
            window.showCompactView = function() {
                const compactView = document.getElementById('compactView');
                const detailedView = document.getElementById('detailedView');
                const compactBtn = document.getElementById('compactViewBtn');
                const detailedBtn = document.getElementById('detailedViewBtn');

                // Fade out current view
                detailedView.style.opacity = '0';
                setTimeout(() => {
                    detailedView.classList.add('hidden');
                    compactView.classList.remove('hidden');
                    compactView.style.opacity = '0';

                    // Fade in new view
                    setTimeout(() => {
                        compactView.style.opacity = '1';
                    }, 50);
                }, 150);

                // Update buttons
                compactBtn.classList.add('bg-white', 'text-gray-800', 'shadow-sm');
                compactBtn.classList.remove('text-gray-600');
                detailedBtn.classList.add('text-gray-600');
                detailedBtn.classList.remove('bg-white', 'text-gray-800', 'shadow-sm');
            }

            window.showDetailedView = function() {
                const compactView = document.getElementById('compactView');
                const detailedView = document.getElementById('detailedView');
                const compactBtn = document.getElementById('compactViewBtn');
                const detailedBtn = document.getElementById('detailedViewBtn');

                // Fade out current view
                compactView.style.opacity = '0';
                setTimeout(() => {
                    compactView.classList.add('hidden');
                    detailedView.classList.remove('hidden');
                    detailedView.style.opacity = '0';

                    // Fade in new view
                    setTimeout(() => {
                        detailedView.style.opacity = '1';
                    }, 50);
                }, 150);

                // Update buttons
                detailedBtn.classList.add('bg-white', 'text-gray-800', 'shadow-sm');
                detailedBtn.classList.remove('text-gray-600');
                compactBtn.classList.add('text-gray-600');
                compactBtn.classList.remove('bg-white', 'text-gray-800', 'shadow-sm');
            }

            // Enhanced form validation with visual feedback
            form.addEventListener("submit", function(e) {
                const submitBtn = document.getElementById('next-btn');

                if (!joiningDateInput.value) {
                    e.preventDefault();

                    // Add error styling
                    joiningDateInput.classList.add("border-red-500", "ring-4", "ring-red-500/20");

                    // Show error message
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'text-red-600 text-sm mt-1 flex items-center';
                    errorMsg.innerHTML = `
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Date of Joining is required
                    `;
                    joiningDateInput.parentElement.appendChild(errorMsg);

                    // Scroll to error
                    joiningDateInput.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    // Remove error after 3 seconds
                    setTimeout(() => {
                        joiningDateInput.classList.remove("border-red-500", "ring-4",
                            "ring-red-500/20");
                        errorMsg.remove();
                    }, 3000);

                } else {
                    // Success styling
                    joiningDateInput.classList.remove("border-red-500", "ring-4", "ring-red-500/20");
                    joiningDateInput.classList.add("border-green-500", "ring-4", "ring-green-500/20");

                    // Loading state for button
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    `;
                    submitBtn.disabled = true;
                }
            });

            // Add smooth transitions to all elements
            const elements = document.querySelectorAll('[class*="transition"]');
            elements.forEach(el => {
                el.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            });
        });
    </script>
@endpush
