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
                                    <div>
                                        <label for="start-time"
                                            class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
                                            Daily Start Time
                                        </label>
                                        <div class="relative">
                                            <select id="start-time" name="start_time"
                                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 appearance-none @error('start_time') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                                required>
                                                <option value="">Select Start Time</option>
                                                @for ($h = 0; $h < 24; $h++)
                                                    @for ($m = 0; $m < 60; $m += 30)
                                                        @php
                                                            $time = sprintf('%02d:%02d', $h, $m);
                                                            $selected =
                                                                old('start_time', '08:00') == $time ? 'selected' : '';
                                                        @endphp
                                                        <option value="{{ $time }}" {{ $selected }}>
                                                            {{ $time }}</option>
                                                    @endfor
                                                @endfor
                                            </select>
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
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
                                    <div>
                                        <label for="end-time"
                                            class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
                                            Daily End Time
                                        </label>
                                        <div class="relative">
                                            <select id="end-time" name="end_time"
                                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 appearance-none @error('end_time') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                                required>
                                                <option value="">Select End Time</option>
                                                @for ($h = 0; $h < 24; $h++)
                                                    @for ($m = 0; $m < 60; $m += 30)
                                                        @php
                                                            $time = sprintf('%02d:%02d', $h, $m);
                                                            $selected =
                                                                old('end_time', '17:00') == $time ? 'selected' : '';
                                                        @endphp
                                                        <option value="{{ $time }}" {{ $selected }}>
                                                            {{ $time }}</option>
                                                    @endfor
                                                @endfor
                                            </select>
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
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
    <script>
        // Initialize available soldiers data for create view
        window.availableSoldiers = @json($availableSoldiers ?? []);
        window.initialIndividualRanks = {};
        window.initialRankGroups = [];
        window.initialFixedSoldiers = {};

        console.log('Available Soldiers Data:', @json($availableSoldiers ?? []));
        console.log('Available Soldiers Count:', {{ count($availableSoldiers ?? []) }});
        console.log('Initial Individual Ranks:', window.initialIndividualRanks);
        console.log('Initial Rank Groups:', window.initialRankGroups);
        console.log('Initial Fixed Soldiers:', window.initialFixedSoldiers);
    </script>
    <script src="{{ asset('asset/js/duty-form.js') }}"></script>
@endpush
