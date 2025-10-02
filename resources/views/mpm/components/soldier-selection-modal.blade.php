<!-- Soldier Selection Modal -->
<div id="soldier-selection-modal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[80vh] overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Select Soldier for Fixed Assignment</h3>
                    <p class="text-sm text-gray-600 mt-1">Choose from all available soldiers</p>
                </div>
                <div class="text-sm text-gray-500">
                    <span id="soldier-count">{{ count($availableSoldiers ?? []) }}</span> soldiers available
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Search Box -->
            <div class="relative mb-4">
                <input type="text" id="soldier-search"
                    placeholder="Search soldiers by name, army number, rank, or company..."
                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition-all duration-300">
                <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Soldier List -->
            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-xl" id="soldier-options">
                @if (!empty($availableSoldiers))
                    @foreach ($availableSoldiers as $soldier)
                        <div class="soldier-option border-b border-gray-100 last:border-b-0 p-4 hover:bg-green-50 cursor-pointer transition-colors duration-200"
                            onclick="selectSoldier({{ $soldier['id'] }})"
                            data-soldier-name="{{ strtolower($soldier['full_name']) }}"
                            data-army-no="{{ strtolower($soldier['army_no']) }}"
                            data-rank="{{ strtolower($soldier['rank']) }}"
                            data-company="{{ strtolower($soldier['company']) }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4 flex-1">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-800 truncate">{{ $soldier['full_name'] }}
                                        </h4>
                                        <p class="text-sm text-gray-600 truncate">
                                            <span class="font-mono">{{ $soldier['army_no'] }}</span> •
                                            <span>{{ $soldier['rank'] }}</span> •
                                            <span>{{ $soldier['company'] }}</span>
                                        </p>
                                        @if (!empty($soldier['current_assignments']))
                                            <div class="relative group mt-1">
                                                <p class="text-xs text-amber-600 flex items-center cursor-help">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Currently assigned to {{ count($soldier['current_assignments']) }}
                                                    duty(s)
                                                </p>

                                                <!-- Hover Tooltip -->
                                                <div
                                                    class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-50 w-64">
                                                    <div
                                                        class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 shadow-xl">
                                                        <div class="font-semibold mb-1 text-white">Current Assignments:
                                                        </div>
                                                        <div class="space-y-1 max-h-32 overflow-y-auto">
                                                            @foreach ($soldier['current_assignments'] as $assignment)
                                                                <div
                                                                    class="flex justify-between items-start mb-1 last:mb-0">
                                                                    <div class="flex items-start space-x-2 flex-1">
                                                                        <!-- Icon based on type -->
                                                                        @switch($assignment['type'])
                                                                            @case('fixed_duty')
                                                                                <svg class="w-3 h-3 text-green-400 mt-0.5 flex-shrink-0"
                                                                                    fill="none" stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round" stroke-width="2"
                                                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                </svg>
                                                                            @break

                                                                            @case('course')
                                                                                <svg class="w-3 h-3 text-blue-400 mt-0.5 flex-shrink-0"
                                                                                    fill="none" stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round" stroke-width="2"
                                                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                                                </svg>
                                                                            @break

                                                                            @case('cadre')
                                                                                <svg class="w-3 h-3 text-purple-400 mt-0.5 flex-shrink-0"
                                                                                    fill="none" stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round" stroke-width="2"
                                                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                                                </svg>
                                                                            @break

                                                                            @case('service')
                                                                                <svg class="w-3 h-3 text-yellow-400 mt-0.5 flex-shrink-0"
                                                                                    fill="none" stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round" stroke-width="2"
                                                                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                                                </svg>
                                                                            @break

                                                                            @default
                                                                                <svg class="w-3 h-3 text-gray-400 mt-0.5 flex-shrink-0"
                                                                                    fill="none" stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round" stroke-width="2"
                                                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                </svg>
                                                                        @endswitch

                                                                        <div class="flex-1 min-w-0">
                                                                            <span
                                                                                class="text-white text-xs block leading-tight">
                                                                                {{ $assignment['name'] ?? 'Unknown Duty' }}
                                                                            </span>
                                                                            <span
                                                                                class="text-amber-300 text-xs block leading-tight">
                                                                                @switch($assignment['type'])
                                                                                    @case('fixed_duty')
                                                                                        fixed duty:
                                                                                        {{ \Carbon\Carbon::parse($assignment['start_time'])->format('H:i') }}
                                                                                        -
                                                                                        {{ \Carbon\Carbon::parse($assignment['end_time'])->format('H:i') }}
                                                                                        @if ($assignment['duration_days'] > 1)
                                                                                            ({{ $assignment['duration_days'] }}
                                                                                            days)
                                                                                        @endif
                                                                                    @break

                                                                                    @case('course')
                                                                                    @case('cadre')

                                                                                    @case('service')
                                                                                        {{ \Carbon\Carbon::parse($assignment['start_date'])->format('d M Y') }}
                                                                                        -
                                                                                        {{ isset($assignment['end_date']) ? \Carbon\Carbon::parse($assignment['end_date'])->format('M d Y') : 'Ongoing' }}
                                                                                    @break

                                                                                    @default
                                                                                        Ongoing
                                                                                @endswitch
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <!-- Tooltip arrow -->
                                                    <div
                                                        class="absolute top-full left-4 border-4 border-transparent border-t-gray-900">
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 ml-4">
                                    @if ($soldier['is_on_leave'])
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            On Leave
                                        </span>
                                    @elseif(!empty($soldier['current_assignments']))
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                            Assigned
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Available
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        <h4 class="text-lg font-medium text-gray-500 mb-2">No Soldiers Available</h4>
                        <p class="text-sm text-gray-400 max-w-md mx-auto">
                            All soldiers are currently assigned to duties or on leave.
                            Please check back later or create roster assignments instead.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="p-6 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
            <div class="text-sm text-gray-600">
                <span id="filtered-soldier-count">{{ count($availableSoldiers ?? []) }}</span> soldiers shown
            </div>
            <button type="button" onclick="closeSoldierModal()"
                class="px-6 py-2 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-100 transition-colors duration-200">
                Cancel
            </button>
        </div>
    </div>
</div>
