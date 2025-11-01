<!-- resources/views/mpm/page/cadre_course/assigned_personnel_section.blade.php -->

<div>
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
            </path>
        </svg>
        Currently Assigned Personnel ({{ $assignedSoldiers->count() }})
    </h3>
    <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-r from-amber-400/10 to-orange-400/10 rounded-2xl">
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
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
                                        <p class="text-sm font-semibold text-gray-700 truncate">
                                            {{ $soldier->full_name }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded-md inline-block">
                                            🆔 {{ $soldier->army_no }}</p>
                                        <div class="flex flex-wrap gap-1">
                                            <span
                                                class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">{{ $soldier->rank->name ?? 'No Rank' }}</span>
                                            <span
                                                class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">{{ $soldier->company->name ?? 'No Company' }}</span>
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
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
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
                                                        <div class="text-xs font-medium text-blue-800">
                                                            {{ $course->course->name ?? 'N/A' }}</div>
                                                        <div class="text-xs text-blue-600 mt-0.5">
                                                            @if ($course->start_date)
                                                                {{ \Carbon\Carbon::parse($course->start_date)->format('M d, Y') }}
                                                            @endif
                                                            @if ($course->end_date)
                                                                -
                                                                {{ \Carbon\Carbon::parse($course->end_date)->format('M d, Y') }}
                                                            @endif
                                                        </div>
                                                        @if ($course->remarks)
                                                            <div class="text-xs text-blue-500 mt-0.5 truncate"
                                                                title="{{ $course->remarks }}">
                                                                {{ Str::limit($course->remarks, 30) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
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
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
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
                                                        <div class="text-xs font-medium text-purple-800">
                                                            {{ $cadre->cadre->name ?? 'N/A' }}</div>
                                                        <div class="text-xs text-purple-600 mt-0.5">
                                                            @if ($cadre->start_date)
                                                                {{ \Carbon\Carbon::parse($cadre->start_date)->format('M d, Y') }}
                                                            @endif
                                                            @if ($cadre->end_date)
                                                                -
                                                                {{ \Carbon\Carbon::parse($cadre->end_date)->format('M d, Y') }}
                                                            @endif
                                                        </div>
                                                        @if ($cadre->remarks)
                                                            <div class="text-xs text-purple-500 mt-0.5 truncate"
                                                                title="{{ $cadre->remarks }}">
                                                                {{ Str::limit($cadre->remarks, 30) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
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
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
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
                                                        <div class="text-xs font-medium text-teal-800">
                                                            {{ $exArea->exArea->name ?? 'N/A' }}</div>
                                                        <div class="text-xs text-teal-600 mt-0.5">
                                                            @if ($exArea->start_date)
                                                                {{ \Carbon\Carbon::parse($exArea->start_date)->format('M d, Y') }}
                                                            @endif
                                                            @if ($exArea->end_date)
                                                                -
                                                                {{ \Carbon\Carbon::parse($exArea->end_date)->format('M d, Y') }}
                                                            @endif
                                                        </div>
                                                        @if ($exArea->remarks)
                                                            <div class="text-xs text-teal-500 mt-0.5 truncate"
                                                                title="{{ $exArea->remarks }}">
                                                                {{ Str::limit($exArea->remarks, 30) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="w-2 h-2 bg-teal-400 rounded-full"></div>
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
                                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
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
                                                        <div class="text-xs font-medium text-indigo-800">
                                                            {{ $service->appointments_name ?? 'N/A' }}</div>
                                                        <div class="text-xs text-indigo-600 mt-0.5">
                                                            @if ($service->from_date)
                                                                {{ \Carbon\Carbon::parse($service->from_date)->format('M d, Y') }}
                                                            @endif
                                                            @if ($service->to_date)
                                                                -
                                                                {{ \Carbon\Carbon::parse($service->to_date)->format('M d, Y') }}
                                                            @endif
                                                        </div>
                                                        @if ($service->remarks)
                                                            <div class="text-xs text-indigo-500 mt-0.5 truncate"
                                                                title="{{ $service->remarks }}">
                                                                {{ Str::limit($service->remarks, 30) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="w-2 h-2 bg-indigo-400 rounded-full"></div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- No Assignments Message -->
                                @if (
                                    !$soldier->activeCourses()->exists() &&
                                        !$soldier->activeCadres()->exists() &&
                                        (!$soldier->activeExAreas || $soldier->activeExAreas->count() == 0) &&
                                        !$soldier->activeServices()->exists())
                                    <div class="text-center py-4 text-gray-500">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <p class="text-xs">No active assignments</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full text-gray-500">
                    <svg class="w-16 h-16 mb-4 text-green-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
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

@push('styles')
    <style>
        .assignment-section {
            border-left: 2px solid #e5e7eb;
            padding-left: 0.5rem;
        }

        .assignment-section:last-child {
            margin-bottom: 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Assigned soldiers filtering functionality (if needed in the future)
            const assignedRepo = document.getElementById("assigned-soldier-repo");
            const assignedSoldierCards = Array.from(assignedRepo.querySelectorAll("div.group"));

            // Future filtering functionality for assigned soldiers can be added here
            function filterAssignedSoldiers() {
                // Implementation for filtering assigned soldiers if needed
            }
        });
    </script>
@endpush
