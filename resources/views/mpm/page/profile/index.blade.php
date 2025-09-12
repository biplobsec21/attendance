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
                    {{-- <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                        <div class="flex items-center">
                            <div class="bg-red-100 rounded-full p-3">
                                <i class="fas fa-medical text-red-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Medical</p>
                                <p class="text-2xl font-bold text-gray-900" id="medical-count">0</p>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>

            <!-- Filters Section -->
            <div class=" mx-auto px-4 sm:px-6 lg:px-8 mb-8">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <div class="relative">
                                <input type="text" id="search-input" placeholder="Search by name, army number..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Rank Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rank</label>
                            <select id="rank-filter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">All Ranks</option>
                            </select>
                        </div>

                        <!-- Company Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                            <select id="company-filter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">All Companies</option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="status-filter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="leave">On Leave</option>
                                {{-- <option value="medical">Medical</option> --}}
                                {{-- <option value="inactive">Inactive</option> --}}
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3">
                        <button id="clear-filters"
                            class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </button>
                        <button id="export-excel"
                            class="px-4 py-2 text-green-600 bg-green-100 rounded-lg hover:bg-green-200 transition-colors duration-200">
                            <i class="fas fa-file-excel mr-2"></i>Export Excel
                        </button>
                        <button id="export-pdf"
                            class="px-4 py-2 text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors duration-200">
                            <i class="fas fa-file-pdf mr-2"></i>Export PDF
                        </button>
                        <button id="bulk-action"
                            class="px-4 py-2 text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors duration-200 hidden">
                            <i class="fas fa-edit mr-2"></i>Bulk Actions
                        </button>
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
                                        Rank</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Company</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Joining Date</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Progress</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
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
    <!-- Profile Quick View Modal -->
    <div id="profile-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                onclick="soldierManager.closeModal('profile-modal')"></div>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Profile Quick View</h3>
                        <button id="close-modal" onclick="soldierManager.closeModal('profile-modal')"
                            class="text-gray-400 hover:text-gray-600">
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
@endsection
@push('scripts')
    <!-- JavaScript -->
    <script>
        class SoldierProfileManager {
            constructor() {
                this.filters = {
                    search: '',
                    rank: '',
                    company: '',
                    status: ''
                };
                this.selectedRows = new Set();
                this.soldiers = [];
                this.stats = [];

                this.init();
            }

            init() {
                this.setupEventListeners();
                this.loadData();
                // this.loadFilterOptions();
            }

            setupEventListeners() {
                // Search input with debouncing
                let searchTimeout;
                document.getElementById('search-input').addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.filters.search = e.target.value;
                        this.filterAndRender();
                    }, 300);
                });

                // Filter dropdowns
                ['rank-filter', 'company-filter', 'status-filter'].forEach(id => {
                    document.getElementById(id).addEventListener('change', (e) => {
                        const filterType = id.replace('-filter', '');
                        this.filters[filterType] = e.target.value;
                        console.log('Filter array', this.filters);
                        this.filterAndRender();
                    });
                });

                // Clear filters
                document.getElementById('clear-filters').addEventListener('click', () => {
                    this.clearFilters();
                });

                // Export buttons
                document.getElementById('export-excel').addEventListener('click', () => {
                    this.exportData('excel');
                });

                document.getElementById('export-pdf').addEventListener('click', () => {
                    this.exportData('pdf');
                });

                // Select all checkbox
                document.getElementById('select-all').addEventListener('change', (e) => {
                    this.toggleSelectAll(e.target.checked);
                });

                // Bulk actions
                document.getElementById('bulk-action').addEventListener('click', () => {
                    this.showBulkActions();
                });
            }

            async loadData() {
                this.showLoading(true);

                try {
                    const response = await fetch('{{ route('soldier.index') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load data');
                    }

                    const data = await response.json();
                    this.soldiers = data.data || [];
                    this.stats = data.stats || [];
                    this.filterAndRender();
                    this.updateStats();
                    console.log(this.soldiers);

                } catch (error) {
                    console.error('Error loading data:', error);
                    this.showError('Failed to load soldier data');
                    this.soldiers = [];
                } finally {
                    this.showLoading(false);
                    this.loadFilterOptions();

                }
            }

            async loadFilterOptions() {
                try {
                    // You might want to create separate endpoints for these
                    // For now, we'll extract unique values from loaded data

                    this.populateFilterOptions();
                } catch (error) {
                    console.error('Error loading filter options:', error);
                }
            }

            populateFilterOptions() {
                const rankSelect = document.getElementById('rank-filter');
                const companySelect = document.getElementById('company-filter');

                // Clear existing options except the first one
                rankSelect.innerHTML = '<option value="">All Ranks</option>';
                companySelect.innerHTML = '<option value="">All Companies</option>';

                console.log('After data is loading...', this.soldiers);
                // Extract unique ranks and companies
                const ranks = [...new Set(this.soldiers.map(s => s.rank).filter(Boolean))];
                const companies = [...new Set(this.soldiers.map(s => s.unit).filter(Boolean))];

                ranks.forEach(rank => {
                    const option = document.createElement('option');
                    option.value = rank;
                    option.textContent = rank;
                    rankSelect.appendChild(option);
                });

                companies.forEach(company => {
                    const option = document.createElement('option');
                    option.value = company;
                    option.textContent = company;
                    companySelect.appendChild(option);
                });
            }

            filterAndRender() {
                let filteredSoldiers = this.soldiers;

                // Apply search filter
                if (this.filters.search) {
                    console.log(this.filters.search);
                    const searchTerm = this.filters.search.toLowerCase();
                    filteredSoldiers = filteredSoldiers.filter(soldier =>
                        soldier.name?.toLowerCase().includes(searchTerm) ||
                        soldier.army_no?.toLowerCase().includes(searchTerm)
                    );
                }

                // Apply rank filter
                if (this.filters.rank) {
                    filteredSoldiers = filteredSoldiers.filter(soldier =>
                        soldier.rank === this.filters.rank
                    );
                }

                // Apply company filter
                if (this.filters.company) {
                    filteredSoldiers = filteredSoldiers.filter(soldier =>
                        soldier.unit === this.filters.company
                    );
                }

                // Apply status filter
                if (this.filters.status) {

                    filteredSoldiers = filteredSoldiers.filter(soldier => {
                        const status = this.getAttendanceStatusFromSoldier(soldier, this.filters.status);

                        console.log('the status', status);
                        console.log('the filter status', this.filters.status);
                        return status === this.filters.status;
                    });
                }

                this.renderData(filteredSoldiers);
            }

            getStatusFromSoldier(soldier) {
                console.log("Fiters--", soldier);
                if (soldier.is_leave === true) return 'leave';
                if (soldier.is_sick === true) return 'medical';
                if (soldier.status === true) return 'active';
                return 'inactive';
            }
            getAttendanceStatusFromSoldier(soldier, types) {
                console.log("Fiters--", soldier);
                if (types == "leave") {
                    console.log('here i am ');
                    if (soldier.is_leave) return 'leave';

                }
                if (types == "active") {
                    if (!soldier.is_leave) return 'active';
                }

            }


            renderData(soldiers) {
                const tbody = document.getElementById('soldiers-tbody');
                const mobileCards = document.getElementById('mobile-cards');

                if (soldiers.length === 0) {
                    document.getElementById('empty-state').classList.remove('hidden');
                    tbody.innerHTML = '';
                    mobileCards.innerHTML = '';
                    return;
                }

                document.getElementById('empty-state').classList.add('hidden');

                // Render desktop table
                tbody.innerHTML = soldiers.map(soldier => this.renderTableRow(soldier)).join('');

                // Render mobile cards
                mobileCards.innerHTML = soldiers.map(soldier => this.renderMobileCard(soldier)).join('');

                this.attachRowEventListeners();
            }

            renderTableRow(soldier) {
                const status = this.getStatusFromSoldier(soldier);
                const statusBadge = this.getStatusBadge(status);
                const progress = this.calculateProgress(soldier);
                const imageUrl = soldier.image ? `/storage/${soldier.image}` : '/images/default-avatar.png';
                const defaultAvatar = "{{ asset('images/default-avatar.png') }}";

                return `
                <tr class="hover:bg-gray-50 transition-colors duration-150" data-soldier-id="${soldier.id}">
                    <td class="px-6 py-4">
                        <input type="checkbox" class="row-select rounded border-gray-300 text-green-600 focus:ring-green-500"
                               value="${soldier.id}">
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200"
                                 src="${defaultAvatar}"
                                 alt="${soldier.name || 'Soldier'}"
                                 onerror="this.src='/images/default-avatar.png'">
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${soldier.name || 'N/A'}</div>
                                <div class="text-sm text-gray-500">Army #${soldier.army_no || 'N/A'}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            ${soldier.rank|| 'N/A'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">${soldier.unit || 'N/A'}</td>
                    <td class="px-6 py-4">${statusBadge}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${this.formatDate(soldier.joining_date)}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-${progress.color}-600 h-2 rounded-full" style="width: ${progress.percentage}%"></div>
                            </div>
                            <span class="text-xs text-gray-600">${progress.percentage}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium space-x-2">
                        ${this.renderActionButtons(soldier)}
                    </td>
                </tr>
            `;
            }

            renderMobileCard(soldier) {
                const status = this.getStatusFromSoldier(soldier);
                const statusBadge = this.getStatusBadge(status);
                const progress = this.calculateProgress(soldier);
                const imageUrl = soldier.image ? `/storage/${soldier.image}` : '/images/default-avatar.png';

                return `
                <div class="bg-white border border-gray-200 rounded-lg p-4 cursor-pointer hover:shadow-md transition-shadow duration-200"
                     data-soldier-id="${soldier.id}">
                    <div class="flex items-center space-x-4">
                        <input type="checkbox" class="row-select rounded border-gray-300 text-green-600 focus:ring-green-500"
                               value="${soldier.id}">
                        <img class="h-16 w-16 rounded-full object-cover border-2 border-gray-200"
                             src="${imageUrl}"
                             alt="${soldier.full_name || 'Soldier'}"
                             onerror="this.src='/images/default-avatar.png'">
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-900">${soldier.full_name || 'N/A'}</h3>
                            <p class="text-sm text-gray-500">Army #${soldier.army_no || 'N/A'}</p>
                            <div class="flex items-center space-x-2 mt-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    ${soldier.rank?.name || 'N/A'}
                                </span>
                                ${statusBadge}
                            </div>
                            <div class="flex items-center mt-2">
                                <div class="w-20 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-${progress.color}-600 h-2 rounded-full" style="width: ${progress.percentage}%"></div>
                                </div>
                                <span class="text-xs text-gray-600">${progress.percentage}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-2">
                        ${this.renderActionButtons(soldier, true)}
                    </div>
                </div>
            `;
            }

            renderActionButtons(soldier, isMobile = false) {
                const buttonClass = isMobile ? 'flex-1 px-3 py-2 text-sm' : 'px-2 py-1 text-xs';

                return `
                <button onclick="soldierManager.viewProfile(${soldier.id})"
                        class="${buttonClass} bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors duration-200">
                    <i class="fas fa-eye ${isMobile ? 'mr-1' : ''}"></i>${isMobile ? 'View' : ''}
                </button>
                <button onclick="window.location.href='/army/${soldier.id}/service'"
                        class="${buttonClass} bg-green-100 text-green-700 rounded hover:bg-green-200 transition-colors duration-200">
                    <i class="fas fa-edit ${isMobile ? 'mr-1' : ''}"></i>${isMobile ? 'Edit' : ''}
                </button>
                <button onclick="soldierManager.deleteProfile(${soldier.id})"
                        class="${buttonClass} bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors duration-200">
                    <i class="fas fa-trash ${isMobile ? 'mr-1' : ''}"></i>${isMobile ? 'Delete' : ''}
                </button>
            `;
            }

            attachRowEventListeners() {
                // Row selection
                document.querySelectorAll('.row-select').forEach(checkbox => {
                    checkbox.addEventListener('change', (e) => {
                        const soldierId = e.target.value;
                        if (e.target.checked) {
                            this.selectedRows.add(soldierId);
                        } else {
                            this.selectedRows.delete(soldierId);
                        }
                        this.updateBulkActionButton();
                    });
                });

                // Quick view on row click (desktop)
                document.querySelectorAll('tr[data-soldier-id]').forEach(row => {
                    row.addEventListener('click', (e) => {
                        if (!e.target.matches('input, button, a, i')) {
                            const soldierId = row.dataset.soldierId;
                            this.viewProfile(soldierId);
                        }
                    });
                });

                // Quick view on card click (mobile)
                document.querySelectorAll('div[data-soldier-id]').forEach(card => {
                    card.addEventListener('click', (e) => {
                        if (!e.target.matches('input, button, a, i')) {
                            const soldierId = card.dataset.soldierId;
                            this.viewProfile(soldierId);
                        }
                    });
                });
            }

            getStatusBadge(status) {
                const badges = {
                    active: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>',
                    leave: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">On Leave</span>',
                    medical: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Medical</span>',
                    inactive: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>'
                };

                return badges[status] || badges.inactive;
            }

            calculateProgress(soldier) {
                const completedSteps = [
                    soldier.personal_completed,
                    soldier.service_completed,
                    soldier.qualifications_completed,
                    soldier.medical_completed
                ].filter(Boolean).length;

                const percentage = Math.round((completedSteps / 4) * 100);
                let color = 'red';

                if (percentage >= 80) color = 'green';
                else if (percentage >= 50) color = 'yellow';

                return {
                    percentage,
                    color
                };
            }

            formatDate(dateString) {
                if (!dateString) return 'N/A';
                return new Date(dateString).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }

            async viewProfile(soldierId) {
                try {
                    // Show loading in modal
                    this.showModal('Profile Quick View',
                        '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div><span class="ml-3">Loading profile...</span></div>'
                    );

                    // Fetch soldier data
                    const response = await fetch(`/army/${soldierId}/profile`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load profile');
                    }

                    const soldier = await response.json();

                    // Generate modal content
                    const modalContent = this.generateProfileModalContent(soldier);

                    // Update modal with actual content
                    document.getElementById('modal-content').innerHTML = modalContent;

                } catch (error) {
                    console.error('Error viewing profile:', error);

                    // Show error in modal or fallback to redirect
                    const errorContent = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-red-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Failed to Load Profile</h3>
                <p class="text-gray-600 mb-4">Unable to load profile details in quick view.</p>
                <div class="space-x-4">
                    <button onclick="window.location.href='/army/${soldierId}/details'"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        View Full Profile
                    </button>
                    <button onclick="soldierManager.closeModal('profile-modal')"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                        Close
                    </button>
                </div>
            </div>
        `;

                    document.getElementById('modal-content').innerHTML = errorContent;
                }
            }

            async deleteProfile(soldierId) {
                if (!confirm('Are you sure you want to delete this soldier profile? This action cannot be undone.')) {
                    return;
                }

                try {
                    const response = await fetch(`/army/${soldierId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        this.showSuccess('Profile deleted successfully');
                        this.loadData();
                    } else {
                        throw new Error('Delete failed');
                    }
                } catch (error) {
                    console.error('Error deleting profile:', error);
                    this.showError('Failed to delete profile');
                }
            }

            clearFilters() {
                this.filters = {
                    search: '',
                    rank: '',
                    company: '',
                    status: ''
                };

                document.getElementById('search-input').value = '';
                document.getElementById('rank-filter').value = '';
                document.getElementById('company-filter').value = '';
                document.getElementById('status-filter').value = '';

                this.filterAndRender();
            }

            exportData(format) {
                const params = new URLSearchParams({
                    ...this.filters,
                    format: format,
                    selected: Array.from(this.selectedRows).join(',')
                });

                // You'll need to create export routes
                window.open(`/army/export?${params}`, '_blank');
            }

            toggleSelectAll(checked) {
                document.querySelectorAll('.row-select').forEach(checkbox => {
                    checkbox.checked = checked;
                    const soldierId = checkbox.value;
                    if (checked) {
                        this.selectedRows.add(soldierId);
                    } else {
                        this.selectedRows.delete(soldierId);
                    }
                });
                this.updateBulkActionButton();
            }

            updateBulkActionButton() {
                const bulkButton = document.getElementById('bulk-action');
                if (this.selectedRows.size > 0) {
                    bulkButton.classList.remove('hidden');
                    bulkButton.innerHTML = `<i class="fas fa-edit mr-2"></i>Bulk Actions (${this.selectedRows.size})`;
                } else {
                    bulkButton.classList.add('hidden');
                }
            }

            showBulkActions() {
                const actions = [{
                        label: 'Export Selected',
                        action: 'export'
                    },
                    {
                        label: 'Delete Selected',
                        action: 'delete',
                        dangerous: true
                    }
                ];

                let html = '<div class="space-y-2">';
                actions.forEach(action => {
                    const colorClass = action.dangerous ? 'text-red-600 hover:bg-red-50' :
                        'text-gray-700 hover:bg-gray-100';
                    html += `
                    <button onclick="soldierManager.performBulkAction('${action.action}')"
                            class="w-full text-left px-4 py-2 text-sm ${colorClass} rounded-md transition-colors duration-200">
                        ${action.label}
                    </button>
                `;
                });
                html += '</div>';

                this.showModal('Bulk Actions', html);
            }

            performBulkAction(action) {
                const count = this.selectedRows.size;

                switch (action) {
                    case 'status':
                        const newStatus = prompt('Enter new status (active/leave/medical/inactive):');
                        if (newStatus && ['active', 'leave', 'medical', 'inactive'].includes(newStatus)) {
                            this.bulkUpdateStatus(newStatus);
                        }
                        break;

                    case 'export':
                        this.exportSelected();
                        break;

                    case 'delete':
                        if (confirm(
                                `Are you sure you want to delete ${count} soldier profile(s)? This action cannot be undone.`
                            )) {
                            this.bulkDelete();
                        }
                        break;
                }

                this.closeModal('profile-modal');
            }

            async bulkUpdateStatus(status) {
                try {
                    const response = await fetch('/army/bulk-update-status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            soldier_ids: Array.from(this.selectedRows),
                            status: status
                        })
                    });

                    if (response.ok) {
                        this.showSuccess(`Status updated for ${this.selectedRows.size} soldiers`);
                        this.selectedRows.clear();
                        this.loadData();
                    } else {
                        throw new Error('Bulk update failed');
                    }
                } catch (error) {
                    console.error('Error in bulk update:', error);
                    this.showError('Failed to update status');
                }
            }
            generateProfileModalContent(soldier) {
                const status = this.getStatusFromSoldier(soldier);
                const statusBadge = this.getStatusBadge(status);
                const progress = this.calculateProgress(soldier);
                const imageUrl = soldier.image ? `/storage/${soldier.image}` : '';

                return `
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex items-center space-x-4 pb-4 border-b">
                <div class="h-20 w-20 rounded-full border-2 border-gray-200 flex items-center justify-center bg-gray-100 overflow-hidden">
                    ${imageUrl ?
                        `<img class="h-full w-full rounded-full object-cover" src="${imageUrl}" alt="${soldier.full_name || 'Soldier'}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             <div class="hidden items-center justify-center h-full w-full text-gray-400" style="display: none;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 <i class="fas fa-user text-2xl"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             </div>` :
                        `<div class="flex items-center justify-center h-full w-full text-gray-400">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 <i class="fas fa-user text-2xl"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             </div>`
                    }
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900">${soldier.full_name || 'N/A'}</h2>
                    <p class="text-gray-600">Army #${soldier.army_no || 'N/A'}</p>
                    <div class="flex items-center space-x-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            ${soldier.rank?.name || 'N/A'}
                        </span>
                        ${statusBadge}
                    </div>
                </div>
            </div>

            <!-- Quick Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Personal Info</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Unit:</span>
                            <span class="font-medium">${soldier.company?.name || soldier.unit || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Joining Date:</span>
                            <span class="font-medium">${this.formatDate(soldier.joining_date)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Blood Group:</span>
                            <span class="font-medium">${soldier.blood_group || 'N/A'}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Contact Info</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phone:</span>
                            <span class="font-medium">${soldier.phone || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">${soldier.email || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Emergency Contact:</span>
                            <span class="font-medium">${soldier.emergency_contact || 'N/A'}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Section -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-500 mb-3">Profile Completion</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">Overall Progress</span>
                        <span class="text-sm font-medium">${progress.percentage}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-${progress.color}-600 h-2 rounded-full transition-all duration-500"
                             style="width: ${progress.percentage}%"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-${soldier.personal_completed ? 'check-circle text-green-500' : 'circle text-gray-400'}"></i>
                            <span>Personal Info</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-${soldier.service_completed ? 'check-circle text-green-500' : 'circle text-gray-400'}"></i>
                            <span>Service Record</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-${soldier.qualifications_completed ? 'check-circle text-green-500' : 'circle text-gray-400'}"></i>
                            <span>Qualifications</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-${soldier.medical_completed ? 'check-circle text-green-500' : 'circle text-gray-400'}"></i>
                            <span>Medical Info</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-3 pt-4 border-t">
                <button onclick="window.location.href='/army/${soldier.id}/details'"
                        class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <i class="fas fa-eye mr-2"></i>View Full Profile
                </button>
                <button onclick="window.location.href='/army/${soldier.id}/service'"
                        class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit Profile
                </button>
                <button onclick="soldierManager.closeModal('profile-modal')"
                        class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    Close
                </button>
            </div>
        </div>
    `;
            }
            async bulkDelete() {
                try {
                    // Get CSRF token safely
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfMeta ? csrfMeta.content : null;

                    if (!csrfToken) {
                        console.error('CSRF token not found!');
                        this.showError('CSRF token missing. Cannot perform delete.');
                        return;
                    }

                    // Perform fetch request
                    const response = await fetch('{{ route('soldier.bulkDelete') }}', {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            soldier_ids: Array.from(this.selectedRows)
                        })
                    });

                    if (response.ok) {
                        const count = this.selectedRows.size;
                        this.selectedRows.clear();
                        this.loadData();
                        this.showSuccess(`${count} profiles deleted successfully`);

                        // Close modal safely if it exists
                        const modal = document.getElementById('bulkActionModal');
                        if (modal) {
                            modal.classList.remove('open');
                        }
                    } else {
                        const errorData = await response.json().catch(() => ({}));
                        console.error('Bulk delete failed:', errorData);
                        this.showError('Failed to delete profiles');
                    }
                } catch (error) {
                    console.error('Error in bulk delete:', error);
                    this.showError('Failed to delete profiles');
                }
            }


            exportSelected() {
                const params = new URLSearchParams({
                    selected: Array.from(this.selectedRows).join(','),
                    format: 'excel'
                });

                window.open(`/army/export?${params}`, '_blank');
            }

            closeModal(modalId) {
                // document.getElementById(modalId).classList.add('hidden');
                const modal = document.getElementById(modalId);
                console.log(modalId);
                if (!modal) return; // safely exit if not found
                modal.classList.add('hidden');
            }

            showModal(title, content) {
                const modal = document.getElementById('profile-modal');
                document.querySelector('#profile-modal h3').textContent = title;
                document.getElementById('modal-content').innerHTML = content;
                modal.classList.remove('hidden');
            }

            showLoading(show) {
                const loadingState = document.getElementById('loading-state');
                if (show) {
                    loadingState.classList.remove('hidden');
                } else {
                    loadingState.classList.add('hidden');
                }
            }

            updateStats() {

                const stats = {
                    total: this.soldiers.length,
                    active: this.soldiers.filter(s => this.getStatusFromSoldier(s) === 'active').length,
                    leave: this.soldiers.filter(s => this.getStatusFromSoldier(s) === 'leave').length,
                    // medical: this.soldiers.filter(s => this.getStatusFromSoldier(s) === 'medical').length,
                    // inactive: this.soldiers.filter(s => this.getStatusFromSoldier(s) === 'inactive').length
                };
                console.log("Here is the stats", stats);

                document.getElementById('total-count').textContent = stats.total;
                document.getElementById('active-count').textContent = this.stats.active;
                document.getElementById('leave-count').textContent = this.stats.leave;
                // document.getElementById('medical-count').textContent = stats.medical;
            }

            showSuccess(message) {
                this.showToast(message, 'success');
            }

            showError(message) {
                this.showToast(message, 'error');
            }

            showToast(message, type) {
                const toast = document.createElement('div');
                const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
                const icon = type === 'success' ? 'check' : 'exclamation-triangle';

                toast.className =
                    `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
                toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${icon} mr-2"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

                document.body.appendChild(toast);

                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 100);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            }
        }

        // Initialize the manager when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            window.soldierManager = new SoldierProfileManager();
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Focus search with Ctrl+F
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                document.getElementById('search-input').focus();
            }

            // Add new profile with Ctrl+N
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                window.location.href = '{{ route('soldier.personalForm') }}';
            }

            // Refresh data with F5 or Ctrl+R
            if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                e.preventDefault();
                soldierManager.loadData();
            }

            // Close modal with Escape
            if (e.key === 'Escape') {
                soldierManager.closeModal('profile-modal');
            }
        });
    </script>
@endpush
@push('styles')
    <!-- Custom CSS for additional styling -->
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Smooth transitions */
        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Focus styles for accessibility */
        input:focus,
        select:focus,
        button:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.5);
        }

        /* Table row hover effect */
        tbody tr:hover {
            background-color: rgba(249, 250, 251, 1);
        }

        /* Progress bar animation */
        .progress-bar {
            transition: width 0.5s ease-in-out;
        }

        /* Loading animation */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }



        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
@endpush
