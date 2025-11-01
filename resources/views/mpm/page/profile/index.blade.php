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
                <div class="mx-auto px-4 sm:px-6 lg:px-8 mb-4">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <button id="toggle-filters"
                                class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-all duration-200 relative">
                                <i class="fas fa-filter text-green-600"></i>
                                <span class="font-medium text-gray-700">Filters</span>
                                <span id="filter-count"
                                    class="absolute -top-2 -right-2 px-2 py-1 text-xs font-semibold text-white bg-green-600 rounded-full hidden min-w-[20px] text-center"></span>
                                <i class="fas fa-chevron-right text-gray-400 ml-2" id="filter-toggle-icon"></i>
                            </button>

                            <!-- NEW: Selection Status Badge -->
                            <div id="selection-status" class="hidden">
                                <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                                    <i class="fas fa-check-square text-blue-600"></i>
                                    <span class="text-sm font-medium text-blue-900">
                                        <span id="selection-count">0</span> selected
                                    </span>
                                    <button id="clear-selection"
                                        class="ml-2 text-blue-600 hover:text-blue-800 transition-colors"
                                        title="Clear selection">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            <!-- Export buttons -->
                            <button id="export-excel"
                                class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm">
                                <i class="fas fa-file-excel"></i>
                                <span class="font-medium">Export Excel</span>
                            </button>

                            <button id="export-pdf"
                                class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 shadow-sm">
                                <i class="fas fa-file-pdf"></i>
                                <span class="font-medium">Export PDF</span>
                            </button>

                            <!-- Bulk Action Button -->
                            <button id="bulk-action"
                                class="hidden items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm">
                                <i class="fas fa-edit"></i>
                                <span class="font-medium">Bulk Actions</span>
                            </button>
                        </div>
                    </div>

                    <!-- NEW: Selection info bar (shown when soldiers are selected) -->
                    <div id="selection-info-bar"
                        class="hidden mt-3 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                    <span class="text-sm text-blue-900">
                                        <span id="selected-of-visible" class="font-semibold">0 of 0</span> visible soldiers
                                        selected
                                    </span>
                                </div>
                                <div class="text-xs text-blue-700">
                                    (<span id="selection-percentage">0</span>% of visible)
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button id="select-all-visible"
                                    class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-check-double text-xs mr-1"></i>
                                    Select All Visible
                                </button>
                                <button id="deselect-all"
                                    class="text-sm px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                                    <i class="fas fa-times text-xs mr-1"></i>
                                    Clear All
                                </button>
                            </div>
                        </div>
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
            class="fixed top-16 right-0 h-[calc(100vh-4rem)] w-96 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out z-50 overflow-hidden flex flex-col">
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
            <div id="active-filters-summary"
                class="hidden bg-green-50 border-b border-green-200 px-6 py-3 sticky top-16 z-5">
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
            <div class="flex-1 px-6 py-4 overflow-auto filter-scrollbar">
                <div class="space-y-4">
                    <!-- Search -->
                    <!-- In your filters sidebar -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search text-gray-400 mr-2"></i>Search
                        </label>
                        <input type="text" id="search-input" placeholder="Name, army number, mobile number..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                    </div>

                    <!-- Rank Filter -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                RK
                            </label>
                            <div id="rank-filter-container"></div>
                        </div>

                        <!-- Company Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Coy
                            </label>
                            <div id="company-filter-container"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Courses Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Courses
                            </label>
                            <div id="course-filter-container"></div>
                        </div>

                        <!-- Cadres Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Cadres
                            </label>
                            <div id="cadre-filter-container"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Skills Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Skills
                            </label>
                            <div id="skill-filter-container"></div>
                        </div>

                        <!-- Education Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Education
                            </label>
                            <div id="education-filter-container"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- ERE Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                ERE Status
                            </label>
                            <div id="ere-filter-container"></div>
                        </div>

                        <!-- ATT Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Att
                            </label>
                            <div id="att-filter-container"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Leave Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Leave Status
                            </label>
                            <div id="leave-filter-container"></div>
                        </div>

                        <!-- District Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                District
                            </label>
                            <div id="district-filter-container"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Blood Group Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Blood Group
                            </label>
                            <div id="bloodGroup-filter-container"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                CMD
                            </label>
                            <div id="cmd-filter-container"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ex-Areas
                            </label>
                            <div id="exArea-filter-container"></div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Sidebar Footer -->

        </div>

        <!-- Overlay -->
        <div id="filters-overlay"
            class="fixed inset-0 bg-black bg-opacity-50 z-[55] hidden transition-opacity duration-300"></div>

        <!-- Profile Quick View Modal -->
        <div id="profile-modal" class="fixed inset-0 z-[70] hidden overflow-y-auto">
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
        <div id="history-modal" class="fixed inset-0 z-[70] hidden overflow-y-auto pt-16">
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
    <!-- SheetJS for Excel Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- jsPDF for PDF Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- jsPDF AutoTable for PDF Tables -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

    <script>
        const routes = {
            getAllSoldiers: "{{ route('soldier.getAllData') }}",
            delete: "{{ route('soldier.destroy', ['soldier' => ':id']) }}",
            edit: "{{ route('soldier.personalForm', ['profile' => ':id']) }}",
            bulkDelete: "{{ route('soldier.bulkDelete') }}",
            view: "{{ route('soldier.details', ['id' => ':id']) }}",
            getHistory: "{{ route('soldier.history', ['id' => ':id']) }}",
            getAttTypes: '/api/soldiers/att-types',
            addAttRecord: '/api/soldiers/:id/att',
            getCmdTypes: '/api/soldiers/cmd-types',
            addCmdRecord: '/api/soldiers/:id/cmd',
            defaultAvatar: "{!! asset('images/default-avatar.png') !!}"
        };

        // Sidebar functionality - ONLY basic open/close, no filter logic
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggle-filters');
            const closeBtn = document.getElementById('close-filters');
            const sidebar = document.getElementById('filters-sidebar');
            const overlay = document.getElementById('filters-overlay');
            const filterToggleIcon = document.getElementById('filter-toggle-icon');

            // Toggle sidebar
            function openSidebar() {
                sidebar.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                filterToggleIcon.classList.remove('fa-chevron-right');
                filterToggleIcon.classList.add('fa-chevron-left');
            }

            function closeSidebar() {
                sidebar.classList.add('translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
                filterToggleIcon.classList.remove('fa-chevron-left');
                filterToggleIcon.classList.add('fa-chevron-right');
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (sidebar.classList.contains('translate-x-full')) {
                        openSidebar();
                    } else {
                        closeSidebar();
                    }
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeSidebar();
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeSidebar();
                });
            }

            // Close sidebar on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !sidebar.classList.contains('translate-x-full')) {
                    closeSidebar();
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for manager to be initialized
            const checkManager = setInterval(() => {
                if (window.manager) {
                    clearInterval(checkManager);
                    initSelectionUI();
                }
            }, 100);
        });

        function initSelectionUI() {
            const manager = window.manager;

            // Update selection UI whenever selection changes
            const originalUpdateBulkAction = manager.bulkActions.updateBulkActionButton.bind(manager.bulkActions);
            manager.bulkActions.updateBulkActionButton = function() {
                originalUpdateBulkAction();
                updateSelectionUI();
            };

            // Clear selection button
            document.getElementById('clear-selection')?.addEventListener('click', () => {
                manager.selectedRows.clear();
                manager.elements.selectAll.checked = false;
                manager.elements.selectAll.indeterminate = false;
                manager.updateCheckboxStates();
                manager.bulkActions.updateBulkActionButton();
                showToast('Selection cleared', 'info');
            });

            // Select all visible button
            document.getElementById('select-all-visible')?.addEventListener('click', () => {
                manager.toggleSelectAllVisible(true);
            });

            // Deselect all button
            document.getElementById('deselect-all')?.addEventListener('click', () => {
                manager.toggleSelectAllVisible(false);
            });

            // Highlight selected rows
            const originalHandleCheckboxChange = manager.handleCheckboxChange.bind(manager);
            manager.handleCheckboxChange = function(event) {
                originalHandleCheckboxChange(event);

                // Add/remove highlight class
                if (event.target.classList.contains('row-select')) {
                    const row = event.target.closest('tr');
                    if (row) {
                        if (event.target.checked) {
                            row.classList.add('row-selected');
                        } else {
                            row.classList.remove('row-selected');
                        }
                    }
                }
            };
        }

        function updateSelectionUI() {
            const manager = window.manager;
            const stats = manager.getSelectionStats();

            // Update selection status badge
            const selectionStatus = document.getElementById('selection-status');
            const selectionCount = document.getElementById('selection-count');

            if (stats.selected > 0) {
                selectionStatus?.classList.remove('hidden');
                if (selectionCount) selectionCount.textContent = stats.selected;
            } else {
                selectionStatus?.classList.add('hidden');
            }

            // Update selection info bar
            const infoBar = document.getElementById('selection-info-bar');
            const selectedOfVisible = document.getElementById('selected-of-visible');
            const selectionPercentage = document.getElementById('selection-percentage');

            if (stats.selected > 0) {
                infoBar?.classList.remove('hidden');
                if (selectedOfVisible) {
                    selectedOfVisible.textContent = `${stats.selected} of ${stats.visible}`;
                }
                if (selectionPercentage) {
                    selectionPercentage.textContent = stats.percentage;
                }
            } else {
                infoBar?.classList.add('hidden');
            }

            // Highlight selected rows
            document.querySelectorAll('.row-select').forEach(checkbox => {
                const row = checkbox.closest('tr');
                if (row) {
                    if (checkbox.checked) {
                        row.classList.add('row-selected');
                    } else {
                        row.classList.remove('row-selected');
                    }
                }
            });
        }
    </script>
    <script type="module" src="{{ asset('asset/js/soldiers/init.js') }}"></script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('asset/css/profile.css') }}">
    <style>
        /* Multi-select dropdown styles */
        .multi-select-container {
            position: relative;
        }

        .multi-select-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            margin-top: 0.25rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 100;
            max-height: 200px;
            overflow-y: auto;
            display: none;
        }

        .multi-select-dropdown.show {
            display: block;
        }

        .multi-select-option {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .multi-select-option:hover {
            background-color: #f3f4f6;
        }

        .multi-select-option input[type="checkbox"] {
            margin-right: 0.5rem;
        }

        .multi-select-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
            margin-top: 0.5rem;
            max-width: 100%;
        }

        .multi-select-tag {
            display: inline-flex;
            align-items: center;
            background-color: #10b981;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            max-width: 100%;
            word-break: break-word;
        }

        .multi-select-tag button {
            margin-left: 0.25rem;
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .multi-select-tag button:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .multi-select-input {
            cursor: pointer;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.5rem 2.5rem 0.5rem 0.75rem;
            background: white;
            min-height: 42px;
            display: flex;
            align-items: center;
            position: relative;
        }

        .multi-select-input::after {
            content: "▼";
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.75rem;
            color: #6b7280;
        }

        .multi-select-input.filter-active {
            border-color: #10b981;
            background-color: #f0fdf4;
        }

        .search-input {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            width: 100%;
        }

        .search-input:focus {
            outline: none;
            ring: 2px;
            ring-color: #10b981;
            border-color: #10b981;
        }

        /* Filter count badge */
        #filter-count {
            line-height: 1;
        }

        /* Ensure proper z-index hierarchy */
        /* .bg-gradient-to-r.from-green-800.to-green-600:not(#filters-sidebar .bg-gradient-to-r) {
                                                                                                                                                                                position: relative;
                                                                                                                                                                                z-index: 40;
                                                                                                                                                                            } */

        #filters-sidebar {
            z-index: 60 !important;
        }

        #filters-overlay {
            z-index: 55 !important;
        }

        #profile-modal,
        #history-modal {
            z-index: 70 !important;
        }

        /* Custom scrollbar */
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

        /* Smooth transitions */
        #filters-sidebar {
            transition: transform 0.3s ease-in-out;
        }

        #filters-overlay {
            transition: opacity 0.3s ease-in-out;
        }
    </style>
@endpush
@push('styles')
    <style>
        /* Export button styles */
        #export-excel,
        #export-pdf,
        #bulk-action {
            transition: all 0.2s ease-in-out;
        }

        #export-excel:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        #export-pdf:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        #bulk-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* Responsive buttons */
        @media (max-width: 768px) {
            .mx-auto .flex.items-center.justify-between {
                flex-direction: column;
                gap: 12px;
                align-items: stretch;
            }

            .mx-auto .flex.items-center.gap-2 {
                justify-content: center;
                flex-wrap: wrap;
            }

            #export-excel,
            #export-pdf,
            #bulk-action {
                flex: 1;
                min-width: 140px;
                justify-content: center;
            }
        }
    </style>
    <!-- Add this CSS to your styles section -->
    <style>
        /* Selection status animations */
        #selection-status,
        #selection-info-bar {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Indeterminate checkbox styling */
        input[type="checkbox"]:indeterminate {
            background-color: #2563eb;
            border-color: #2563eb;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 16 16'%3e%3cpath stroke='white' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 8h8'/%3e%3c/svg%3e");
        }

        /* Selected row highlighting */
        tr.row-selected {
            background-color: #eff6ff !important;
            border-left: 3px solid #3b82f6;
        }

        /* Pulse animation for bulk action button */
        #bulk-action.pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .8;
            }
        }
    </style>
@endpush
