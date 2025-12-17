<!-- Soldier Selection Modal - Enhanced Version -->
<div id="soldier-selection-modal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col">

        <!-- Modal Header -->
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50 flex-shrink-0">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Select Soldier for Fixed Assignment
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Choose a soldier to assign to this duty permanently</p>
                </div>
                <button type="button" onclick="closeSoldierModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Stats Bar -->
            <div class="mt-4 flex items-center gap-4 text-sm">
                <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-green-200">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                    <span class="font-semibold text-gray-700">Total: <span id="soldier-count"
                            class="text-green-600">0</span></span>
                </div>
                <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-gray-200">
                    <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-semibold text-gray-700">Showing: <span id="filtered-soldier-count"
                            class="text-gray-600">0</span></span>
                </div>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="flex-1 overflow-hidden flex flex-col">
            <div class="p-6 flex-shrink-0">
                <!-- Search and Filter Controls -->
                <div class="space-y-4">
                    <!-- Search Box -->
                    <div class="relative">
                        <input type="text" id="soldier-search"
                            placeholder="Search by name, army number, rank, or company..."
                            class="w-full px-4 py-3 pl-12 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition-all duration-300">
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button type="button" id="clear-search"
                            class="hidden absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Filter Controls -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Filter
                                by Rank</label>
                            <select id="soldier-rank-filter"
                                class="w-full px-4 py-2.5 bg-gray-50 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all">
                                <option value="">All Ranks</option>
                                @foreach ($ranks as $rank)
                                    <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="button" id="reset-filters"
                                class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="soldier-loading" class="hidden flex-1 flex items-center justify-center">

            </div>

            <!-- Soldier List Container -->
            <div id="soldier-options-container" class="flex-1 overflow-y-auto px-6 pb-6 space-y-2 custom-scrollbar">
                <!-- Soldiers will be loaded here dynamically -->
                <div class="text-center py-16 text-gray-400">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <h4 class="text-lg font-medium text-gray-500 mb-2">Click "Load Soldiers" to Begin</h4>
                    <p class="text-sm text-gray-400 max-w-md mx-auto mb-4">
                        Soldiers will be loaded on demand to improve page performance.
                    </p>
                    <button id="load-soldiers-btn" type="button"
                        class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        Load Soldiers
                    </button>
                </div>
            </div>

            <!-- Pagination Controls -->
            <div id="soldier-pagination" class="hidden px-6 pb-6 flex-shrink-0">
                <!-- Pagination will be rendered here by JavaScript -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-6 border-t border-gray-200 bg-gray-50 flex justify-end flex-shrink-0">
            <button type="button" onclick="closeSoldierModal()"
                class="px-6 py-2.5 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-100 hover:border-gray-400 transition-all duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Close
            </button>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 8px;
        margin: 8px 0;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #10b981, #059669);
        border-radius: 8px;
        border: 2px solid #f1f5f9;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #059669, #047857);
    }

    .soldier-card {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
