@extends('mpm.layouts.app')

@section('title', 'Course/Cadre/Ex-Area Create Manager')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
        <div class="container mx-auto px-4 max-w-6xl">
            <x-breadcrumb :breadcrumbs="generateBreadcrumbs_auto()" />
            @include('mpm.components.alerts')

            <!-- Main Form Card -->
            <div class="bg-white/80 backdrop-blur-sm shadow-2xl rounded-3xl border border-white/50 overflow-hidden">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-purple-600 p-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                        <div class="w-2 h-2 bg-white/70 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                        <div class="w-2 h-2 bg-white/50 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                        <h2 class="text-white text-xl font-semibold ml-4">Create Course/Cadre/Ex-Area Assignment</h2>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-8">
                    <form action="{{ route('coursecadremanager.store') }}" method="POST" class="space-y-8"
                        id="assignmentForm">
                        @csrf

                        <!-- Assignment Type Selection -->
                        <div class="group">
                            <label for="type" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-emerald-400 to-cyan-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <span class="text-white font-bold text-sm">1</span>
                                </div>
                                Assignment Type
                            </label>
                            <select name="type" id="type" required
                                class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 text-gray-900 font-medium hover:border-gray-300">
                                <option value="" class="text-gray-500">Choose assignment type...</option>
                                <option value="course" class="text-gray-900"
                                    {{ old('type') == 'course' ? 'selected' : '' }}>📚 Course Assignment</option>
                                <option value="cadre" class="text-gray-900" {{ old('type') == 'cadre' ? 'selected' : '' }}>
                                    👥 Cadre Assignment</option>
                                <option value="ex_area" class="text-gray-900"
                                    {{ old('type') == 'ex_area' ? 'selected' : '' }}>🗺️ Ex-Area Assignment</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date Selection -->
                        <div class="group">
                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-amber-400 to-orange-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <span class="text-white font-bold text-sm">2</span>
                                </div>
                                Assignment Duration
                            </label>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Start Date -->
                                <div class="relative">
                                    <label for="start_date" class="block text-sm font-medium text-gray-600 mb-2">
                                        <svg class="inline w-4 h-4 mr-2 text-emerald-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Start Date
                                    </label>
                                    <input type="date" name="start_date" id="start_date"
                                        value="{{ old('start_date', now()->toDateString()) }}"
                                        max="{{ now()->toDateString() }}"
                                        class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300 @error('start_date') border-red-300 @enderror">
                                    @error('start_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- End Date -->
                                <div class="relative">
                                    <label for="end_date" class="block text-sm font-medium text-gray-600 mb-2">
                                        <svg class="inline w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        End Date
                                    </label>
                                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                        class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-red-500/20 focus:border-red-500 transition-all duration-300 @error('end_date') border-red-300 @enderror">
                                    @error('end_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Course Selection -->
                        <div id="courseSelection" class="hidden group">
                            <label for="course_id" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                </div>
                                Select Course
                            </label>
                            <select name="course_id" id="course_id"
                                class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 text-gray-900 font-medium @error('course_id') border-red-300 @enderror">
                                <option value="" class="text-gray-500">Choose a course...</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" class="text-gray-900"
                                        {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cadre Selection -->
                        <div id="cadreSelection" class="hidden group">
                            <label for="cadre_id" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                Select Cadre
                            </label>
                            <select name="cadre_id" id="cadre_id"
                                class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 text-gray-900 font-medium @error('cadre_id') border-red-300 @enderror">
                                <option value="" class="text-gray-500">Choose a cadre...</option>
                                @foreach ($cadres as $cadre)
                                    <option value="{{ $cadre->id }}" class="text-gray-900"
                                        {{ old('cadre_id') == $cadre->id ? 'selected' : '' }}>{{ $cadre->name }}</option>
                                @endforeach
                            </select>
                            @error('cadre_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ex-Area Selection -->
                        <div id="exAreaSelection" class="hidden group">
                            <label for="ex_area_id" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-teal-400 to-cyan-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                                        </path>
                                    </svg>
                                </div>
                                Select Ex-Area
                            </label>
                            <select name="ex_area_id" id="ex_area_id"
                                class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-teal-500/20 focus:border-teal-500 transition-all duration-300 text-gray-900 font-medium @error('ex_area_id') border-red-300 @enderror">
                                <option value="" class="text-gray-500">Choose an ex-area...</option>
                                @foreach ($exAreas as $exArea)
                                    <option value="{{ $exArea->id }}" class="text-gray-900"
                                        {{ old('ex_area_id') == $exArea->id ? 'selected' : '' }}>{{ $exArea->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ex_area_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Personnel Selection -->
                        <div class="group">
                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-teal-400 to-cyan-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <span class="text-white font-bold text-sm">3</span>
                                </div>
                                Personnel Selection
                            </label>

                            <!-- Filter Section -->
                            <div
                                class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-2xl p-6 mb-6 border border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z">
                                        </path>
                                    </svg>
                                    Filter Personnel
                                </h3>
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                    <div class="relative">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                                        <input type="text" id="filter-army-no" placeholder="Army No or Name..."
                                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 text-sm">
                                    </div>

                                    <div class="relative">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Rank</label>
                                        <select id="filter-rank"
                                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 text-sm">
                                            <option value="">All Ranks</option>
                                            @foreach ($ranks as $rank)
                                                <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="relative">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Company</label>
                                        <select id="filter-company"
                                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 text-sm">
                                            <option value="">All Companies</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Available Soldiers Section -->
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Available Personnel ({{ $availableSoldiers->count() }})
                                </h3>

                                <div class="relative">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-r from-green-400/10 to-emerald-400/10 rounded-2xl">
                                    </div>
                                    <div id="available-soldier-repo"
                                        class="relative bg-white/90 backdrop-blur-sm border-2 border-gray-100 rounded-2xl p-6 h-80 overflow-y-auto shadow-inner">
                                        @if ($availableSoldiers->count() > 0)
                                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                                @foreach ($availableSoldiers as $soldier)
                                                    <label
                                                        class="group relative flex items-start space-x-3 p-4 bg-white rounded-xl border border-gray-100 hover:border-green-300 hover:shadow-lg transition-all duration-300 cursor-pointer hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50">
                                                        <input type="checkbox" name="soldier_ids[]"
                                                            value="{{ $soldier->id }}"
                                                            data-rank-id="{{ $soldier->rank_id }}"
                                                            data-company-id="{{ $soldier->company_id }}"
                                                            data-army-no="{{ strtolower(str_replace(' ', '', $soldier->army_no ?? '')) }}"
                                                            data-full-name="{{ strtolower($soldier->full_name ?? '') }}"
                                                            data-completed-today="{{ $soldier->hasCompletedAssignmentsToday() ? '1' : '0' }}"
                                                            class="form-checkbox h-5 w-5 text-green-600 rounded-lg border-2 border-gray-300 focus:ring-green-500/50 focus:ring-2 transition-all duration-200 mt-0.5 @error('soldier_ids') border-red-300 @enderror"
                                                            {{ in_array($soldier->id, old('soldier_ids', [])) ? 'checked' : '' }}>

                                                        <div class="flex-1 min-w-0">
                                                            <div class="flex items-center space-x-2 mb-1">
                                                                <div
                                                                    class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                                    <span
                                                                        class="text-white text-xs font-bold">{{ substr($soldier->full_name, 0, 1) }}</span>
                                                                </div>
                                                                <p
                                                                    class="text-sm font-semibold text-gray-900 truncate group-hover:text-green-700 transition-colors">
                                                                    {{ $soldier->full_name }}</p>

                                                                @if ($soldier->hasCompletedAssignmentsToday())
                                                                    <span
                                                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                                        <svg class="w-3 h-3 mr-1" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                            </path>
                                                                        </svg>
                                                                        Completed Today
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="space-y-1">
                                                                <p
                                                                    class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded-md inline-block">
                                                                    🆔
                                                                    {{ $soldier->army_no }}</p>
                                                                <div class="flex flex-wrap gap-1">
                                                                    <span
                                                                        class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">{{ $soldier->rank->name }}</span>
                                                                    <span
                                                                        class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">{{ $soldier->company->name }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center justify-center h-full text-gray-500">
                                                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium">No available personnel</p>
                                                <p class="text-sm">All personnel are currently assigned</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @error('soldier_ids')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Assigned Personnel Section -->
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    Currently Assigned Personnel ({{ $assignedSoldiers->count() }})
                                </h3>
                                <div class="relative">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-r from-amber-400/10 to-orange-400/10 rounded-2xl">
                                    </div>
                                    <div id="assigned-soldier-repo"
                                        class="relative bg-white/90 backdrop-blur-sm border-2 border-gray-100 rounded-2xl p-6 h-80 overflow-y-auto shadow-inner">
                                        @if ($assignedSoldiers->count() > 0)
                                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                                @foreach ($assignedSoldiers as $soldier)
                                                    <div
                                                        class="group relative flex flex-col p-4 bg-white rounded-xl border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all duration-200 opacity-75 hover:opacity-100">

                                                        <!-- Header with Cross Icon and Soldier Info -->
                                                        <div class="flex items-start space-x-3 mb-3">
                                                            <div class="w-5 h-5 mt-0.5 flex items-center justify-center">
                                                                <svg class="w-4 h-4 text-gray-400" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                                                    </path>
                                                                </svg>
                                                            </div>

                                                            <div class="flex-1 min-w-0">
                                                                <div class="flex items-center space-x-2 mb-1">
                                                                    <div
                                                                        class="w-8 h-8 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center flex-shrink-0">
                                                                        <span
                                                                            class="text-white text-xs font-bold">{{ substr($soldier->full_name, 0, 1) }}</span>
                                                                    </div>
                                                                    <p
                                                                        class="text-sm font-semibold text-gray-700 truncate">
                                                                        {{ $soldier->full_name }}</p>
                                                                </div>
                                                                <div class="space-y-1">
                                                                    <p
                                                                        class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded-md inline-block">
                                                                        🆔 {{ $soldier->army_no }}</p>
                                                                    <div class="flex flex-wrap gap-1">
                                                                        <span
                                                                            class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">{{ $soldier->rank->name }}</span>
                                                                        <span
                                                                            class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">{{ $soldier->company->name }}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Assignment Badges with Improved Design -->
                                                        <div class="space-y-3">
                                                            @if ($soldier->activeCourses()->exists())
                                                                <div class="assignment-section">
                                                                    <div class="flex items-center mb-2">
                                                                        <div
                                                                            class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-2">
                                                                            <svg class="w-3 h-3 text-white" fill="none"
                                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                                                </path>
                                                                            </svg>
                                                                        </div>
                                                                        <span
                                                                            class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Active
                                                                            Courses</span>
                                                                    </div>
                                                                    <div class="space-y-1 ml-8">
                                                                        @foreach ($soldier->activeCourses as $course)
                                                                            <div
                                                                                class="flex items-center p-2 bg-blue-50 rounded-lg border-l-4 border-blue-400 hover:bg-blue-100 transition-colors duration-150">
                                                                                <div class="flex-1">
                                                                                    <div
                                                                                        class="text-xs font-medium text-blue-800">
                                                                                        {{ $course->name }}</div>
                                                                                    <div
                                                                                        class="text-xs text-blue-600 mt-0.5">
                                                                                        Course Assignment
                                                                                    </div>
                                                                                </div>
                                                                                <div
                                                                                    class="w-2 h-2 bg-blue-400 rounded-full">
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            @if ($soldier->activeCadres()->exists())
                                                                <div class="assignment-section">
                                                                    <div class="flex items-center mb-2">
                                                                        <div
                                                                            class="w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center mr-2">
                                                                            <svg class="w-3 h-3 text-white" fill="none"
                                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                                                </path>
                                                                            </svg>
                                                                        </div>
                                                                        <span
                                                                            class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Cadre
                                                                            Positions</span>
                                                                    </div>
                                                                    <div class="space-y-1 ml-8">
                                                                        @foreach ($soldier->activeCadres as $cadre)
                                                                            <div
                                                                                class="flex items-center p-2 bg-purple-50 rounded-lg border-l-4 border-purple-400 hover:bg-purple-100 transition-colors duration-150">
                                                                                <div class="flex-1">
                                                                                    <div
                                                                                        class="text-xs font-medium text-purple-800">
                                                                                        {{ $cadre->name }}</div>
                                                                                    <div
                                                                                        class="text-xs text-purple-600 mt-0.5">
                                                                                        Cadre Role</div>
                                                                                </div>
                                                                                <div
                                                                                    class="w-2 h-2 bg-purple-400 rounded-full">
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            @if ($soldier->activeExAreas && $soldier->activeExAreas->count() > 0)
                                                                <div class="assignment-section">
                                                                    <div class="flex items-center mb-2">
                                                                        <div
                                                                            class="w-6 h-6 bg-teal-500 rounded-full flex items-center justify-center mr-2">
                                                                            <svg class="w-3 h-3 text-white" fill="none"
                                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7">
                                                                                </path>
                                                                            </svg>
                                                                        </div>
                                                                        <span
                                                                            class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Ex-Areas</span>
                                                                    </div>
                                                                    <div class="space-y-1 ml-8">
                                                                        @foreach ($soldier->activeExAreas as $exArea)
                                                                            <div
                                                                                class="flex items-center p-2 bg-teal-50 rounded-lg border-l-4 border-teal-400 hover:bg-teal-100 transition-colors duration-150">
                                                                                <div class="flex-1">
                                                                                    <div
                                                                                        class="text-xs font-medium text-teal-800">
                                                                                        {{ $exArea->name }}</div>
                                                                                    <div
                                                                                        class="text-xs text-teal-600 mt-0.5">
                                                                                        Ex-Area Assignment</div>
                                                                                </div>
                                                                                <div
                                                                                    class="w-2 h-2 bg-teal-400 rounded-full">
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            @if ($soldier->activeServices()->exists())
                                                                <div class="assignment-section">
                                                                    <div class="flex items-center mb-2">
                                                                        <div
                                                                            class="w-6 h-6 bg-indigo-500 rounded-full flex items-center justify-center mr-2">
                                                                            <svg class="w-3 h-3 text-white" fill="none"
                                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V6m8 0V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2">
                                                                                </path>
                                                                            </svg>
                                                                        </div>
                                                                        <span
                                                                            class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Appointments</span>
                                                                    </div>
                                                                    <div class="space-y-1 ml-8">
                                                                        @foreach ($soldier->activeServices as $service)
                                                                            <div
                                                                                class="flex items-center p-2 bg-indigo-50 rounded-lg border-l-4 border-indigo-400 hover:bg-indigo-100 transition-colors duration-150">
                                                                                <div class="flex-1">
                                                                                    <div
                                                                                        class="text-xs font-medium text-indigo-800">
                                                                                        {{ $service->appointments_name }}
                                                                                    </div>
                                                                                    <div
                                                                                        class="text-xs text-indigo-600 mt-0.5">
                                                                                        Service Appointment
                                                                                    </div>
                                                                                </div>
                                                                                <div
                                                                                    class="w-2 h-2 bg-indigo-400 rounded-full">
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center justify-center h-full text-gray-500">
                                                <svg class="w-16 h-16 mb-4 text-green-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <p class="text-lg font-medium">All personnel are available</p>
                                                <p class="text-sm">No active assignments found</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="group">
                            <label for="note" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-rose-400 to-pink-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </div>
                                Additional Notes
                            </label>
                            <div class="relative">
                                <textarea name="note" id="note" rows="4"
                                    class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-rose-500/20 focus:border-rose-500 transition-all duration-300 resize-none @error('note') border-red-300 @enderror"
                                    placeholder="✍️ Add any additional notes or special instructions here...">{{ old('note') }}</textarea>
                                <div class="absolute bottom-3 right-3 text-xs text-gray-400">Optional</div>
                            </div>
                            @error('note')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="border-t border-gray-200 pt-6">
                            <div
                                class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                                <div class="text-sm text-gray-500">
                                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Please review all details before submitting
                                </div>
                                <button type="submit"
                                    class="inline-flex items-center px-8 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold shadow-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 transform hover:scale-105 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Save information
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Soldier filtering
            const armyInput = document.getElementById("filter-army-no");
            const rankSelect = document.getElementById("filter-rank");
            const companySelect = document.getElementById("filter-company");
            const availableRepo = document.getElementById("available-soldier-repo");
            const assignedRepo = document.getElementById("assigned-soldier-repo");
            const availableSoldierCards = Array.from(availableRepo.querySelectorAll("label"));
            const assignedSoldierCards = Array.from(assignedRepo.querySelectorAll("div.group"));

            function normalize(str = '') {
                return String(str).toLowerCase().replace(/\s+/g, '');
            }

            function filter() {
                const armyRaw = armyInput.value.trim();
                const army = normalize(armyRaw);
                const rank = rankSelect.value;
                const company = companySelect.value;

                // Filter available soldiers
                availableSoldierCards.forEach(card => {
                    const checkbox = card.querySelector('input[type="checkbox"]');
                    const cardArmy = normalize(checkbox.dataset.armyNo || '');
                    const cardName = normalize(checkbox.dataset.fullName || '');
                    const cardRank = String(checkbox.dataset.rankId || '');
                    const cardCompany = String(checkbox.dataset.companyId || '');

                    const matchesArmy = !army || cardArmy.includes(army) || cardName.includes(army);
                    const matchesRank = !rank || cardRank === rank;
                    const matchesCompany = !company || cardCompany === company;

                    card.style.display = (matchesArmy && matchesRank && matchesCompany) ? "flex" : "none";
                });

                // Filter assigned soldiers
                assignedSoldierCards.forEach(card => {
                    // We need to add data attributes to assigned soldiers too
                    // For now, we'll just show all assigned soldiers
                    // In a real implementation, you'd add data attributes to these cards too
                });
            }

            // wire up events
            armyInput.addEventListener("input", filter);
            rankSelect.addEventListener("change", filter);
            companySelect.addEventListener("change", filter);

            // run once on load to apply any defaults
            filter();

            // Dynamic course/cadre/ex-area selection
            const typeSelect = document.getElementById('type');
            const courseSelection = document.getElementById('courseSelection');
            const cadreSelection = document.getElementById('cadreSelection');
            const exAreaSelection = document.getElementById('exAreaSelection');
            const courseIdSelect = document.getElementById('course_id');
            const cadreIdSelect = document.getElementById('cadre_id');
            const exAreaIdSelect = document.getElementById('ex_area_id');
            const assignmentForm = document.getElementById('assignmentForm');

            // Function to handle type selection
            function handleTypeSelection() {
                const type = typeSelect.value;

                // Hide all selections
                courseSelection.classList.add('hidden');
                cadreSelection.classList.add('hidden');
                exAreaSelection.classList.add('hidden');

                // Remove all fields from form submission
                courseIdSelect.removeAttribute('required');
                cadreIdSelect.removeAttribute('required');
                exAreaIdSelect.removeAttribute('required');
                courseIdSelect.setAttribute('disabled', 'disabled');
                cadreIdSelect.setAttribute('disabled', 'disabled');
                exAreaIdSelect.setAttribute('disabled', 'disabled');

                // Show the appropriate selection based on type
                if (type === 'course') {
                    courseSelection.classList.remove('hidden');
                    courseIdSelect.setAttribute('required', 'required');
                    courseIdSelect.removeAttribute('disabled');
                    // Clear other values
                    cadreIdSelect.value = '';
                    exAreaIdSelect.value = '';
                } else if (type === 'cadre') {
                    cadreSelection.classList.remove('hidden');
                    cadreIdSelect.setAttribute('required', 'required');
                    cadreIdSelect.removeAttribute('disabled');
                    // Clear other values
                    courseIdSelect.value = '';
                    exAreaIdSelect.value = '';
                } else if (type === 'ex_area') {
                    exAreaSelection.classList.remove('hidden');
                    exAreaIdSelect.setAttribute('required', 'required');
                    exAreaIdSelect.removeAttribute('disabled');
                    // Clear other values
                    courseIdSelect.value = '';
                    cadreIdSelect.value = '';
                }
            }

            // Set initial state based on old input or default
            const oldType = '{{ old('type') }}';
            if (oldType) {
                typeSelect.value = oldType;
                handleTypeSelection();
            }

            typeSelect.addEventListener('change', handleTypeSelection);

            // Form submission handler to ensure proper validation
            assignmentForm.addEventListener('submit', function(e) {
                const type = typeSelect.value;

                if (!type) {
                    e.preventDefault();
                    alert('Please select an assignment type.');
                    return;
                }

                if (type === 'course' && !courseIdSelect.value) {
                    e.preventDefault();
                    alert('Please select a course.');
                    return;
                }

                if (type === 'cadre' && !cadreIdSelect.value) {
                    e.preventDefault();
                    alert('Please select a cadre.');
                    return;
                }

                if (type === 'ex_area' && !exAreaIdSelect.value) {
                    e.preventDefault();
                    alert('Please select an ex-area.');
                    return;
                }

                // Check if at least one soldier is selected
                const selectedSoldiers = document.querySelectorAll('input[name="soldier_ids[]"]:checked');
                if (selectedSoldiers.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one soldier.');
                    return;
                }
            });

            // Handle date adjustment for soldiers who completed assignments today
            const soldierCheckboxes = document.querySelectorAll('input[name="soldier_ids[]"]');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const dateAdjustmentWarning = document.createElement('div');
            dateAdjustmentWarning.className = 'mt-2 p-3 bg-amber-50 rounded-lg border border-amber-200 hidden';
            dateAdjustmentWarning.innerHTML = `
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <p class="text-sm text-amber-700">
                        The start date will be adjusted to tomorrow for soldiers who completed assignments today.
                    </p>
                </div>
            `;

            // Insert the warning after the date inputs
            const dateInputsContainer = startDateInput.closest('.grid');
            dateInputsContainer.parentNode.insertBefore(dateAdjustmentWarning, dateInputsContainer.nextSibling);

            // Check if any selected soldiers have completed assignments today
            function checkForCompletedToday() {
                const selectedSoldiers = document.querySelectorAll('input[name="soldier_ids[]"]:checked');
                let hasCompletedToday = false;

                selectedSoldiers.forEach(checkbox => {
                    if (checkbox.dataset.completedToday === '1') {
                        hasCompletedToday = true;
                    }
                });

                if (hasCompletedToday && startDateInput.value === new Date().toISOString().split('T')[0]) {
                    dateAdjustmentWarning.classList.remove('hidden');
                } else {
                    dateAdjustmentWarning.classList.add('hidden');
                }
            }

            // Add event listeners to all soldier checkboxes
            soldierCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', checkForCompletedToday);
            });

            // Add event listener to start date input
            startDateInput.addEventListener('change', checkForCompletedToday);

            // Initial check
            checkForCompletedToday();
        });
    </script>
@endsection
