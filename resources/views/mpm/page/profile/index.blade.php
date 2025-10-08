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

                <!-- Filters Section -->
                <div class=" mx-auto px-4 sm:px-6 lg:px-8 mb-8">
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">RK</label>
                                <select id="rank-filter"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">All RK</option>
                                </select>
                            </div>

                            <!-- Company Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Coy</label>
                                <select id="company-filter"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">All Coy</option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status-filter"
                                    class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select id="status-filter"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="leave">On Leave</option>
                                </select>
                            </div>

                            {{-- Skill --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Skills</label>
                                <select id="skill-filter"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">All Skills</option>
                                </select>
                            </div>

                            {{-- Courses --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Courses</label>
                                <select id="course-filter"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">All Courses</option>
                                </select>
                            </div>

                            {{-- Cadres --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cadres</label>
                                <select id="cadre-filter"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">All Cadres</option>
                                </select>
                            </div>

                            {{-- ERE Filter --}}
                            <div>
                                <label for="ere-filter" class="block text-sm font-medium text-gray-700 mb-2">ERE
                                    Status</label>
                                <select id="ere-filter"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="all">All Soldiers</option>
                                    <option value="with-ere">With ERE</option>
                                    <option value="without-ere">Without ERE</option>
                                </select>
                            </div>

                            {{-- ATT Filter --}}
                            <div>
                                <label for="att-filter" class="block text-sm font-medium text-gray-700 mb-2">ATT</label>
                                <select id="att-filter"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">All ATT</option>
                                </select>
                            </div>
                            {{-- Education Filter --}}
                            <div>
                                <label for="education-filter"
                                    class="block text-sm font-medium text-gray-700 mb-2">Education</label>
                                <select id="education-filter"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">All Education</option>
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
                                class="hidden px-4 py-2 text-green-600 bg-green-100 rounded-lg hover:bg-green-200 transition-colors duration-200">
                                <i class="fas fa-file-excel mr-2"></i>Export Excel
                            </button>
                            <button id="export-pdf"
                                class="hidden px-4 py-2 text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors duration-200">
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
        <!-- Profile Quick View Modal //  make it as a common modal //-->
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
    @endsection

    @push('scripts')
        <script>
            const routes = {
                getAllSoldiers: "{{ route('soldier.getAllData') }}",
                delete: "{{ route('soldier.destroy', ['soldier' => ':id']) }}", // placeholder
                edit: "{{ route('soldier.personalForm', ['profile' => ':id']) }}",
                bulkDelete: "{{ route('soldier.bulkDelete') }}",
                view: "{{ route('soldier.details', ['id' => ':id']) }}",
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
            });
        </script>
        <script type="module" src="{{ asset('asset/js/soldiers/init.js') }}"></script>
    @endpush

    @push('styles')
        <link rel="stylesheet" href="{{ asset('asset/css/profile.css') }}">
    @endpush
