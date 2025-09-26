<!-- resources/views/mpm/page/cadre_course/available_soldiers_section.blade.php -->

<div class="mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Available Personnel ({{ $availableSoldiers->count() }})
    </h3>


    <div class="relative">
        <div class="absolute inset-0 bg-gradient-to-r from-green-400/10 to-emerald-400/10 rounded-2xl"></div>
        <div id="available-soldier-repo"
            class="relative bg-white/90 backdrop-blur-sm border-2 border-gray-100 rounded-2xl p-6 h-80 overflow-y-auto shadow-inner">
            @if ($availableSoldiers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach ($availableSoldiers as $soldier)
                        <label
                            class="group relative flex items-start space-x-3 p-4 bg-white rounded-xl border border-gray-100 hover:border-green-300 hover:shadow-lg transition-all duration-300 cursor-pointer hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50">
                            <input type="checkbox" name="soldier_ids[]" value="{{ $soldier->id }}"
                                data-rank-id="{{ $soldier->rank_id }}" data-company-id="{{ $soldier->company_id }}"
                                data-army-no="{{ strtolower(str_replace(' ', '', $soldier->army_no ?? '')) }}"
                                data-full-name="{{ strtolower($soldier->full_name ?? '') }}"
                                data-completed-today="{{ $soldier->hasCompletedAssignmentsToday() ? '1' : '0' }}"
                                class="form-checkbox h-5 w-5 text-green-600 rounded-lg border-2 border-gray-300 focus:ring-green-500/50 focus:ring-2 transition-all duration-200 mt-0.5">

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
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Completed Today
                                        </span>
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded-md inline-block">🆔
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
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
</div>
