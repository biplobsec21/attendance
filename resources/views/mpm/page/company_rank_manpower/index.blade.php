@extends('mpm.layouts.app')

@section('title', 'Primary Manpower Distribution')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
        <div class="container mx-auto px-4 py-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-2">
                    <div
                        class="w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                        Manpower Distribution
                    </h1>
                </div>
                <p class="text-gray-600 text-sm">Manage and track personnel allocation across companies and ranks</p>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div
                    class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 rounded-xl shadow-sm flex items-center space-x-3">
                    <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('company_rank_manpower.store') }}">
                @csrf

                <!-- Main Table Card -->
                <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-xl border border-white/50 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full" id="manpower-table">
                            <thead>
                                <tr class="bg-gradient-to-r from-slate-800 to-slate-700">
                                    <th
                                        class="px-6 py-4 text-left text-sm font-semibold text-white border-r border-slate-600">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                </path>
                                            </svg>
                                            <span>Company / Rank</span>
                                        </div>
                                    </th>
                                    @foreach ($ranks as $rank)
                                        <th
                                            class="px-4 py-4 text-center text-sm font-semibold text-white border-r border-slate-600 last:border-r-0 min-w-[120px]">
                                            <div class="flex flex-col items-center space-y-1">
                                                <div
                                                    class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                                    <span class="text-xs font-bold">{{ substr($rank->name, 0, 2) }}</span>
                                                </div>
                                                <span class="text-xs">{{ $rank->name }}</span>
                                            </div>
                                        </th>
                                    @endforeach
                                    <th
                                        class="px-4 py-4 text-center text-sm font-semibold text-white bg-gradient-to-r from-slate-700 to-slate-600 min-w-[100px]">
                                        <div class="flex flex-col items-center space-y-1">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <span class="text-xs">Total</span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($companies as $index => $company)
                                    <tr
                                        class="hover:bg-blue-50/50 transition-colors duration-200 {{ $index % 2 == 0 ? 'bg-white/50' : 'bg-gray-50/50' }}">
                                        <td
                                            class="px-6 py-4 font-semibold text-gray-900 border-r border-gray-200 bg-gradient-to-r from-slate-50 to-gray-50">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-3 h-3 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full">
                                                </div>
                                                <span>{{ $company->name }}</span>
                                            </div>
                                        </td>
                                        @foreach ($ranks as $rank)
                                            <td class="px-4 py-4 text-center border-r border-gray-200 last:border-r-0">
                                                <div class="flex flex-col items-center space-y-1">
                                                    <input type="number" min="0"
                                                        name="manpower[{{ $company->id }}][{{ $rank->id }}]"
                                                        value="{{ old('manpower.' . $company->id . '.' . $rank->id, $manpower[$company->id][$rank->id]->manpower_number ?? '') }}"
                                                        class="w-20 h-10 px-3 py-2 text-center border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 bg-white/70 backdrop-blur-sm hover:border-gray-300 manpower-input"
                                                        data-company="{{ $company->id }}" data-rank="{{ $rank->id }}">
                                                    @error("manpower.{$company->id}.{$rank->id}")
                                                        <div class="text-red-500 text-xs mt-1 bg-red-50 px-2 py-1 rounded">
                                                            {{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </td>
                                        @endforeach
                                        <td
                                            class="px-4 py-4 text-center bg-gradient-to-r from-blue-50 to-indigo-50 border-l-2 border-blue-200">
                                            <div class="flex flex-col items-center space-y-1">
                                                <div
                                                    class="w-20 h-10 bg-gradient-to-r from-blue-100 to-indigo-100 border-2 border-blue-300 rounded-lg flex items-center justify-center">
                                                    <span class="font-bold text-blue-800 company-total"
                                                        data-company="{{ $company->id }}">0</span>
                                                </div>
                                                <span class="text-xs text-blue-600 font-medium">Personnel</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <!-- Totals Footer -->
                            <tfoot class="bg-gradient-to-r from-indigo-800 to-purple-800">
                                <tr>
                                    <td class="px-6 py-4 font-bold text-white border-r border-indigo-600">
                                        <div class="flex items-center space-x-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                                </path>
                                            </svg>
                                            <span>Total by Rank</span>
                                        </div>
                                    </td>
                                    @foreach ($ranks as $rank)
                                        <td
                                            class="px-4 py-4 text-center text-white border-r border-indigo-600 last:border-r-0">
                                            <div class="flex flex-col items-center space-y-1">
                                                <div
                                                    class="w-20 h-10 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center border border-white/30">
                                                    <span class="font-bold rank-total"
                                                        data-rank="{{ $rank->id }}">0</span>
                                                </div>
                                                <span class="text-xs text-indigo-200">Total</span>
                                            </div>
                                        </td>
                                    @endforeach
                                    <td
                                        class="px-4 py-4 text-center bg-gradient-to-r from-purple-700 to-pink-700 border-l-2 border-purple-500">
                                        <div class="flex flex-col items-center space-y-1">
                                            <div
                                                class="w-20 h-10 bg-white/30 backdrop-blur-sm rounded-lg flex items-center justify-center border-2 border-white/50">
                                                <span class="font-bold text-white" id="grand-total">0</span>
                                            </div>
                                            <span class="text-xs text-purple-200 font-medium">Grand Total</span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span class="inline-flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Changes are automatically calculated as you type</span>
                        </span>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button type="reset"
                            class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-medium shadow-sm">
                            <span class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                <span>Reset</span>
                            </span>
                        </button>

                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl">
                            <span class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Save Changes</span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.manpower-input');

            function calculateTotals() {
                // Calculate company totals
                const companies = {};
                const ranks = {};
                let grandTotal = 0;

                inputs.forEach(input => {
                    const companyId = input.dataset.company;
                    const rankId = input.dataset.rank;
                    const value = parseInt(input.value) || 0;

                    // Company totals
                    if (!companies[companyId]) companies[companyId] = 0;
                    companies[companyId] += value;

                    // Rank totals
                    if (!ranks[rankId]) ranks[rankId] = 0;
                    ranks[rankId] += value;

                    grandTotal += value;
                });

                // Update company totals
                Object.keys(companies).forEach(companyId => {
                    const element = document.querySelector(`.company-total[data-company="${companyId}"]`);
                    if (element) element.textContent = companies[companyId];
                });

                // Update rank totals
                Object.keys(ranks).forEach(rankId => {
                    const element = document.querySelector(`.rank-total[data-rank="${rankId}"]`);
                    if (element) element.textContent = ranks[rankId];
                });

                // Update grand total
                document.getElementById('grand-total').textContent = grandTotal;
            }

            // Calculate totals on page load
            calculateTotals();

            // Recalculate totals when any input changes
            inputs.forEach(input => {
                input.addEventListener('input', calculateTotals);
            });
        });
    </script>
@endsection
