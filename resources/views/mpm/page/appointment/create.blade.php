@extends('mpm.layouts.app')

@section('title', 'appointments Manager')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs_auto()" />
        @include('mpm.components.alerts')

        <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Military Appointment Management</h1>
                <p class="text-gray-600">Manage soldier appointments and view all appointed personnel</p>
            </div>

            <form action="{{ route('appointmanager.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- 1. Appointment Dropdown --}}
                <div>
                    <label for="appointment_id" class="block text-sm font-medium text-gray-700">Select Appointment</label>
                    <select name="appointment_id" id="appointment_id"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Select Appointment --</option>
                        @foreach ($appointments as $appointment)
                            <option value="{{ $appointment->id }}">{{ $appointment->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 2. Soldiers Repository with Filters --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Soldiers</label>

                    {{-- Filter Section --}}
                    <div class="flex flex-col sm:flex-row sm:space-x-4 mb-4">
                        <input type="text" id="filter-army-no" placeholder="Search by Army No"
                            class="w-full sm:w-1/3 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm mb-2 sm:mb-0">

                        <select id="filter-rank"
                            class="w-full sm:w-1/3 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm mb-2 sm:mb-0">
                            <option value="">All Ranks</option>
                            @foreach ($ranks as $rank)
                                <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                            @endforeach
                        </select>

                        <select id="filter-company"
                            class="w-full sm:w-1/3 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Companies</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Soldiers List --}}
                    <div id="soldier-repo"
                        class="border rounded-lg p-3 h-64 overflow-y-auto bg-white shadow-inner grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach ($soldiers as $soldier)
                            <label
                                class="flex items-center space-x-2 p-2 rounded-lg border hover:bg-indigo-50 cursor-pointer">
                                <input type="checkbox" name="soldier_ids[]" value="{{ $soldier->id }}"
                                    data-rank-id="{{ $soldier->rank_id }}" data-company-id="{{ $soldier->company_id }}"
                                    data-army-no="{{ strtolower(str_replace(' ', '', $soldier->army_no ?? '')) }}"
                                    data-full-name="{{ strtolower($soldier->full_name ?? '') }}"
                                    class="form-checkbox h-5 w-5 text-indigo-600">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $soldier->full_name }}</p>
                                    <p class="text-xs text-gray-500">Army No: {{ $soldier->army_no }} |
                                        {{ $soldier->rank->name }} | {{ $soldier->company->name }}</p>
                                </div>
                            </label>
                        @endforeach

                    </div>
                </div>

                {{-- 3. Text Field --}}
                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="note" id="note" rows="3"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Write additional notes here..."></textarea>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 shadow">
                        Save Appointment Manager
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JS for Filtering Soldiers --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const armyInput = document.getElementById("filter-army-no");
            const rankSelect = document.getElementById("filter-rank");
            const companySelect = document.getElementById("filter-company");
            const repo = document.getElementById("soldier-repo");
            const soldierCards = Array.from(repo.querySelectorAll("label"));

            function normalize(str = '') {
                return String(str).toLowerCase().replace(/\s+/g, '');
            }

            function filter() {
                const armyRaw = armyInput.value.trim();
                const army = normalize(armyRaw);
                const rank = rankSelect.value; // ID as string or ""
                const company = companySelect.value; // ID as string or ""

                soldierCards.forEach(card => {
                    const checkbox = card.querySelector('input[type="checkbox"]');
                    const cardArmy = normalize(checkbox.dataset.armyNo || '');
                    const cardName = normalize(checkbox.dataset.fullName || '');
                    const cardRank = String(checkbox.dataset.rankId || '');
                    const cardCompany = String(checkbox.dataset.companyId || '');

                    // Army filter matches army number or full name substring
                    const matchesArmy = !army || cardArmy.includes(army) || cardName.includes(army);

                    // Rank/company compare IDs (exact match)
                    const matchesRank = !rank || cardRank === rank;
                    const matchesCompany = !company || cardCompany === company;

                    card.style.display = (matchesArmy && matchesRank && matchesCompany) ? "flex" : "none";
                });
            }

            // wire up events
            armyInput.addEventListener("input", filter);
            rankSelect.addEventListener("change", filter);
            companySelect.addEventListener("change", filter);

            // run once on load to apply any defaults
            filter();
        });
    </script>

@endsection
