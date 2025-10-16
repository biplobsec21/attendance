{{-- resources/views/mpm/page/duty-assignments/partials/dutyDetailsModal.blade.php --}}
<div class="bg-white rounded-lg max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 rounded-t-lg">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-white">{{ $duty['duty_name'] }}</h2>
                <p class="text-blue-100 mt-1">Date: {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</p>
                <p class="text-blue-100 text-sm">{{ $duty['start_time'] }} - {{ $duty['end_time'] }}</p>
            </div>
            <div class="text-right">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    {{ $duty['status'] }}
                </span>
                <p class="text-blue-100 mt-2">{{ $statistics['total_assigned'] }} /
                    {{ $statistics['required_manpower'] }} Assigned</p>
            </div>
        </div>
    </div>

    <!-- Session Times Information -->
    @if ($duty['session_times'] && array_filter($duty['session_times']))
        <div class="px-6 py-3 bg-yellow-50 border-b border-yellow-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 text-sm">
                    <span class="font-medium text-yellow-800">Daily Session Times:</span>
                    @if ($duty['session_times']['pt_time'])
                        <span class="text-yellow-700">PT: {{ $duty['session_times']['pt_time'] }}</span>
                    @endif
                    @if ($duty['session_times']['roll_call_time'])
                        <span class="text-yellow-700">Roll Call: {{ $duty['session_times']['roll_call_time'] }}</span>
                    @endif
                    @if ($duty['session_times']['parade_time'])
                        <span class="text-yellow-700">Parade: {{ $duty['session_times']['parade_time'] }}</span>
                    @endif
                    @if ($duty['session_times']['games_time'])
                        <span class="text-yellow-700">Games: {{ $duty['session_times']['games_time'] }}</span>
                    @endif
                </div>
                @if ($duty['session_overlap_excuses'] > 0)
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                        {{ $duty['session_overlap_excuses'] }} session overlap(s)
                    </span>
                @endif
            </div>
        </div>
    @endif

    <!-- Duty Information Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-6 border-b">
        <!-- Time Information -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">Timing</p>
                    <p class="text-sm text-gray-600">{{ $duty['start_time'] }} - {{ $duty['end_time'] }}</p>
                    @if ($duty['is_overnight'])
                        <span
                            class="inline-block mt-1 px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded">Overnight</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Duration -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">Duration</p>
                    <p class="text-sm text-gray-600">{{ $duty['total_hours'] }} hours</p>
                    <p class="text-xs text-gray-500">{{ $duty['duration_days'] }} day(s)</p>
                </div>
            </div>
        </div>

        <!-- Manpower Stats -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">Manpower</p>
                    <p class="text-sm text-gray-600">{{ $statistics['fulfillment_rate'] }}% Complete</p>
                    <p class="text-xs text-gray-500">{{ $statistics['shortage'] }} short</p>
                </div>
            </div>
        </div>

        <!-- Duty Excuses -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">Duty Excuses</p>
                    <p class="text-sm text-gray-600">{{ $statistics['duty_excuses_count'] }} provided</p>
                    @if ($statistics['session_overlap_excuses'] > 0)
                        <p class="text-xs text-orange-600">{{ $statistics['session_overlap_excuses'] }} session
                            overlaps</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Excuse Badges with Reasons -->
    @if ($duty['has_any_excuse'])
        <div class="px-6 py-4 border-b bg-yellow-50">
            <h3 class="text-sm font-medium text-gray-900 mb-2">This duty provides excuses for:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach ($duty['excuse_info'] as $type => $excuse)
                    @if ($excuse['excused'])
                        <div class="flex items-center justify-between p-2 bg-white rounded border">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">{{ $excuse['description'] }}</span>
                            </div>
                            <span
                                class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $excuse['reason'] }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Rest of the modal content remains the same -->
    <!-- Statistics Overview -->
    <div class="px-6 py-4 border-b bg-gray-50">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div>
                <p class="text-2xl font-bold text-blue-600">{{ $statistics['total_assigned'] }}</p>
                <p class="text-sm text-gray-600">Total Assigned</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-green-600">{{ $statistics['roster_count'] }}</p>
                <p class="text-sm text-gray-600">Roster Assignments</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-purple-600">{{ $statistics['fixed_count'] }}</p>
                <p class="text-sm text-gray-600">Fixed Assignments</p>
            </div>
            <div>
                <p
                    class="text-2xl font-bold {{ $statistics['fulfillment_rate'] >= 100 ? 'text-green-600' : ($statistics['fulfillment_rate'] >= 80 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $statistics['fulfillment_rate'] }}%
                </p>
                <p class="text-sm text-gray-600">Fulfillment Rate</p>
            </div>
        </div>
    </div>

    <!-- Assignments Section -->
    <div class="p-6">
        <!-- Fixed Assignments -->
        @if ($assignments['fixed']->count() > 0)
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                        Fixed Assignments ({{ $assignments['fixed']->count() }})
                    </h3>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">From:
                        {{ $assignment_summary['fixed_source'] }}</span>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Soldier</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rank & Company</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Priority</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($assignments['fixed'] as $assignment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $assignment['full_name'] }}</div>
                                                    <div class="text-sm text-gray-500">{{ $assignment['army_no'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $assignment['rank'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $assignment['company'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Priority {{ $assignment['priority'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($assignment['is_on_leave'])
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    On Leave
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Available
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $assignment['remarks'] ?: '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Roster Assignments -->
        @if ($assignments['roster']->count() > 0)
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Roster Assignments ({{ $assignments['roster']->count() }})
                    </h3>
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">From:
                        {{ $assignment_summary['roster_source'] }}</span>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Soldier</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Rank & Company</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Timing</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($assignments['roster'] as $assignment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $assignment['full_name'] }}</div>
                                                    <div class="text-sm text-gray-500">{{ $assignment['army_no'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $assignment['rank'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $assignment['company'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $assignment['start_time'] }} -
                                                {{ $assignment['end_time'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $assignment['status'] === 'assigned'
                                            ? 'bg-green-100 text-green-800'
                                            : ($assignment['status'] === 'pending'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($assignment['status']) }}
                                            </span>
                                            @if ($assignment['is_on_leave'])
                                                <span
                                                    class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    On Leave
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $assignment['remarks'] ?: '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif



        <!-- Empty State -->
        @if ($assignments['fixed']->count() === 0 && $assignments['roster']->count() === 0)
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                    </path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No assignments</h3>
                <p class="mt-1 text-sm text-gray-500">No soldiers have been assigned to this duty yet.</p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="px-6 py-4 bg-gray-50 border-t rounded-b-lg flex justify-between items-center">
        <div class="text-sm text-gray-500">
            Last updated: {{ now()->format('M d, Y H:i') }}
        </div>

    </div>
</div>
