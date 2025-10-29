@extends('mpm.layouts.app')

@section('title', 'Profile Details')
@section('content')
    <div class="container mx-auto p-4 bg-gray-50">

        <div class="min-h-screen">

            <!-- Page Header -->
            <div class="bg-gradient-to-r from-green-800 to-green-600 shadow-lg">
                <div class=" mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-white">Profiles</h1>
                            <p class="mt-2 text-green-100">Manage military personnel information</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button onclick="window.location.href='{{ route('soldier.personalForm') }}'"
                                class="bg-white text-green-800 px-6 py-3 rounded-lg font-semibold hover:bg-green-50 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <i class="fas fa-plus mr-2"></i>Add New Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class=" mx-auto px-4 sm:px-6 lg:px-8 -mt-4 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-3">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Soldiers</p>
                                <p class="text-2xl font-bold text-gray-900" id="total-count">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-3">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Active</p>
                                <p class="text-2xl font-bold text-gray-900" id="active-count">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 rounded-full p-3">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">On Leave</p>
                                <p class="text-2xl font-bold text-gray-900" id="leave-count">0</p>
                            </div>
                        </div>
                    </div>
                    {{-- ERE Stats Card --}}
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                        <div class="flex items-center">
                            <div class="bg-red-100 rounded-full p-3">
                                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">With ERE</p>
                                <p class="text-2xl font-bold text-gray-900" id="ere-count">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Toggle Button -->
                <!-- Filter Toggle Button -->
                <div class="mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                    <div class="flex items-center justify-between">
                        <button id="toggle-filters"
                            class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-all duration-200">
                            <i class="fas fa-filter text-green-600"></i>
                            <span class="font-medium text-gray-700">Filters</span>
                            <span id="filter-count"
                                class="hidden px-2 py-1 text-xs font-semibold text-white bg-green-600 rounded-full"></span>
                            <i class="fas fa-chevron-right text-gray-400 ml-2" id="filter-toggle-icon"></i>
                        </button>

                        <!-- Bulk Action Button (moved here) -->
                        <button id="bulk-action"
                            class="hidden items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm">
                            <i class="fas fa-edit"></i>
                            <span class="font-medium">Bulk Actions</span>
                        </button>
                    </div>
                </div>

                <!-- Data Table -->
                <div class=" mx-auto px-4 sm:px-6 lg:px-8 mb-8">
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <!-- Desktop Table -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left">
                                            <input type="checkbox" id="select-all"
                                                class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        </th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Soldier</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Key info.</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Skills</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="soldiers-tbody">
                                    <!-- Table rows will be populated via AJAX -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                        <div class="md:hidden space-y-4 p-4" id="mobile-cards">
                            <!-- Mobile cards will be populated via AJAX -->
                        </div>

                        <!-- Loading State -->
                        <div id="loading-state" class="flex items-center justify-center py-12">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
                            <span class="ml-3 text-gray-600">Loading soldiers...</span>
                        </div>

                        <!-- Empty State -->
                        <div id="empty-state" class="hidden flex flex-col items-center justify-center py-12">
                            <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No soldiers found</h3>
                            <p class="text-gray-600 mb-4">Try adjusting your search criteria or add a new soldier profile.
                            </p>
                            <button onclick="window.location.href='{{ route('soldier.personalForm') }}'"
                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                                Add First Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Sidebar -->
        <div id="filters-sidebar"
            class="fixed top-0 right-0 h-full w-96 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50 overflow-hidden flex flex-col">
            <!-- Sidebar Header -->
            <div
                class="bg-gradient-to-r from-green-800 to-green-600 px-6 py-4 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    <i class="fas fa-filter text-white text-xl"></i>
                    <h3 class="text-xl font-bold text-white">Filters</h3>
                </div>
                <button id="close-filters" class="text-white hover:text-green-100 transition-colors duration-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Active Filters Summary (Inside Sidebar) -->
            <div id="active-filters-summary" class="hidden bg-green-50 border-b border-green-200 px-6 py-3 flex-shrink-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-green-800 font-medium text-sm">Active:</span>
                        <div id="active-filters-list" class="flex flex-wrap gap-2"></div>
                    </div>
                    <button id="clear-all-filters"
                        class="text-green-600 hover:text-green-800 text-sm font-medium whitespace-nowrap">
                        Clear All
                    </button>
                </div>
            </div>

            <!-- Scrollable Filters Content -->
            <div class="flex-1 overflow-y-auto px-6 py-4 filter-scrollbar">
                <div class="space-y-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search text-gray-400 mr-2"></i>Search
                        </label>
                        <input type="text" id="search-input" placeholder="Name, army number..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                    </div>

                    <!-- Rank Filter -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                RK
                            </label>
                            <select id="rank-filter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Select RK</option>
                            </select>
                        </div>

                        <!-- Company Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Coy
                            </label>
                            <select id="company-filter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Select Coy</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Courses Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Courses
                            </label>
                            <select id="course-filter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Select Courses</option>
                            </select>
                        </div>

                        <!-- Cadres Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Cadres
                            </label>
                            <select id="cadre-filter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Select Cadres</option>
                            </select>
                        </div>
                    </div>
                    <!-- Status Filter (Hidden) -->
                    <div class="hidden">
                        <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status-filter"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="leave">On Leave</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">

                        <!-- Skills Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Skills
                            </label>
                            <select id="skill-filter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Select Skills</option>
                            </select>
                        </div>
                        <!-- Education Filter -->
                        <div>
                            <label for="education-filter" class="block text-sm font-medium text-gray-700 mb-2">
                                Education
                            </label>
                            <select id="education-filter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Select Education</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- ERE Filter -->
                        <div>
                            <label for="ere-filter" class="block text-sm font-medium text-gray-700 mb-2">
                                ERE Status
                            </label>
                            <select id="ere-filter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Select Soldiers</option>
                                <option value="true">With ERE</option>
                                <option value="false">Without ERE</option>
                            </select>
                        </div>

                        <!-- ATT Filter -->
                        <div>
                            <label for="att-filter" class="block text-sm font-medium text-gray-700 mb-2">
                                Att
                            </label>
                            <select id="att-filter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">Select Att</option>
                            </select>
                        </div>
                    </div>


                    <!-- Leave Filter -->
                    <div>
                        <label for="leave-filter" class="block text-sm font-medium text-gray-700 mb-2">
                            Leave Status
                        </label>
                        <select id="leave-filter"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Soldiers</option>
                            <option value="on-leave">On Leave</option>
                            <option value="not-on-leave">Not On Leave</option>
                        </select>
                    </div>

                    <!-- District Filter -->
                    <div>
                        <label for="district-filter" class="block text-sm font-medium text-gray-700 mb-2">
                            District
                        </label>
                        <select id="district-filter"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Select District</option>
                        </select>
                    </div>

                    <!-- Blood Group Filter -->
                    <div>
                        <label for="bloodGroup-filter" class="block text-sm font-medium text-gray-700 mb-2">
                            Blood Group
                        </label>
                        <select id="bloodGroup-filter"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">Select Blood Group</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Sidebar Footer with Action Buttons -->
            <!-- Sidebar Footer with Action Buttons -->
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 flex-shrink-0 hidden">
                <div class="flex flex-col gap-3">
                    <div class="flex gap-3">
                    </div>
                    <div class="flex gap-3">
                        <button id="export-excel"
                            class="flex-1 px-4 py-2 text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200 text-sm font-medium border border-green-200">
                            <i class="fas fa-file-excel mr-2"></i>Export Excel
                        </button>
                        <button id="export-pdf"
                            class="flex-1 px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors duration-200 text-sm font-medium border border-red-200">
                            <i class="fas fa-file-pdf mr-2"></i>Export PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overlay -->
        <div id="filters-overlay"
            class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity duration-300"></div>

        <!-- Profile Quick View Modal -->
        <div id="profile-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900" id="modal-title">Profile Quick View</h3>
                            <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div id="modal-content">
                            <!-- Modal content will be loaded dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Modal -->
        <div id="history-modal" class="fixed inset-0 z-50 hidden overflow-y-auto pt-16">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full max-h-[80vh] relative">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900" id="history-modal-title">History</h3>
                            <button id="close-history-modal"
                                class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div id="history-modal-content" class="overflow-y-auto max-h-[60vh]">
                            <!-- History content will be loaded dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const routes = {
            getAllSoldiers: "{{ route('soldier.getAllData') }}",
            delete: "{{ route('soldier.destroy', ['soldier' => ':id']) }}", // placeholder
            edit: "{{ route('soldier.personalForm', ['profile' => ':id']) }}",
            bulkDelete: "{{ route('soldier.bulkDelete') }}",
            view: "{{ route('soldier.details', ['id' => ':id']) }}",
            getHistory: "{{ route('soldier.history', ['id' => ':id']) }}",

            getAttTypes: '/api/soldiers/att-types',
            addAttRecord: '/api/soldiers/:id/att',
            getCmdTypes: '/api/soldiers/cmd-types',
            addCmdRecord: '/api/soldiers/:id/cmd'


        };

        // Debug ERE filter
        document.addEventListener('DOMContentLoaded', function() {
            const ereFilter = document.getElementById('ere-filter');
            if (ereFilter) {
                console.log('ERE filter element found');
                ereFilter.addEventListener('change', function() {
                    console.log('ERE filter changed to:', this.value);
                });
            } else {
                console.error('ERE filter element not found');
            }

            // Sidebar functionality
            const toggleBtn = document.getElementById('toggle-filters');
            const closeBtn = document.getElementById('close-filters');
            const sidebar = document.getElementById('filters-sidebar');
            const overlay = document.getElementById('filters-overlay');
            const applyBtn = document.getElementById('apply-filters');
            const filterToggleIcon = document.getElementById('filter-toggle-icon');
            const filterCount = document.getElementById('filter-count');

            // Toggle sidebar
            function openSidebar() {
                sidebar.classList.add('show');
                sidebar.style.transform = 'translateX(0)';
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                filterToggleIcon.classList.remove('fa-chevron-right');
                filterToggleIcon.classList.add('fa-chevron-left');
            }

            function closeSidebar() {
                sidebar.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    sidebar.classList.remove('show');
                }, 300);
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
                filterToggleIcon.classList.remove('fa-chevron-left');
                filterToggleIcon.classList.add('fa-chevron-right');
            }

            toggleBtn.addEventListener('click', function() {
                if (sidebar.classList.contains('show')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            closeBtn.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);

            // Optional: Close sidebar when apply is clicked
            if (applyBtn) {
                applyBtn.addEventListener('click', function() {
                    closeSidebar();
                    // Your existing filter apply logic will be triggered by your existing JS
                });
            }

            // Update filter count badge
            function updateFilterCount() {
                const filters = document.querySelectorAll('#filters-sidebar select, #filters-sidebar input');
                let activeCount = 0;

                filters.forEach(filter => {
                    if (filter.value && filter.value !== '' && filter.id !== 'search-input') {
                        activeCount++;
                    } else if (filter.id === 'search-input' && filter.value.trim() !== '') {
                        activeCount++;
                    }
                });

                if (activeCount > 0) {
                    filterCount.textContent = activeCount;
                    filterCount.classList.remove('hidden');
                } else {
                    filterCount.classList.add('hidden');
                }
            }

            // Listen for filter changes
            const filterInputs = document.querySelectorAll('#filters-sidebar select, #filters-sidebar input');
            filterInputs.forEach(input => {
                input.addEventListener('change', updateFilterCount);
                input.addEventListener('input', updateFilterCount);
            });

            // Close sidebar on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                    closeSidebar();
                }
            });

            // Initial count update
            updateFilterCount();
        });
    </script>
    <script type="module" src="{{ asset('asset/js/soldiers/init.js') }}"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('asset/css/profile.css') }}">
    <style>
        /* Sidebar styles */
        #filters-sidebar {
            box-shadow: -4px 0 6px -1px rgba(0, 0, 0, 0.1), -2px 0 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Custom scrollbar for sidebar */
        .filter-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .filter-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .filter-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }

        .filter-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            #filters-sidebar {
                width: 85%;
                max-width: 320px;
            }
        }

        /* Animation for filter count badge */
        #filter-count {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Ensure modals appear above sidebar */
        #profile-modal,
        #history-modal {
            z-index: 60;
        }
    </style>
@endpush
