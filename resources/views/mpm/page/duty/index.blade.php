@extends('mpm.layouts.app')

@section('title', 'Duty Management')

@section('content')

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8 px-4">
        <div class="container mx-auto max-w-7xl">

            <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />

            <!-- Header Section -->
            <div class="mb-6">
                <div class="flex items-center gap-3 p-3 bg-white/50 rounded-xl border border-gray-200">
                    <div
                        class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Duty Management</h1>
                        <p class="text-sm text-gray-500">Manage duty assignments and schedules</p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/50">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-blue-100 mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Duties</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_duties'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/50">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-green-100 mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Duties</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['active_duties'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/50">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-purple-100 mr-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Fixed Assignments</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['fixed_assignments'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg border border-white/50">
                    <div class="flex items-center">
                        <div class="p-2 rounded-lg bg-orange-100 mr-4">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Manpower</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_manpower'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Actions Card -->
            <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-3xl border border-white/50 overflow-hidden mb-8">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Search Box -->
                        <form method="GET" action="{{ route('duty.index') }}" class="flex-1">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="Search duties by name or remarks..."
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            <!-- Hidden fields to preserve other filters -->
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'created_at') }}">
                            <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'desc') }}">
                        </form>

                        <!-- Filters and Actions -->
                        <!-- Combined Filters and Actions -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <!-- Combined Filter Form -->
                            <form method="GET" action="{{ route('duty.index') }}"
                                class="flex flex-col sm:flex-row gap-3 flex-1">
                                <!-- Status Filter -->
                                <select name="status" onchange="this.form.submit()"
                                    class="px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 appearance-none">
                                    <option value="">All Status</option>
                                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active
                                    </option>
                                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>

                                <!-- Sort Options -->
                                <select name="sort_by" onchange="this.form.submit()"
                                    class="px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 appearance-none">
                                    <option value="created_at"
                                        {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>Sort by
                                        Date</option>
                                    <option value="duty_name" {{ request('sort_by') == 'duty_name' ? 'selected' : '' }}>
                                        Sort by Name</option>
                                    <option value="manpower" {{ request('sort_by') == 'manpower' ? 'selected' : '' }}>Sort
                                        by Manpower</option>
                                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Sort by
                                        Status</option>
                                </select>

                                <!-- Sort Direction -->
                                <select name="sort_direction" onchange="this.form.submit()"
                                    class="px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 appearance-none">
                                    <option value="desc"
                                        {{ request('sort_direction', 'desc') == 'desc' ? 'selected' : '' }}>Descending
                                    </option>
                                    <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>
                                        Ascending</option>
                                </select>

                                <!-- Hidden search field to preserve search -->
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            </form>

                            <!-- Create Button -->
                            <a href="{{ route('duty.create') }}"
                                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white font-semibold hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-500/50 transition-all duration-300 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Create Duty
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Duties Table Card -->
            <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-3xl border border-white/50 overflow-hidden">
                <div class="p-6">
                    @if ($duties->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b-2 border-gray-200">
                                        <th class="pb-4 text-left text-sm font-semibold text-gray-700">Duty Name</th>
                                        <th class="pb-4 text-left text-sm font-semibold text-gray-700">Schedule</th>
                                        <th class="pb-4 text-left text-sm font-semibold text-gray-700">Assignments</th>
                                        <th class="pb-4 text-left text-sm font-semibold text-gray-700">Manpower</th>
                                        <th class="pb-4 text-left text-sm font-semibold text-gray-700">Status</th>
                                        <th class="pb-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($duties as $duty)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="py-4">
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center mr-4">
                                                        <svg class="w-5 h-5 text-white" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h3 class="font-semibold text-gray-900">{{ $duty->duty_name }}
                                                        </h3>
                                                        <p class="text-sm text-gray-500 mt-1">
                                                            {{ Str::limit($duty->remark, 50) }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4">
                                                <div class="text-sm text-gray-900">
                                                    {{ $duty->start_time->format('H:i') }} -
                                                    {{ $duty->end_time->format('H:i') }}
                                                    @if ($duty->duration_days > 1)
                                                        <span
                                                            class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full ml-2">
                                                            {{ $duty->duration_days }} days
                                                        </span>
                                                    @endif
                                                </div>
                                                @php
                                                    $start = \Carbon\Carbon::createFromTimeString($duty->start_time);
                                                    $end = \Carbon\Carbon::createFromTimeString($duty->end_time);
                                                    if ($end->lt($start)) {
                                                        $end->addDay();
                                                    }
                                                    $duration =
                                                        $end->diffInHours($start) == 0 ? 24 : $end->diffInHours($start);
                                                @endphp
                                                <div class="text-xs text-gray-500">{{ $duration }}h daily</div>
                                            </td>
                                            <td class="py-4">
                                                @php
                                                    $fixedCount = $duty->dutyRanks
                                                        ->where('assignment_type', 'fixed')
                                                        ->count();
                                                    $rosterCount = $duty->dutyRanks
                                                        ->where('assignment_type', 'roster')
                                                        ->count();
                                                @endphp
                                                <div class="flex flex-wrap gap-1">
                                                    @if ($fixedCount > 0)
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <svg class="w-3 h-3 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                            {{ $fixedCount }} fixed
                                                        </span>
                                                    @endif
                                                    @if ($rosterCount > 0)
                                                        <span
                                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <svg class="w-3 h-3 mr-1" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                            {{ $rosterCount }} roster
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-4">
                                                <div class="text-lg font-bold text-gray-900">{{ $duty->manpower }}</div>
                                                <div class="text-xs text-gray-500">total manpower</div>
                                            </td>
                                            <td class="py-4">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $duty->status == 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    @if ($duty->status == 'Active')
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    @else
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    @endif
                                                    {{ $duty->status }}
                                                </span>
                                            </td>
                                            <td class="py-4">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('duty.show', $duty) }}"
                                                        class="p-2 text-gray-400 hover:text-blue-600 transition-colors duration-200"
                                                        title="View Details">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('duty.edit', $duty) }}"
                                                        class="p-2 text-gray-400 hover:text-green-600 transition-colors duration-200"
                                                        title="Edit Duty">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('duty.destroy', $duty) }}" method="POST"
                                                        class="inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this duty? This action cannot be undone.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="p-2 text-gray-400 hover:text-red-600 transition-colors duration-200"
                                                            title="Delete Duty">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{-- If you implement pagination later --}}
                            {{-- {{ $duties->links() }} --}}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No duties found</h3>
                            <p class="text-gray-500 mb-6">Get started by creating your first duty assignment.</p>
                            <a href="{{ route('duty.create') }}"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Create First Duty
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Auto-submit form when filters change
        document.addEventListener('DOMContentLoaded', function() {
            const filterSelects = document.querySelectorAll('select[name="status"], select[name="sort_by"]');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            });
        });
    </script>
@endpush
