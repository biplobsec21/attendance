@extends('mpm.layouts.app')

@section('title', 'Course/Cadre/Ex-Area Lists Manager')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs_auto()" />

        <!-- Enhanced error display -->
        @if ($errors->any())
            <div class="mb-4">
                <div class="bg-red-50 border-l-4 border-red-500 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                There were errors with your submission:
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @include('mpm.components.alerts')

        <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Military Course/Cadre/Ex-Area Management</h1>
                <p class="text-gray-600">Manage soldier course, cadre, and ex-area assignments</p>
            </div>

            <!-- Action Buttons -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('coursecadremanager.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Assignment
                    </a>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <input type="text" id="searchInput"
                            placeholder="Search by soldier name, rank, company, or course..."
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <button id="clearSearch"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 hidden">
                        Clear
                    </button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto">
                        <button
                            class="tab-button active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600 whitespace-nowrap"
                            data-tab="current-courses" data-original-count="{{ $currentCourses->count() }}">
                            Current Courses
                            <span class="ml-2 bg-blue-100 text-blue-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $currentCourses->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                            data-tab="previous-courses" data-original-count="{{ $previousCourses->count() }}">
                            Previous Courses
                            <span class="ml-2 bg-gray-100 text-gray-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $previousCourses->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                            data-tab="current-cadres" data-original-count="{{ $currentCadres->count() }}">
                            Current Cadres
                            <span class="ml-2 bg-green-100 text-green-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $currentCadres->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                            data-tab="previous-cadres" data-original-count="{{ $previousCadres->count() }}">
                            Previous Cadres
                            <span class="ml-2 bg-gray-100 text-gray-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $previousCadres->count() }}
                            </span>
                        </button>
                        <!-- Ex-Areas Tabs -->
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                            data-tab="current-ex-areas" data-original-count="{{ $currentExAreas->count() }}">
                            Current Ex-Areas
                            <span class="ml-2 bg-purple-100 text-purple-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $currentExAreas->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap"
                            data-tab="previous-ex-areas" data-original-count="{{ $previousExAreas->count() }}">
                            Previous Ex-Areas
                            <span class="ml-2 bg-gray-100 text-gray-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $previousExAreas->count() }}
                            </span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Include Course Tabs -->
            @include('mpm.page.cadre_course.partials.courses-tabs')

            <!-- Include Cadre Tabs -->
            @include('mpm.page.cadre_course.partials.cadres-tabs')

            <!-- Include Ex-Areas Tabs -->
            @include('mpm.page.cadre_course.partials.ex-areas-tabs')
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <div>
                    <p class="text-lg font-medium text-gray-900">Loading...</p>
                    <p class="text-sm text-gray-500">Please wait</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Modals -->
    @include('mpm.page.cadre_course.partials.modals')
@endsection
@push('styles')
    <style>
        .recommendation-badge {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            cursor: help;
        }

        .recommendation-badge:hover {
            background-color: #dbeafe !important;
        }
    </style>
@endpush
@push('scripts')
    <script>
        // Global loading overlay functions
        function showLoadingOverlay() {
            document.getElementById('loadingOverlay').classList.remove('hidden');
        }

        function hideLoadingOverlay() {
            document.getElementById('loadingOverlay').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');

            // Get the active tab from localStorage or default to 'current-courses'
            const activeTab = localStorage.getItem('activeCourseCadreTab') || 'current-courses';

            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            // Function to activate a tab
            function activateTab(tabName) {
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });

                const activeButton = Array.from(tabButtons).find(btn => btn.getAttribute('data-tab') === tabName);
                if (activeButton) {
                    activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
                    activeButton.classList.remove('border-transparent', 'text-gray-500');
                }

                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });

                const targetContent = document.getElementById(tabName + '-tab');
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }

                localStorage.setItem('activeCourseCadreTab', tabName);

                if (searchInput && searchInput.value) {
                    const searchTerm = searchInput.value.toLowerCase().trim();
                    if (searchTerm) {
                        performSearch(searchTerm);
                    }
                }
            }

            // Activate the stored tab on page load
            activateTab(activeTab);

            // Add click event listeners to tab buttons
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetTab = button.getAttribute('data-tab');
                    activateTab(targetTab);
                });
            });

            // FIXED SEARCH FUNCTIONALITY
            function performSearch(searchTerm) {
                const allTabs = ['current-courses', 'previous-courses', 'current-cadres', 'previous-cadres',
                    'current-ex-areas', 'previous-ex-areas'
                ];

                allTabs.forEach(tabName => {
                    const tableBodyId = tabName.replace(/-/g, '').charAt(0).toUpperCase() +
                        tabName.replace(/-/g, '').slice(1) + 'TableBody';
                    const tableBody = document.getElementById(tableBodyId);

                    if (!tableBody) return;

                    const rows = tableBody.querySelectorAll('tr.searchable-row');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const soldierName = row.querySelector('.soldier-name')?.textContent
                            ?.toLowerCase() || '';
                        const soldierDetails = row.querySelector('.soldier-details')?.textContent
                            ?.toLowerCase() || '';
                        const assignmentName = row.querySelector('.assignment-name')?.textContent
                            ?.toLowerCase() || '';
                        const assignmentRemarks = row.querySelector('.assignment-remarks')
                            ?.textContent?.toLowerCase() || '';

                        const searchableText =
                            `${soldierName} ${soldierDetails} ${assignmentName} ${assignmentRemarks}`;

                        if (searchableText.includes(searchTerm)) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Handle empty state
                    const emptyRow = tableBody.querySelector('tr.empty-row');
                    if (emptyRow) {
                        if (visibleCount === 0 && rows.length > 0) {
                            emptyRow.style.display = '';
                            emptyRow.querySelector('td').innerHTML = `
                                <div class="text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">No matching results found</p>
                                    <p class="text-sm">Try adjusting your search terms</p>
                                </div>
                            `;
                        } else {
                            emptyRow.style.display = 'none';
                        }
                    }
                });

                updateTabCounts(searchTerm);
            }

            function updateTabCounts(searchTerm) {
                tabButtons.forEach(button => {
                    const tabName = button.getAttribute('data-tab');
                    const countSpan = button.querySelector(
                        '.bg-blue-100, .bg-green-100, .bg-purple-100, .bg-gray-100');
                    const originalCount = button.getAttribute('data-original-count');

                    if (searchTerm) {
                        const tableBodyId = tabName.replace(/-/g, '').charAt(0).toUpperCase() +
                            tabName.replace(/-/g, '').slice(1) + 'TableBody';
                        const tableBody = document.getElementById(tableBodyId);

                        if (tableBody) {
                            const visibleRows = Array.from(tableBody.querySelectorAll('tr.searchable-row'))
                                .filter(row =>
                                    row.style.display !== 'none'
                                );
                            countSpan.textContent = visibleRows.length;
                        }
                    } else {
                        countSpan.textContent = originalCount;
                    }
                });
            }

            function clearSearchResults() {
                const allTabs = ['current-courses', 'previous-courses', 'current-cadres', 'previous-cadres',
                    'current-ex-areas', 'previous-ex-areas'
                ];

                allTabs.forEach(tabName => {
                    const tableBodyId = tabName.replace(/-/g, '').charAt(0).toUpperCase() +
                        tabName.replace(/-/g, '').slice(1) + 'TableBody';
                    const tableBody = document.getElementById(tableBodyId);

                    if (!tableBody) return;

                    const rows = tableBody.querySelectorAll('tr');
                    rows.forEach(row => {
                        row.style.display = '';
                    });

                    const emptyRow = tableBody.querySelector('tr.empty-row');
                    if (emptyRow) {
                        const dataRows = tableBody.querySelectorAll('tr.searchable-row');
                        if (dataRows.length === 0) {
                            emptyRow.style.display = '';
                        } else {
                            emptyRow.style.display = 'none';
                        }
                    }
                });

                updateTabCounts('');
            }

            // Search input event listener
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();

                    if (searchTerm) {
                        if (clearSearch) clearSearch.classList.remove('hidden');
                        performSearch(searchTerm);
                    } else {
                        if (clearSearch) clearSearch.classList.add('hidden');
                        clearSearchResults();
                    }
                });
            }

            // Clear search functionality
            if (clearSearch) {
                clearSearch.addEventListener('click', function() {
                    if (searchInput) searchInput.value = '';
                    clearSearch.classList.add('hidden');
                    clearSearchResults();
                });
            }

            // Checkbox handlers
            setupCheckboxHandlers();
        });

        function setupCheckboxHandlers() {
            // Course checkboxes
            const selectAllCoursesCheckbox = document.getElementById('selectAllCourses');
            const courseCheckboxes = document.querySelectorAll('.course-checkbox');
            const completeCoursesButtonContainer = document.getElementById('completeCoursesButtonContainer');

            if (selectAllCoursesCheckbox) {
                selectAllCoursesCheckbox.addEventListener('change', function() {
                    courseCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    toggleCompleteCoursesButton();
                });
            }

            courseCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(courseCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(courseCheckboxes).some(cb => cb.checked);

                    if (selectAllCoursesCheckbox) {
                        selectAllCoursesCheckbox.checked = allChecked;
                        selectAllCoursesCheckbox.indeterminate = someChecked && !allChecked;
                    }

                    toggleCompleteCoursesButton();
                });
            });

            function toggleCompleteCoursesButton() {
                const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');
                if (completeCoursesButtonContainer) {
                    completeCoursesButtonContainer.classList.toggle('hidden', checkedBoxes.length === 0);
                }
            }

            // Cadre checkboxes
            const selectAllCadresCheckbox = document.getElementById('selectAllCadres');
            const cadreCheckboxes = document.querySelectorAll('.cadre-checkbox');
            const completeCadresButtonContainer = document.getElementById('completeCadresButtonContainer');

            if (selectAllCadresCheckbox) {
                selectAllCadresCheckbox.addEventListener('change', function() {
                    cadreCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    toggleCompleteCadresButton();
                });
            }

            cadreCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(cadreCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(cadreCheckboxes).some(cb => cb.checked);

                    if (selectAllCadresCheckbox) {
                        selectAllCadresCheckbox.checked = allChecked;
                        selectAllCadresCheckbox.indeterminate = someChecked && !allChecked;
                    }

                    toggleCompleteCadresButton();
                });
            });

            function toggleCompleteCadresButton() {
                const checkedBoxes = document.querySelectorAll('.cadre-checkbox:checked');
                if (completeCadresButtonContainer) {
                    completeCadresButtonContainer.classList.toggle('hidden', checkedBoxes.length === 0);
                }
            }

            // Ex-Area checkboxes
            const selectAllExAreasCheckbox = document.getElementById('selectAllExAreas');
            const exAreaCheckboxes = document.querySelectorAll('.ex-area-checkbox');
            const completeExAreasButtonContainer = document.getElementById('completeExAreasButtonContainer');

            if (selectAllExAreasCheckbox) {
                selectAllExAreasCheckbox.addEventListener('change', function() {
                    exAreaCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    toggleCompleteExAreasButton();
                });
            }

            exAreaCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(exAreaCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(exAreaCheckboxes).some(cb => cb.checked);

                    if (selectAllExAreasCheckbox) {
                        selectAllExAreasCheckbox.checked = allChecked;
                        selectAllExAreasCheckbox.indeterminate = someChecked && !allChecked;
                    }

                    toggleCompleteExAreasButton();
                });
            });

            function toggleCompleteExAreasButton() {
                const checkedBoxes = document.querySelectorAll('.ex-area-checkbox:checked');
                if (completeExAreasButtonContainer) {
                    completeExAreasButtonContainer.classList.toggle('hidden', checkedBoxes.length === 0);
                }
            }
        }

        // Modal and action functions
        function openEditModal(type, id) {
            showLoadingOverlay();

            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('editAssignmentForm').action = `/coursecadremanager/${type}/${id}`;
            document.getElementById('editAssignmentId').value = id;
            document.getElementById('editAssignmentType').value = type;

            document.getElementById('editModalTitle').textContent =
                `Edit ${type === 'course' ? 'Course' : type === 'cadre' ? 'Cadre' : 'Ex-Area'} Assignment`;

            const courseSection = document.getElementById('editCourseSection');
            const cadreSection = document.getElementById('editCadreSection');
            const exAreaSection = document.getElementById('editExAreaSection');
            const courseSelect = document.getElementById('editCourseId');
            const cadreSelect = document.getElementById('editCadreId');
            const exAreaSelect = document.getElementById('editExAreaId');

            courseSection.classList.add('hidden');
            cadreSection.classList.add('hidden');
            exAreaSection.classList.add('hidden');

            courseSelect.removeAttribute('required');
            cadreSelect.removeAttribute('required');
            exAreaSelect.removeAttribute('required');

            if (type === 'course') {
                courseSection.classList.remove('hidden');
                courseSelect.setAttribute('required', 'required');
            } else if (type === 'cadre') {
                cadreSection.classList.remove('hidden');
                cadreSelect.setAttribute('required', 'required');
            } else {
                exAreaSection.classList.remove('hidden');
                exAreaSelect.setAttribute('required', 'required');
            }

            document.getElementById('editAssignmentTypeDisplay').textContent = 'Loading...';
            document.getElementById('editStartDate').value = '';
            document.getElementById('editEndDate').value = '';
            document.getElementById('editNote').value = '';
            document.getElementById('editSoldierId').innerHTML = '<option value="">Loading...</option>';

            fetch(`/coursecadremanager/${type}/${id}/edit-data`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('editAssignmentTypeDisplay').textContent =
                        `${type === 'course' ? '📚 Course' : type === 'cadre' ? '👥 Cadre' : '🗺️ Ex-Area'} Assignment`;

                    document.getElementById('editStartDate').value = data.start_date;
                    document.getElementById('editEndDate').value = data.end_date || '';
                    document.getElementById('editNote').value = data.remarks || '';

                    if (type === 'course') {
                        populateDropdown('editCourseId', data.courses, data.course_id);
                    } else if (type === 'cadre') {
                        populateDropdown('editCadreId', data.cadres, data.cadre_id);
                    } else {
                        populateDropdown('editExAreaId', data.ex_areas, data.ex_area_id);
                    }

                    populateSoldierDropdown('editSoldierId', data.soldiers, data.soldier_id, data
                        .completed_today_soldiers);

                    hideLoadingOverlay();
                    document.getElementById('editAssignmentModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching assignment data:', error);
                    hideLoadingOverlay();
                    showError('Failed to load assignment data. Please try again.');
                });
        }

        function closeEditModal() {
            document.getElementById('editAssignmentForm').reset();
            document.getElementById('editAssignmentModal').classList.add('hidden');
        }

        function populateDropdown(selectId, options, selectedValue) {
            const select = document.getElementById(selectId);
            select.innerHTML = '';

            options.forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option.id;
                optionElement.textContent = option.name;
                optionElement.selected = option.id == selectedValue;
                select.appendChild(optionElement);
            });
        }

        function populateSoldierDropdown(selectId, soldiers, selectedValue, completedTodaySoldiers) {
            const select = document.getElementById(selectId);
            select.innerHTML = '';

            const availableSoldiers = soldiers.filter(s => !s.has_active_assignments || s.id == selectedValue);
            const assignedSoldiers = soldiers.filter(s => s.has_active_assignments && s.id != selectedValue);

            if (availableSoldiers.length > 0) {
                const optgroup = document.createElement('optgroup');
                optgroup.label = 'Available Soldiers';

                availableSoldiers.forEach(soldier => {
                    const option = document.createElement('option');
                    option.value = soldier.id;
                    option.textContent =
                        `${soldier.full_name} (${soldier.army_no}) - ${soldier.rank.name}, ${soldier.company.name}`;
                    option.selected = soldier.id == selectedValue;

                    if (completedTodaySoldiers.includes(soldier.id)) {
                        option.textContent += ' [Completed Today]';
                    }

                    optgroup.appendChild(option);
                });

                select.appendChild(optgroup);
            }

            if (assignedSoldiers.length > 0) {
                const optgroup = document.createElement('optgroup');
                optgroup.label = 'Assigned Soldiers (Unavailable)';
                optgroup.disabled = true;

                assignedSoldiers.forEach(soldier => {
                    const option = document.createElement('option');
                    option.value = soldier.id;
                    option.textContent =
                        `${soldier.full_name} (${soldier.army_no}) - ${soldier.rank.name}, ${soldier.company.name}`;
                    option.disabled = true;
                    optgroup.appendChild(option);
                });

                select.appendChild(optgroup);
            }
        }

        function completeCourse(id, soldierName) {
            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('completeCourseForm').action = `/coursecadremanager/course/${id}/complete`;
            document.getElementById('soldierNameCompleteCourse').textContent = soldierName;
            document.getElementById('completeCourseNote').value = '';
            document.getElementById('completeCourseRecommendation').value = '';
            document.getElementById('completeCourseModal').classList.remove('hidden');
        }

        function closeCompleteCourseModal() {
            document.querySelector('#completeCourseModal h3').textContent = 'Complete Course';
            document.querySelector('#completeCourseModal .text-gray-700').innerHTML =
                'Are you sure you want to mark <span id="soldierNameCompleteCourse" class="font-semibold"></span>\'s course as completed?';
            document.getElementById('completeCourseNote').value = '';
            document.getElementById('completeCourseRecommendation').value = '';

            const existingInput = document.querySelector('input[name="course_ids"]');
            if (existingInput) existingInput.remove();

            document.getElementById('completeCourseModal').classList.add('hidden');
        }

        function completeCadre(id, soldierName) {
            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('completeCadreForm').action = `/coursecadremanager/cadre/${id}/complete`;
            document.getElementById('soldierNameCompleteCadre').textContent = soldierName;
            document.getElementById('completeCadreNote').value = '';
            document.getElementById('completeCadreRecommendation').value = '';
            document.getElementById('completeCadreModal').classList.remove('hidden');
        }

        function closeCompleteCadreModal() {
            document.querySelector('#completeCadreModal h3').textContent = 'Complete Cadre';
            document.querySelector('#completeCadreModal .text-gray-700').innerHTML =
                'Are you sure you want to mark <span id="soldierNameCompleteCadre" class="font-semibold"></span>\'s cadre as completed?';
            document.getElementById('completeCadreNote').value = '';
            document.getElementById('completeCadreRecommendation').value = '';

            const existingInput = document.querySelector('input[name="cadre_ids"]');
            if (existingInput) existingInput.remove();

            document.getElementById('completeCadreModal').classList.add('hidden');
        }

        function completeExArea(id, soldierName) {
            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('completeExAreaForm').action = `/coursecadremanager/ex_area/${id}/complete`;
            document.getElementById('soldierNameCompleteExArea').textContent = soldierName;
            document.getElementById('completeExAreaNote').value = '';
            document.getElementById('completeExAreaRecommendation').value = '';
            document.getElementById('completeExAreaModal').classList.remove('hidden');
        }

        function closeCompleteExAreaModal() {
            document.querySelector('#completeExAreaModal h3').textContent = 'Complete Ex-Area';
            document.querySelector('#completeExAreaModal .text-gray-700').innerHTML =
                'Are you sure you want to mark <span id="soldierNameCompleteExArea" class="font-semibold"></span>\'s ex-area as completed?';
            document.getElementById('completeExAreaNote').value = '';
            document.getElementById('completeExAreaRecommendation').value = '';

            const existingInput = document.querySelector('input[name="ex_area_ids"]');
            if (existingInput) existingInput.remove();

            document.getElementById('completeExAreaModal').classList.add('hidden');
        }

        function deleteAssignment(id, type) {
            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            if (confirm('Are you sure you want to delete this assignment? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/coursecadremanager/${type}/${id}`;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';

                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function viewAssignment(id, type) {
            // Implement view logic
        }

        function completeSelectedCourses() {
            const checkedBoxes = document.querySelectorAll('.course-checkbox:checked');
            if (checkedBoxes.length === 0) {
                showError('Please select at least one course to complete.');
                return;
            }

            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('completeCourseForm').action = `/coursecadremanager/courses/bulk-complete`;

            const courseIdsInput = document.createElement('input');
            courseIdsInput.type = 'hidden';
            courseIdsInput.name = 'course_ids';
            const courseIds = Array.from(checkedBoxes).map(cb => cb.dataset.id);
            courseIdsInput.value = JSON.stringify(courseIds);

            const existingInput = document.querySelector('input[name="course_ids"]');
            if (existingInput) existingInput.remove();

            document.getElementById('completeCourseForm').appendChild(courseIdsInput);

            document.querySelector('#completeCourseModal h3').textContent = 'Complete Selected Courses';
            const message =
                `Are you sure you want to mark ${courseIds.length} course${courseIds.length > 1 ? 's' : ''} as completed?`;
            document.querySelector('#completeCourseModal .text-gray-700').innerHTML = message;

            document.getElementById('completeCourseModal').classList.remove('hidden');
        }

        function completeSelectedCadres() {
            const checkedBoxes = document.querySelectorAll('.cadre-checkbox:checked');
            if (checkedBoxes.length === 0) {
                showError('Please select at least one cadre to complete.');
                return;
            }

            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('completeCadreForm').action = `/coursecadremanager/cadres/bulk-complete`;

            const cadreIdsInput = document.createElement('input');
            cadreIdsInput.type = 'hidden';
            cadreIdsInput.name = 'cadre_ids';
            const cadreIds = Array.from(checkedBoxes).map(cb => cb.dataset.id);
            cadreIdsInput.value = JSON.stringify(cadreIds);

            const existingInput = document.querySelector('input[name="cadre_ids"]');
            if (existingInput) existingInput.remove();

            document.getElementById('completeCadreForm').appendChild(cadreIdsInput);

            document.querySelector('#completeCadreModal h3').textContent = 'Complete Selected Cadres';
            const message =
                `Are you sure you want to mark ${cadreIds.length} cadre${cadreIds.length > 1 ? 's' : ''} as completed?`;
            document.querySelector('#completeCadreModal .text-gray-700').innerHTML = message;

            document.getElementById('completeCadreModal').classList.remove('hidden');
        }

        function completeSelectedExAreas() {
            const checkedBoxes = document.querySelectorAll('.ex-area-checkbox:checked');
            if (checkedBoxes.length === 0) {
                showError('Please select at least one ex-area to complete.');
                return;
            }

            const activeTab = document.querySelector('.tab-button.active')?.getAttribute('data-tab') || 'current-courses';
            localStorage.setItem('activeCourseCadreTab', activeTab);

            document.getElementById('completeExAreaForm').action = `/coursecadremanager/ex-areas/bulk-complete`;

            const exAreaIdsInput = document.createElement('input');
            exAreaIdsInput.type = 'hidden';
            exAreaIdsInput.name = 'ex_area_ids';
            const exAreaIds = Array.from(checkedBoxes).map(cb => cb.dataset.id);
            exAreaIdsInput.value = JSON.stringify(exAreaIds);

            const existingInput = document.querySelector('input[name="ex_area_ids"]');
            if (existingInput) existingInput.remove();

            document.getElementById('completeExAreaForm').appendChild(exAreaIdsInput);

            document.querySelector('#completeExAreaModal h3').textContent = 'Complete Selected Ex-Areas';
            const message =
                `Are you sure you want to mark ${exAreaIds.length} ex-area${exAreaIds.length > 1 ? 's' : ''} as completed?`;
            document.querySelector('#completeExAreaModal .text-gray-700').innerHTML = message;

            document.getElementById('completeExAreaModal').classList.remove('hidden');
        }

        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.remove('hidden');
        }

        function closeErrorModal() {
            document.getElementById('errorModal').classList.add('hidden');
        }

        function showSuccess(message) {
            const alert = document.createElement('div');
            alert.className =
                'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
            alert.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(alert);
            setTimeout(() => alert.remove(), 5000);
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const modals = ['completeCourseModal', 'completeCadreModal', 'completeExAreaModal', 'errorModal',
                'editAssignmentModal'
            ];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (event.target === modal) {
                    if (modalId === 'completeCourseModal') closeCompleteCourseModal();
                    else if (modalId === 'completeCadreModal') closeCompleteCadreModal();
                    else if (modalId === 'completeExAreaModal') closeCompleteExAreaModal();
                    else if (modalId === 'errorModal') closeErrorModal();
                    else if (modalId === 'editAssignmentModal') closeEditModal();
                }
            });
        }

        // Handle edit form submission
        document.addEventListener('DOMContentLoaded', function() {
            const editAssignmentForm = document.getElementById('editAssignmentForm');
            if (editAssignmentForm) {
                editAssignmentForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const type = document.getElementById('editAssignmentType').value;
                    const startDate = document.getElementById('editStartDate').value;
                    const soldierId = document.getElementById('editSoldierId').value;

                    if (!startDate) {
                        showError('Start date is required.');
                        return;
                    }

                    if (!soldierId) {
                        showError('Soldier is required.');
                        return;
                    }

                    if (type === 'course' && !document.getElementById('editCourseId').value) {
                        showError('Course is required.');
                        return;
                    } else if (type === 'cadre' && !document.getElementById('editCadreId').value) {
                        showError('Cadre is required.');
                        return;
                    } else if (type === 'ex_area' && !document.getElementById('editExAreaId').value) {
                        showError('Ex-Area is required.');
                        return;
                    }

                    showLoadingOverlay();

                    const formData = new FormData(this);
                    const id = document.getElementById('editAssignmentId').value;

                    fetch(`/coursecadremanager/${type}/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'X-HTTP-Method-Override': 'PUT'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            hideLoadingOverlay();
                            if (data.success) {
                                closeEditModal();
                                showSuccess(data.message);
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                showError(data.message || 'Failed to update assignment.');
                            }
                        })
                        .catch(error => {
                            hideLoadingOverlay();
                            console.error('Error updating assignment:', error);
                            showError('Failed to update assignment. Please try again.');
                        });
                });
            }

            // Handle completion form submissions
            const completeCourseForm = document.getElementById('completeCourseForm');
            const completeCadreForm = document.getElementById('completeCadreForm');
            const completeExAreaForm = document.getElementById('completeExAreaForm');

            if (completeCourseForm) {
                completeCourseForm.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Completing...';
                });
            }

            if (completeCadreForm) {
                completeCadreForm.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Completing...';
                });
            }

            if (completeExAreaForm) {
                completeExAreaForm.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Completing...';
                });
            }

            // Date adjustment warning handler
            const editSoldierId = document.getElementById('editSoldierId');
            const editStartDate = document.getElementById('editStartDate');

            if (editSoldierId && editStartDate) {
                editSoldierId.addEventListener('change', checkForDateAdjustment);
                editStartDate.addEventListener('change', checkForDateAdjustment);
            }
        });

        function checkForDateAdjustment() {
            const selectedOption = document.getElementById('editSoldierId')?.selectedOptions[0];
            const startDate = document.getElementById('editStartDate')?.value;
            const warning = document.getElementById('editDateAdjustmentWarning');

            if (selectedOption && selectedOption.textContent.includes('[Completed Today]') &&
                startDate === new Date().toISOString().split('T')[0]) {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        }
    </script>
@endpush
