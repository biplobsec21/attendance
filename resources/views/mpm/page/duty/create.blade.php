@extends('mpm.layouts.app')

@section('title', 'Create Duty Record')

@section('content')

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8 px-4">
        <div class="container mx-auto max-w-4xl">
            {{-- <x-breadcrumb :breadcrumbs="[
                ['name' => 'Dashboard', 'url' => route('dashboard')],
                ['name' => 'Duty Management', 'url' => route('duty.index')],
                ['name' => 'Create Duty', 'url' => route('duty.create')],
            ]" /> --}}
            {{-- <x-breadcrumb :breadcrumbs="generateBreadcrumbs_auto()" /> --}}
            <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />

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
            <div class="mb-6">
                <div class="flex items-center gap-3 p-3 bg-white/50 rounded-xl border border-gray-200">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Create Duty Record</h1>
                        <p class="text-sm text-gray-500">Fill in the details to create a new duty assignment</p>
                    </div>
                </div>
            </div>


            <!-- Main Form Card -->
            <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-3xl border border-white/50 overflow-hidden">
                <div class="p-8">
                    <form method="POST" action="{{ route('duty.store') }}" id="duty-form">
                        @csrf

                        <div class="space-y-8">
                            {{-- Duty Name Input --}}
                            <div class="group">
                                <label for="duty-name" class="block text-sm font-semibold text-gray-700 mb-3">
                                    Duty Name <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="duty-name" name="duty_name" value="{{ old('duty_name') }}"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 @error('duty_name') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                        placeholder="Enter duty name" required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('duty_name')
                                    <p class="text-rose-500 text-sm mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Duty Schedule Section --}}
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    Duty Schedule <span class="text-rose-500">*</span>
                                </label>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                    {{-- Start Time --}}
                                    <div class="time-input-container">
                                        <label for="start-time"
                                            class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
                                            Daily Start Time
                                        </label>
                                        <div class="relative">
                                            <input type="text" id="start-time" name="start_time"
                                                value="{{ old('start_time', '08:00') }}"
                                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 time-input flatpickr-input @error('start_time') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                                placeholder="Select start time" readonly required>
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                        @error('start_time')
                                            <p class="text-rose-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- End Time --}}
                                    <div class="time-input-container">
                                        <label for="end-time"
                                            class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
                                            Daily End Time
                                        </label>
                                        <div class="relative">
                                            <input type="text" id="end-time" name="end_time"
                                                value="{{ old('end_time', '17:00') }}"
                                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 time-input flatpickr-input @error('end_time') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                                placeholder="Select end time" readonly required>
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                        @error('end_time')
                                            <p class="text-rose-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Duration Days --}}
                                    <div>
                                        <label for="duration-days"
                                            class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
                                            Duration (Days)
                                        </label>
                                        <div class="relative">
                                            <select id="duration-days" name="duration_days"
                                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 appearance-none @error('duration_days') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                                required>
                                                <option value="">Select Days</option>
                                                @for ($days = 1; $days <= 30; $days++)
                                                    @php
                                                        $selected = old('duration_days', 1) == $days ? 'selected' : '';
                                                        $display = $days . ' day' . ($days > 1 ? 's' : '');
                                                    @endphp
                                                    <option value="{{ $days }}" {{ $selected }}>
                                                        {{ $display }}</option>
                                                @endfor
                                            </select>
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        @error('duration_days')
                                            <p class="text-rose-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Duration Display --}}
                                <div id="duration-display"
                                    class="bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-sm font-medium text-blue-800">Duty Schedule:</span>
                                            <span id="schedule-display"
                                                class="text-lg font-bold text-blue-600 ml-2"></span>
                                        </div>
                                        <div id="multi-day-indicator" class="hidden">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                Multi-Day Duty
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-2 grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-blue-600 font-medium">Daily Duration:</span>
                                            <span id="daily-duration" class="ml-2">0 hours</span>
                                        </div>
                                        <div>
                                            <span class="text-blue-600 font-medium">Total Duration:</span>
                                            <span id="total-duration" class="ml-2">0 hours</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Ranks & Manpower Section --}}
                            <div class="group">
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        Roster Assignments (Rank-based) <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="text-sm text-gray-600 bg-blue-50 px-3 py-1 rounded-lg">
                                        Total Manpower: <span id="total-manpower-display"
                                            class="font-bold text-blue-600">0</span>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-600 mb-4">
                                    Click on ranks below to add them. Use the "+" button to create OR groups.
                                </p>

                                <!-- Available Ranks Grid -->
                                <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl p-4 mb-4">
                                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Available Ranks</h3>

                                    <!-- Search Box -->
                                    <div class="relative mb-3">
                                        <input type="text" id="rank-search" placeholder="Search ranks..."
                                            class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                        <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>

                                    <!-- Ranks Grid -->
                                    <div id="available-ranks-grid"
                                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                        @foreach ($ranks as $rank)
                                            <button type="button"
                                                class="rank-button px-3 py-2 bg-white border-2 border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:border-blue-400 hover:bg-blue-50 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                                data-rank-id="{{ $rank->id }}" data-rank-name="{{ $rank->name }}">
                                                {{ $rank->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Selected Items Area -->
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-semibold text-gray-700">Selected Roster Items</h3>
                                        <button type="button" id="add-or-group"
                                            class="px-3 py-1.5 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-200 transition-colors">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Add OR Group
                                        </button>
                                    </div>

                                    <div id="selected-items-container" class="space-y-3 min-h-[100px]">
                                        <!-- Selected items will appear here -->
                                        <div id="empty-state"
                                            class="text-center py-8 text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            <p class="text-sm">No ranks selected yet</p>
                                            <p class="text-xs mt-1">Click on ranks above to add them</p>
                                        </div>
                                    </div>
                                </div>

                                @error('rank_manpower')
                                    <p class="text-rose-500 text-sm mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror

                                <!-- Hidden input for total manpower -->
                                <input type="hidden" id="total-manpower" name="manpower" value="0">
                            </div>

                            {{-- Fixed Soldier Assignments --}}
                            <div class="group">
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        Fixed Soldier Assignments
                                    </label>
                                    <button type="button" id="add-fixed-soldier"
                                        class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-sm font-medium hover:bg-green-200 transition-colors">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add Fixed Soldier
                                    </button>
                                </div>

                                <p class="text-sm text-gray-600 mb-4">
                                    Assign specific soldiers to this duty (these assignments are fixed and don't count
                                    toward total manpower).
                                </p>

                                <!-- Fixed Soldiers Container -->
                                <div id="fixed-soldiers-container" class="space-y-3">
                                    <!-- Fixed soldiers will be added here dynamically -->
                                </div>
                            </div>

                            {{-- Remark Input --}}
                            <div class="group">
                                <label for="remark"
                                    class="block text-sm font-semibold text-gray-700 mb-3">Remark</label>
                                <div class="relative">
                                    <textarea id="remark" name="remark" rows="4"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 resize-none @error('remark') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                        placeholder="Enter any remarks or additional information...">{{ old('remark') }}</textarea>
                                    <div class="absolute top-3 right-3">
                                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('remark')
                                    <p class="text-rose-500 text-sm mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Status Select --}}
                            <div class="group">
                                <label for="status" class="block text-sm font-semibold text-gray-700 mb-3">
                                    Status <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="status" name="status"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 appearance-none @error('status') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                        required>
                                        <option value="Active" @if (old('status', 'Active') == 'Active') selected @endif>
                                            🟢 Active
                                        </option>
                                        <option value="Inactive" @if (old('status') == 'Inactive') selected @endif>
                                            🔴 Inactive
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('status')
                                    <p class="text-rose-500 text-sm mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                        {{-- Excused Options Section --}}
                        {{-- Excused Options Section --}}
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                Excused Options
                            </label>

                            <div
                                class="bg-gradient-to-br from-gray-50 to-blue-50 border border-blue-100 rounded-xl p-4 space-y-3">
                                <div class="flex items-start space-x-3">
                                    <input type="checkbox" id="default-excusal" checked disabled
                                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label for="default-excusal" class="text-sm text-gray-700">
                                        <span class="font-semibold text-blue-700">Default Excusal:</span>
                                        Soldiers on this duty are exempt from PT, Parade, Games, and Roll Calls for the
                                        duration of the session
                                    </label>
                                </div>

                                <div>
                                    <div class="flex items-center mb-2">
                                        <span class="font-semibold text-blue-700">Excused Next Session:</span>
                                        <span class="text-sm text-gray-600 ml-2">Select activities exempt from PT, Games,
                                            Parade, and Roll Call after duty</span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div class="flex items-start space-x-3">
                                            <input type="checkbox" id="excused-next-day-pt"
                                                name="excused_next_session_pt" value="1"
                                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                {{ old('excused_next_session_pt') ? 'checked' : '' }}>
                                            <label for="excused-next-day-pt" class="text-sm text-gray-700">
                                                PT ({{ $siteSettings->pt_time->format('H:i') ?? 'N/A' }})
                                            </label>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <input type="checkbox" id="excused-next-day-games"
                                                name="excused_next_session_games" value="1"
                                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                {{ old('excused_next_session_games') ? 'checked' : '' }}>
                                            <label for="excused-next-day-games" class="text-sm text-gray-700">
                                                Games ({{ $siteSettings->games_time->format('H:i') ?? 'N/A' }})
                                            </label>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <input type="checkbox" id="excused-next-day-roll-call"
                                                name="excused_next_session_roll_call" value="1"
                                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                {{ old('excused_next_session_roll_call') ? 'checked' : '' }}>
                                            <label for="excused-next-day-roll-call" class="text-sm text-gray-700">
                                                Roll Call ({{ $siteSettings->roll_call_time->format('H:i') ?? 'N/A' }})
                                            </label>
                                        </div>
                                        <div class="flex items-start space-x-3">
                                            <input type="checkbox" id="excused-next-day-parade"
                                                name="excused_next_session_parade" value="1"
                                                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                {{ old('excused_next_session_parade') ? 'checked' : '' }}>
                                            <label for="excused-next-day-parade" class="text-sm text-gray-700">
                                                Parade ({{ $siteSettings->parade_time->format('H:i') ?? 'N/A' }})
                                            </label>
                                        </div>
                                    </div>
                                    <div class="m-3">
                                        Edit these times in <a href="{{ route('settings.edit') }}"
                                            class="text-blue-600 underline hover:text-blue-800">Site Settings</a>.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('duty.index') }}"
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:border-gray-400 hover:bg-gray-50 transition-all duration-300 no-underline group">
                                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to List
                            </a>
                            <button type="submit"
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white font-semibold hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-500/50 transition-all duration-300 shadow-lg hover:shadow-xl group">
                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Save Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Soldier Selection Modal -->
    <!-- Soldier Selection Modal -->
    @include('mpm.components.soldier-selection-modal', ['availableSoldiers' => $availableSoldiers ?? []])

@endsection

@push('scripts')
    <style>
        /* Custom Flatpickr Styles */
        .flatpickr-input {
            cursor: pointer;
            background-color: white;
        }

        .flatpickr-input:read-only {
            background-color: #f9fafb;
        }

        .flatpickr-calendar {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid #e5e7eb;
        }

        .flatpickr-time {
            background-color: white;
            border-top: 1px solid #e5e7eb;
            border-radius: 0 0 0.75rem 0.75rem;
        }

        .flatpickr-time .numInputWrapper {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }

        .flatpickr-time .numInputWrapper:hover {
            border-color: #3b82f6;
        }

        .flatpickr-time .flatpickr-am-pm:hover,
        .flatpickr-time .flatpickr-am-pm:focus {
            background-color: #3b82f6;
            color: white;
        }

        .flatpickr-day.selected {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .flatpickr-day:hover {
            background-color: #dbeafe;
            border-color: #dbeafe;
        }
    </style>

    <script>
        // Initialize available soldiers data for create view
        window.availableSoldiers = @json($availableSoldiers ?? []);
        window.initialIndividualRanks = {};
        window.initialRankGroups = [];
        window.initialFixedSoldiers = {};
        window.loadSoldiersRoute = "{{ route('duty.load-soldiers') }}";

        console.log('Available Soldiers Data:', @json($availableSoldiers ?? []));
        console.log('Available Soldiers Count:', {{ count($availableSoldiers ?? []) }});
        console.log('Initial Individual Ranks:', window.initialIndividualRanks);
        console.log('Initial Rank Groups:', window.initialRankGroups);
        console.log('Initial Fixed Soldiers:', window.initialFixedSoldiers);
    </script>
    <script src="{{ asset('asset/js/duty-form.js') }}"></script>
    <script>
        // Enhanced SoldierLoader with improved UI and pagination
        class SoldierLoader {
            constructor() {
                this.currentPage = 1;
                this.totalPages = 1;
                this.perPage = 10;
                this.currentFilters = {
                    search: '',
                    rank_id: ''
                };
                this.isLoading = false;
                this.initialized = false;
                this.currentSoldiers = [];

                this.init();
            }

            init() {
                this.bindEvents();
            }

            bindEvents() {
                // Search input with debounce
                const soldierSearch = document.getElementById('soldier-search');
                const clearSearch = document.getElementById('clear-search');

                if (soldierSearch) {
                    soldierSearch.addEventListener('input', this.debounce(() => {
                        this.currentFilters.search = soldierSearch.value;

                        // Show/hide clear button
                        if (clearSearch) {
                            clearSearch.classList.toggle('hidden', !soldierSearch.value);
                        }

                        this.resetAndLoad();
                    }, 500));
                }

                // Clear search button
                if (clearSearch) {
                    clearSearch.addEventListener('click', () => {
                        if (soldierSearch) {
                            soldierSearch.value = '';
                            this.currentFilters.search = '';
                            clearSearch.classList.add('hidden');
                            this.resetAndLoad();
                        }
                    });
                }

                // Rank filter
                document.getElementById('soldier-rank-filter')?.addEventListener('change', (e) => {
                    this.currentFilters.rank_id = e.target.value;
                    this.resetAndLoad();
                });

                // Reset filters button
                document.getElementById('reset-filters')?.addEventListener('click', () => {
                    this.resetFilters();
                });
            }

            resetFilters() {
                // Reset all filter values
                this.currentFilters = {
                    search: '',
                    rank_id: ''
                };

                // Reset UI elements
                const soldierSearch = document.getElementById('soldier-search');
                const rankFilter = document.getElementById('soldier-rank-filter');
                const clearSearch = document.getElementById('clear-search');

                if (soldierSearch) soldierSearch.value = '';
                if (rankFilter) rankFilter.value = '';
                if (clearSearch) clearSearch.classList.add('hidden');

                // Reload with reset filters
                this.resetAndLoad();
            }

            // Method to trigger loading when modal opens
            autoLoadOnModalOpen() {
                if (!this.initialized) {
                    this.loadSoldiers();
                }
            }

            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            resetAndLoad() {
                this.currentPage = 1;
                this.loadSoldiers();
            }

            async loadSoldiers() {
                if (this.isLoading) return;

                this.isLoading = true;
                this.showLoading(true);

                try {
                    const response = await fetch(window.loadSoldiersRoute, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            ...this.currentFilters,
                            page: this.currentPage,
                            per_page: this.perPage
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.currentSoldiers = data.soldiers;
                        this.renderSoldiers(data.soldiers);
                        this.updatePagination(data.pagination);
                        this.updateCounts(data.total, data.soldiers.length);
                        this.initialized = true;
                    } else {
                        this.showError(data.message || 'Failed to load soldiers');
                    }
                } catch (error) {
                    console.error('Error loading soldiers:', error);
                    this.showError('Failed to load soldiers. Please try again.');
                } finally {
                    this.isLoading = false;
                    this.showLoading(false);
                }
            }

            renderSoldiers(soldiers) {
                const container = document.getElementById('soldier-options-container');
                if (!container) return;

                if (soldiers.length === 0) {
                    container.innerHTML = this.getEmptyState();
                    return;
                }

                // Just render the soldiers directly without extra wrapper
                const soldierHTML = soldiers.map(soldier => this.createSoldierHTML(soldier)).join('');
                container.innerHTML = soldierHTML;

                // Bind click events
                soldiers.forEach(soldier => {
                    const element = document.getElementById(`soldier-${soldier.id}`);
                    if (element) {
                        element.addEventListener('click', () => this.selectSoldier(soldier));
                    }
                });
            }

            createSoldierHTML(soldier) {
                const hasAssignments = soldier.current_assignments && soldier.current_assignments.length > 0;
                const statusInfo = this.getStatusInfo(soldier);
                const availabilityClass = soldier.is_on_leave ? 'opacity-60' : '';

                return `
            <div id="soldier-${soldier.id}"
                class="soldier-card ${availabilityClass} group flex items-center gap-3 px-4 py-2.5 border border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 cursor-pointer transition-all duration-200 bg-white">

                <!-- Avatar -->
                <div class="relative flex-shrink-0">
                    <div class="w-10 h-10 bg-gradient-to-br ${statusInfo.avatarBg} rounded-full flex items-center justify-center ring-2 ${statusInfo.ringColor} transition-all">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    ${soldier.is_on_leave ? `
                            <div class="absolute -top-0.5 -right-0.5 w-3.5 h-3.5 bg-red-500 rounded-full border-2 border-white"></div>
                            ` : ''}
                </div>

                <!-- Soldier Info -->
                <div class="flex-1 min-w-0 flex items-center gap-3">
                    <div class="min-w-0 flex-shrink">
                        <h4 class="font-semibold text-gray-900 text-sm truncate">${soldier.full_name}</h4>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-600 flex-shrink-0">
                        <span class="font-mono font-medium bg-gray-100 px-2 py-0.5 rounded">${soldier.army_no}</span>
                        <span class="font-medium text-gray-500">${soldier.rank}</span>
                        <span class="text-gray-400">•</span>
                        <span class="text-gray-500">${soldier.company}</span>
                    </div>
                </div>

                <!-- Assignment Info with Tooltip -->
                ${hasAssignments ? this.getAssignmentBadgeWithTooltip(soldier) : ''}

                <!-- Status Badge -->
                <div class="flex-shrink-0">
                    ${statusInfo.badge}
                </div>

                <!-- Select Arrow -->
                <div class="flex-shrink-0 text-gray-400 group-hover:text-green-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        `;
            }

            getStatusInfo(soldier) {
                if (soldier.is_on_leave) {
                    return {
                        avatarBg: 'from-red-500 to-red-600',
                        ringColor: 'ring-red-200',
                        badge: `
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
                        </svg>
                        ON LEAVE
                    </span>
                `
                    };
                } else if (soldier.current_assignments && soldier.current_assignments.length > 0) {
                    return {
                        avatarBg: 'from-amber-500 to-amber-600',
                        ringColor: 'ring-amber-200',
                        badge: `
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        ${soldier.current_assignments.length} ASSIGNED
                    </span>
                `
                    };
                } else {
                    return {
                        avatarBg: 'from-green-500 to-green-600',
                        ringColor: 'ring-green-200',
                        badge: `
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        AVAILABLE
                    </span>
                `
                    };
                }
            }

            getAssignmentBadgeWithTooltip(soldier) {
                const assignments = soldier.current_assignments || [];
                const count = assignments.length;

                const assignmentItems = assignments.map(assignment => `
            <div class="flex items-start gap-2 mb-2 last:mb-0">
                <div class="w-2 h-2 ${this.getAssignmentColor(assignment.type)} rounded-full mt-1.5 flex-shrink-0"></div>
                <div class="flex-1 min-w-0">
                    <div class="text-white text-xs font-medium leading-tight">${assignment.name || 'Unknown Duty'}</div>
                    <div class="text-gray-300 text-xs leading-tight flex items-center gap-1 mt-0.5">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        ${this.getAssignmentTime(assignment)}
                    </div>
                </div>
            </div>
        `).join('');

                return `
            <div class="relative group/tooltip flex-shrink-0">
                <div class="flex items-center gap-1.5 px-2 py-1 bg-amber-50 border border-amber-200 rounded-md cursor-help">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs font-semibold text-amber-700">${count} Duty</span>
                </div>

                <!-- Tooltip -->
                <div class="absolute bottom-full right-0 mb-2 hidden group-hover/tooltip:block z-50 w-72">
                    <div class="bg-gray-900 text-white text-xs rounded-lg py-3 px-3 shadow-2xl border border-gray-700">
                        <div class="font-bold mb-2 text-sm text-white pb-2 border-b border-gray-700">Current Assignments (${count})</div>
                        <div class="space-y-0 max-h-48 overflow-y-auto custom-scrollbar-tooltip">
                            ${assignmentItems}
                        </div>
                    </div>
                    <div class="absolute top-full right-4 border-8 border-transparent border-t-gray-900"></div>
                </div>
            </div>
        `;
            }

            getAssignmentColor(type) {
                const colors = {
                    'fixed_duty': 'bg-green-400',
                    'course': 'bg-blue-400',
                    'cadre': 'bg-purple-400',
                    'service': 'bg-yellow-400'
                };
                return colors[type] || 'bg-gray-400';
            }

            getAssignmentTime(assignment) {
                // If schedule field exists, use it directly
                if (assignment.schedule) {
                    return assignment.schedule;
                }

                // Fallback for other assignment types
                switch (assignment.type) {
                    case 'fixed_duty':
                        if (!assignment.start_time || !assignment.end_time) return 'Time not set';
                        const start = assignment.start_time.substring(0, 5);
                        const end = assignment.end_time.substring(0, 5);
                        const duration = assignment.duration_days > 1 ? ` (${assignment.duration_days}d)` : '';
                        return `${start} - ${end}${duration}`;

                    case 'course':
                    case 'cadre':
                    case 'service':
                        const startDate = assignment.start_date ? new Date(assignment.start_date).toLocaleDateString(
                            'en-GB', {
                                day: '2-digit',
                                month: 'short'
                            }) : 'N/A';
                        const endDate = assignment.end_date ? new Date(assignment.end_date).toLocaleDateString(
                            'en-GB', {
                                day: '2-digit',
                                month: 'short'
                            }) : 'Ongoing';
                        return `${startDate} - ${endDate}`;

                    default:
                        return 'Ongoing';
                }
            }

            updatePagination(pagination) {
                this.currentPage = pagination?.current_page || 1;
                this.totalPages = pagination?.total_pages || 1;

                const paginationContainer = document.getElementById('soldier-pagination');
                if (!paginationContainer) return;

                if (this.totalPages <= 1) {
                    paginationContainer.classList.add('hidden');
                    return;
                }

                paginationContainer.classList.remove('hidden');
                paginationContainer.innerHTML = this.createPaginationHTML();

                // Bind pagination events
                this.bindPaginationEvents();
            }

            createPaginationHTML() {
                const maxVisible = 7; // Show max 7 page numbers
                let pages = [];

                if (this.totalPages <= maxVisible) {
                    // Show all pages
                    pages = Array.from({
                        length: this.totalPages
                    }, (_, i) => i + 1);
                } else {
                    // Show first, last, current and surrounding pages
                    if (this.currentPage <= 4) {
                        pages = [1, 2, 3, 4, 5, '...', this.totalPages];
                    } else if (this.currentPage >= this.totalPages - 3) {
                        pages = [1, '...', this.totalPages - 4, this.totalPages - 3, this.totalPages - 2, this
                            .totalPages - 1, this.totalPages
                        ];
                    } else {
                        pages = [1, '...', this.currentPage - 1, this.currentPage, this.currentPage + 1, '...', this
                            .totalPages
                        ];
                    }
                }

                const pageButtons = pages.map(page => {
                    if (page === '...') {
                        return `<span class="px-3 py-2 text-gray-400">...</span>`;
                    }

                    const isActive = page === this.currentPage;
                    return `
                <button type="button"
                    class="page-btn px-4 py-2 rounded-lg font-medium transition-all ${
                        isActive
                            ? 'bg-green-600 text-white shadow-lg shadow-green-200'
                            : 'bg-white text-gray-700 hover:bg-green-50 hover:text-green-700 border border-gray-200'
                    }"
                    data-page="${page}"
                    ${isActive ? 'disabled' : ''}>
                    ${page}
                </button>
            `;
                }).join('');

                return `
            <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-gray-50 to-green-50 rounded-xl border border-gray-200">
                <button type="button"
                    id="first-page-btn"
                    class="nav-btn px-4 py-2 rounded-lg font-medium bg-white text-gray-700 hover:bg-green-50 hover:text-green-700 border border-gray-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    ${this.currentPage === 1 ? 'disabled' : ''}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>

                <button type="button"
                    id="prev-page-btn"
                    class="nav-btn px-4 py-2 rounded-lg font-medium bg-white text-gray-700 hover:bg-green-50 hover:text-green-700 border border-gray-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    ${this.currentPage === 1 ? 'disabled' : ''}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <div class="flex items-center gap-1">
                    ${pageButtons}
                </div>

                <button type="button"
                    id="next-page-btn"
                    class="nav-btn px-4 py-2 rounded-lg font-medium bg-white text-gray-700 hover:bg-green-50 hover:text-green-700 border border-gray-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    ${this.currentPage === this.totalPages ? 'disabled' : ''}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                <button type="button"
                    id="last-page-btn"
                    class="nav-btn px-4 py-2 rounded-lg font-medium bg-white text-gray-700 hover:bg-green-50 hover:text-green-700 border border-gray-200 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    ${this.currentPage === this.totalPages ? 'disabled' : ''}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        `;
            }

            bindPaginationEvents() {
                // Page number buttons
                document.querySelectorAll('.page-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const page = parseInt(btn.dataset.page);
                        if (!isNaN(page)) {
                            this.currentPage = page;
                            this.loadSoldiers();
                        }
                    });
                });

                // Navigation buttons
                document.getElementById('first-page-btn')?.addEventListener('click', () => {
                    this.currentPage = 1;
                    this.loadSoldiers();
                });

                document.getElementById('prev-page-btn')?.addEventListener('click', () => {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                        this.loadSoldiers();
                    }
                });

                document.getElementById('next-page-btn')?.addEventListener('click', () => {
                    if (this.currentPage < this.totalPages) {
                        this.currentPage++;
                        this.loadSoldiers();
                    }
                });

                document.getElementById('last-page-btn')?.addEventListener('click', () => {
                    this.currentPage = this.totalPages;
                    this.loadSoldiers();
                });
            }

            updateCounts(total, shown) {
                const totalEl = document.getElementById('soldier-count');
                const filteredEl = document.getElementById('filtered-soldier-count');

                if (totalEl) totalEl.textContent = total;
                if (filteredEl) filteredEl.textContent = shown;
            }

            showLoading(show) {
                const loadingEl = document.getElementById('soldier-loading');
                const container = document.getElementById('soldier-options-container');

                if (loadingEl) loadingEl.classList.toggle('hidden', !show);
                if (container && show) {
                    container.innerHTML = `
                <div class="flex items-center justify-center py-16">
                    <div class="text-center">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-green-200 border-t-green-600 mb-4"></div>
                        <p class="text-gray-600 font-medium">Loading soldiers...</p>
                    </div>
                </div>
            `;
                }
            }

            showError(message) {
                const container = document.getElementById('soldier-options-container');
                if (container) {
                    container.innerHTML = `
                <div class="text-center py-12 text-rose-600">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <h4 class="text-lg font-medium mb-2">Error Loading Soldiers</h4>
                    <p class="text-sm max-w-md mx-auto mb-4">${message}</p>
                    <button onclick="window.soldierLoader.loadSoldiers()" type="button"
                        class="px-6 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors duration-200">
                        Retry
                    </button>
                </div>
            `;
                }
            }

            getEmptyState() {
                return `
            <div class="text-center py-16 text-gray-400">
                <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                <h4 class="text-lg font-medium text-gray-500 mb-2">No Soldiers Found</h4>
                <p class="text-sm text-gray-400 max-w-md mx-auto mb-4">
                    No soldiers match your current filters. Try changing your search criteria.
                </p>
                <button onclick="window.soldierLoader.resetAndLoad()" type="button"
                    class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 font-medium">
                    Reset Filters
                </button>
            </div>
        `;
            }

            selectSoldier(soldier) {
                if (window.dutyForm) {
                    window.dutyForm.selectSoldier(soldier);
                }
            }
        }

        // Add custom scrollbar styles
        const style = document.createElement('style');
        style.textContent = `
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #10b981;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #059669;
    }

    .custom-scrollbar-tooltip::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar-tooltip::-webkit-scrollbar-track {
        background: #374151;
        border-radius: 2px;
    }
    .custom-scrollbar-tooltip::-webkit-scrollbar-thumb {
        background: #6b7280;
        border-radius: 2px;
    }
    .custom-scrollbar-tooltip::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    .soldier-card {
        animation: fadeIn 0.2s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Hover effect for soldier cards */
    .soldier-card:hover {
        transform: translateX(4px);
    }

    /* Tooltip arrow styling */
    .group\/tooltip:hover .group-hover\/tooltip\:block {
        animation: tooltipFadeIn 0.2s ease-out;
    }

    @keyframes tooltipFadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
        document.head.appendChild(style);

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.soldierLoader = new SoldierLoader();

            // Update DutyForm's openSoldierSelectionModal method
            if (window.dutyForm) {
                const originalOpenModal = window.dutyForm.openSoldierSelectionModal;

                window.dutyForm.openSoldierSelectionModal = function() {
                    if (originalOpenModal) {
                        originalOpenModal.call(this);
                    }

                    // Auto-load soldiers when modal opens
                    if (window.soldierLoader) {
                        window.soldierLoader.autoLoadOnModalOpen();
                    }
                };
            }
        });
    </script>
@endpush
