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
                            class="group relative flex items-start space-x-3 p-4 bg-white rounded-xl border border-gray-200 opacity-75">
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
                                            class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">{{ $soldier->rank->name }}</span>
                                        <span
                                            class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">{{ $soldier->company->name }}</span>
                                    </div>

                                    <!-- Assignment Badges -->
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @if ($soldier->activeCourses()->exists())
                                            @foreach ($soldier->activeCourses as $course)
                                                <span
                                                    class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                        </path>
                                                    </svg>
                                                    {{ $course->name }}
                                                </span>
                                            @endforeach
                                        @endif

                                        @if ($soldier->activeCadres()->exists())
                                            @foreach ($soldier->activeCadres as $cadre)
                                                <span
                                                    class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                        </path>
                                                    </svg>
                                                    {{ $cadre->name }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full text-gray-500">
                    <svg class="w-16 h-16 mb-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
