@extends('mpm.layouts.app')

@section('title', 'appointments Manager')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8">
        <div class="container mx-auto px-4 max-w-7xl">
            <x-breadcrumb :breadcrumbs="generateBreadcrumbs_auto()" />
            @include('mpm.components.alerts')

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Form Card -->
            <div class="bg-white/70 backdrop-blur-sm shadow-2xl rounded-2xl border border-white/20 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
                    <h2 class="text-2xl font-semibold text-white">Create New Appointment</h2>
                    <p class="text-indigo-100 mt-1">Fill in the details below to assign soldiers to appointments</p>
                </div>

                <form action="{{ route('appointmanager.store') }}" method="POST" class="p-8 space-y-8">
                    @csrf

                    <!-- Appointment & Date Selection -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Appointment Dropdown -->
                        <div class="lg:col-span-1">
                            <label for="appointment_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="inline w-4 h-4 mr-1 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                Select Appointment
                            </label>
                            <select name="appointment_id" id="appointment_id"
                                class="w-full rounded-xl border-2 border-gray-200 bg-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 px-4 py-3 text-sm">
                                <option value="">-- Choose Appointment --</option>
                                @foreach ($appointments as $appointment)
                                    <option value="{{ $appointment->id }}">{{ $appointment->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div class="lg:col-span-1">
                            <label for="appointments_from_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="inline w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Start Date
                            </label>
                            <input type="date" name="appointments_from_date" id="appointments_from_date"
                                value="{{ old('appointments_from_date', now()->toDateString()) }}"
                                class="w-full rounded-xl border-2 border-gray-200 bg-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 px-4 py-3 text-sm"
                                required>
                        </div>

                        <!-- End Date -->
                        <div class="lg:col-span-1">
                            <label for="appointments_to_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                <svg class="inline w-4 h-4 mr-1 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                End Date <span class="text-gray-400 text-xs">(Optional)</span>
                            </label>
                            <input type="date" name="appointments_to_date" id="appointments_to_date"
                                value="{{ old('appointments_to_date') }}"
                                class="w-full rounded-xl border-2 border-gray-200 bg-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 px-4 py-3 text-sm">
                            <p class="mt-2 text-xs text-gray-500 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Leave empty for indefinite appointments
                            </p>
                        </div>
                    </div>

                    <!-- Soldiers Selection Section -->
                    <div class="bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl p-6 border-2 border-gray-100">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Select Personnel</h3>
                        </div>

                        <!-- Filter Section -->
                        <div class="bg-white rounded-xl p-4 mb-6 shadow-sm border border-gray-200">
                            <div class="flex items-center mb-3">
                                <svg class="w-4 h-4 text-gray-500 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z">
                                    </path>
                                </svg>
                                <span class="text-sm font-medium text-gray-600">Filter Personnel</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <input type="text" id="filter-army-no" placeholder="🔍 Search by Army No or Name"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 px-3 py-2 text-sm placeholder-gray-400">
                                </div>

                                <div>
                                    <select id="filter-rank"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 px-3 py-2 text-sm">
                                        <option value="">🎖️ All Ranks</option>
                                        @foreach ($ranks as $rank)
                                            <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <select id="filter-company"
                                        class="w-full rounded-lg border border-gray-300 bg-gray-50 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 px-3 py-2 text-sm">
                                        <option value="">🏢 All Companies</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Soldiers Repository -->
                        <div id="soldier-repo"
                            class="bg-white rounded-xl border-2 border-dashed border-gray-200 p-4 h-80 overflow-y-auto shadow-inner">
                            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-3">
                                @foreach ($soldiers as $soldier)
                                    @php
                                        $isAssigned = in_array($soldier->id, $assignedSoldierIds ?? []);
                                    @endphp

                                    <label
                                        class="group block {{ $isAssigned ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                                        <div
                                            class="relative p-3 rounded-xl border-2 transition-all duration-200 {{ $isAssigned ? 'border-gray-200 bg-gray-50' : 'border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 group-hover:shadow-md' }}">
                                            <div class="flex items-start space-x-3">
                                                <input type="checkbox" name="soldier_ids[]" value="{{ $soldier->id }}"
                                                    data-rank-id="{{ $soldier->rank_id }}"
                                                    data-company-id="{{ $soldier->company_id }}"
                                                    data-army-no="{{ strtolower(str_replace(' ', '', $soldier->army_no ?? '')) }}"
                                                    data-full-name="{{ strtolower($soldier->full_name ?? '') }}"
                                                    class="mt-1 h-4 w-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500 transition-colors duration-200"
                                                    {{ $isAssigned ? 'disabled' : '' }}>

                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        <div
                                                            class="w-8 h-8 bg-gradient-to-r from-indigo-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                                                            {{ strtoupper(substr($soldier->full_name, 0, 2)) }}
                                                        </div>
                                                        <p
                                                            class="text-sm font-semibold {{ $isAssigned ? 'text-gray-400' : 'text-gray-900' }} truncate">
                                                            {{ $soldier->full_name }}
                                                        </p>
                                                    </div>

                                                    <div class="space-y-1">
                                                        <p
                                                            class="text-xs {{ $isAssigned ? 'text-gray-400' : 'text-gray-600' }} flex items-center">
                                                            <span
                                                                class="inline-block w-2 h-2 bg-blue-400 rounded-full mr-1"></span>
                                                            Army No: {{ $soldier->army_no }}
                                                        </p>
                                                        <p
                                                            class="text-xs {{ $isAssigned ? 'text-gray-400' : 'text-gray-600' }} flex items-center">
                                                            <span
                                                                class="inline-block w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                                                            {{ $soldier->rank->name }}
                                                        </p>
                                                        <p
                                                            class="text-xs {{ $isAssigned ? 'text-gray-400' : 'text-gray-600' }} flex items-center">
                                                            <span
                                                                class="inline-block w-2 h-2 bg-yellow-400 rounded-full mr-1"></span>
                                                            {{ $soldier->company->name }}
                                                        </p>
                                                    </div>

                                                    @if ($isAssigned)
                                                        <div class="mt-2 flex items-center">
                                                            <div
                                                                class="bg-red-100 text-red-600 px-2 py-1 rounded-full text-xs font-medium flex items-center">
                                                                <svg class="w-3 h-3 mr-1" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                                    </path>
                                                                </svg>
                                                                Currently Assigned
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div>
                        <label for="note" class="block text-sm font-semibold text-gray-700 mb-2">
                            <svg class="inline w-4 h-4 mr-1 text-yellow-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Additional Notes
                        </label>
                        <textarea name="note" id="note" rows="4"
                            class="w-full rounded-xl border-2 border-gray-200 bg-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 px-4 py-3 text-sm resize-none"
                            placeholder="Add any additional notes, special instructions, or comments about this appointment..."></textarea>
                    </div>

                    <!-- Submit Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
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
                                Save Appointment Manager
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Enhanced JS for Filtering Soldiers --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const armyInput = document.getElementById("filter-army-no");
            const rankSelect = document.getElementById("filter-rank");
            const companySelect = document.getElementById("filter-company");
            const repo = document.getElementById("soldier-repo");
            // Select the actual soldier cards (labels inside the grid)
            const soldierCards = Array.from(repo.querySelectorAll("label"));

            function normalize(str = '') {
                return String(str).toLowerCase().replace(/\s+/g, '');
            }

            function filter() {
                const armyRaw = armyInput.value.trim();
                const army = normalize(armyRaw);
                const rank = rankSelect.value; // ID as string or ""
                const company = companySelect.value; // ID as string or ""

                let visibleCount = 0;

                soldierCards.forEach(card => {
                    const checkbox = card.querySelector('input[type="checkbox"]');
                    const isAssigned = checkbox.disabled;

                    const cardArmy = normalize(checkbox.dataset.armyNo || '');
                    const cardName = normalize(checkbox.dataset.fullName || '');
                    const cardRank = String(checkbox.dataset.rankId || '');
                    const cardCompany = String(checkbox.dataset.companyId || '');

                    // Army filter matches army number or full name substring
                    const matchesArmy = !army || cardArmy.includes(army) || cardName.includes(army);

                    // Rank/company compare IDs (exact match)
                    const matchesRank = !rank || cardRank === rank;
                    const matchesCompany = !company || cardCompany === company;

                    // Apply filter to all soldiers (both assigned and unassigned)
                    if (matchesArmy && matchesRank && matchesCompany) {
                        card.style.display = "block";
                        visibleCount++;
                    } else {
                        card.style.display = "none";
                    }
                });

                // Show/hide empty state message
                updateEmptyState(visibleCount);
            }

            function updateEmptyState(visibleCount) {
                const grid = repo.querySelector('.grid');
                let emptyState = grid.querySelector('.empty-state');

                if (visibleCount === 0 && !emptyState) {
                    emptyState = document.createElement('div');
                    emptyState.className =
                        'empty-state col-span-full flex flex-col items-center justify-center py-12 text-gray-500';
                    emptyState.innerHTML = `
                <svg class="w-12 h-12 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <p class="text-lg font-medium mb-2">No personnel found</p>
                <p class="text-sm">Try adjusting your search filters</p>
            `;
                    grid.appendChild(emptyState);
                } else if (visibleCount > 0 && emptyState) {
                    emptyState.remove();
                }
            }

            // Add smooth focus effects
            [armyInput, rankSelect, companySelect].forEach(element => {
                element.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-indigo-200');
                });
                element.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-indigo-200');
                });
            });

            // Wire up events
            armyInput.addEventListener("input", filter);
            rankSelect.addEventListener("change", filter);
            companySelect.addEventListener("change", filter);

            // Run once on load to apply any defaults
            filter();
        });
    </script>

@endsection
