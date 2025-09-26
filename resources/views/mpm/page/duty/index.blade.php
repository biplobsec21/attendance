@extends('mpm.layouts.app')

@section('title', 'Duty List')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />
        <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
            {{-- Session Success Message --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
                <h1 class="text-2xl font-bold text-gray-800">Duty Lists</h1>
                <a href="{{ route('duty.create') }}"
                    class="w-full sm:w-auto bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition-colors text-center no-underline">
                    Add New Duty
                </a>
            </div>

            {{-- Filter Form --}}
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Filter Duties</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" id="search" name="search" class="w-full border rounded-lg px-3 py-2"
                            placeholder="Search by name or remark..." value="{{ request('search') }}">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status" class="w-full border rounded-lg px-3 py-2">
                            <option value="">All</option>
                            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div>
                        <label for="rank" class="block text-sm font-medium text-gray-700 mb-1">Rank</label>
                        <select id="rank" name="rank" class="w-full border rounded-lg px-3 py-2">
                            <option value="">All Ranks</option>
                            @foreach ($ranks as $rank)
                                <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button id="clearFilters" type="button"
                            class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-transparent rounded-lg" id="dutiesTable">
                    <thead>
                        <tr>
                            @php
                                // Helper function for sortable links
                                $sortLink = fn($sortBy, $label) => '<a href="' .
                                    route(
                                        'duty.index',
                                        array_merge(request()->query(), [
                                            'sort_by' => $sortBy,
                                            'sort_direction' =>
                                                request('sort_by') == $sortBy && request('sort_direction') == 'asc'
                                                    ? 'desc'
                                                    : 'asc',
                                        ]),
                                    ) .
                                    '" class="flex items-center gap-2">' .
                                    $label .
                                    (request('sort_by') == $sortBy
                                        ? (request('sort_direction') == 'asc'
                                            ? '▲'
                                            : '▼')
                                        : '') .
                                    '</a>';
                            @endphp
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#SL
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {!! $sortLink('duty_name', 'Duty Name') !!}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ranks
                                & Manpower</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {!! $sortLink('remarks', 'Remarks') !!}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {!! $sortLink('status', 'Status') !!}</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @forelse ($duties as $duty)
                            <tr class="duty-row" data-duty-name="{{ strtolower($duty->duty_name) }}"
                                data-remark="{{ strtolower($duty->remark) }}" data-status="{{ $duty->status }}"
                                data-ranks="{{ $duty->dutyRanks->pluck('rank_id')->implode(',') }}">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $duty->duty_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $duty->start_time }} - {{ $duty->end_time }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($duty->dutyRanks->count() > 0)
                                        <div class="space-y-1">
                                            @foreach ($duty->dutyRanks as $dutyRank)
                                                <div class="flex items-center text-sm">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $dutyRank->rank->name }}
                                                    </span>
                                                    <span class="ml-2 text-gray-600">
                                                        <svg class="w-4 h-4 inline mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                            </path>
                                                        </svg>
                                                        {{ $dutyRank->manpower }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-500 text-sm">No ranks assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">{{ $duty->remark }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($duty->status == 'Active')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <a href="{{ route('duty.edit', $duty->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 no-underline">Edit</a>
                                    <form action="{{ route('duty.destroy', $duty->id) }}" method="POST"
                                        class="inline-block ml-4"
                                        onsubmit="return confirm('Are you sure you want to delete this duty?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 border-none bg-transparent cursor-pointer p-0">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No duties found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get filter elements
            const searchInput = document.getElementById('search');
            const statusSelect = document.getElementById('status');
            const rankSelect = document.getElementById('rank');
            const clearButton = document.getElementById('clearFilters');
            const dutyRows = document.querySelectorAll('.duty-row');

            // Function to filter duties
            function filterDuties() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusSelect.value;
                const rankValue = rankSelect.value;

                dutyRows.forEach(row => {
                    const dutyName = row.dataset.dutyName;
                    const remark = row.dataset.remark;
                    const status = row.dataset.status;
                    const ranks = row.dataset.ranks;

                    // Check if row matches all filters
                    const matchesSearch = searchTerm === '' ||
                        dutyName.includes(searchTerm) ||
                        remark.includes(searchTerm);

                    const matchesStatus = statusValue === '' || status === statusValue;

                    const matchesRank = rankValue === '' || ranks.includes(rankValue);

                    // Show or hide row based on filters
                    if (matchesSearch && matchesStatus && matchesRank) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Add event listeners for real-time filtering
            searchInput.addEventListener('input', filterDuties);
            statusSelect.addEventListener('change', filterDuties);
            rankSelect.addEventListener('change', filterDuties);

            // Clear all filters
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                statusSelect.value = '';
                rankSelect.value = '';
                filterDuties();
            });

            // Apply initial filters if they exist in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search')) {
                searchInput.value = urlParams.get('search');
            }
            if (urlParams.has('status')) {
                statusSelect.value = urlParams.get('status');
            }
            if (urlParams.has('rank')) {
                rankSelect.value = urlParams.get('rank');
            }

            // Apply initial filter
            filterDuties();
        });
    </script>
@endpush
