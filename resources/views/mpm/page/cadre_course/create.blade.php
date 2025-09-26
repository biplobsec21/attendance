@extends('mpm.layouts.app')

@section('title', 'Course/Cadre Create Manager')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-8">
        <div class="container mx-auto px-4 max-w-6xl">
            <x-breadcrumb :breadcrumbs="generateBreadcrumbs_auto()" />
            @include('mpm.components.alerts')

            <!-- Main Form Card -->
            <div class="bg-white/80 backdrop-blur-sm shadow-2xl rounded-3xl border border-white/50 overflow-hidden">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-purple-600 p-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                        <div class="w-2 h-2 bg-white/70 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                        <div class="w-2 h-2 bg-white/50 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                        <h2 class="text-white text-xl font-semibold ml-4">Create Course/Cadre Assignment</h2>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-8">
                    <form action="{{ route('coursecadremanager.store') }}" method="POST" class="space-y-8"
                        id="assignmentForm">
                        @csrf

                        <!-- Assignment Type Selection -->
                        <div class="group">
                            <label for="type" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-emerald-400 to-cyan-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <span class="text-white font-bold text-sm">1</span>
                                </div>
                                Assignment Type
                            </label>
                            <select name="type" id="type" required
                                class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 text-gray-900 font-medium hover:border-gray-300">
                                <option value="" class="text-gray-500">Choose assignment type...</option>
                                <option value="course" class="text-gray-900">📚 Course Assignment</option>
                                <option value="cadre" class="text-gray-900">👥 Cadre Assignment</option>
                            </select>
                        </div>

                        <!-- Date Selection -->
                        <div class="group">
                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-amber-400 to-orange-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <span class="text-white font-bold text-sm">2</span>
                                </div>
                                Assignment Duration
                            </label>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Start Date -->
                                <div class="relative">
                                    <label for="start_date" class="block text-sm font-medium text-gray-600 mb-2">
                                        <svg class="inline w-4 h-4 mr-2 text-emerald-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Start Date
                                    </label>
                                    <input type="date" name="start_date" id="start_date"
                                        value="{{ old('start_date', now()->toDateString()) }}"
                                        class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-300">
                                </div>

                                <!-- End Date -->
                                <div class="relative">
                                    <label for="end_date" class="block text-sm font-medium text-gray-600 mb-2">
                                        <svg class="inline w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        End Date
                                    </label>
                                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                        class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-red-500/20 focus:border-red-500 transition-all duration-300">
                                </div>
                            </div>
                        </div>

                        <!-- Course Selection -->
                        <div id="courseSelection" class="hidden group">
                            <label for="course_id" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                </div>
                                Select Course
                            </label>
                            <select name="course_id" id="course_id"
                                class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 text-gray-900 font-medium">
                                <option value="" class="text-gray-500">Choose a course...</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" class="text-gray-900">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Cadre Selection -->
                        <div id="cadreSelection" class="hidden group">
                            <label for="cadre_id" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                Select Cadre
                            </label>
                            <select name="cadre_id" id="cadre_id"
                                class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 text-gray-900 font-medium">
                                <option value="" class="text-gray-500">Choose a cadre...</option>
                                @foreach ($cadres as $cadre)
                                    <option value="{{ $cadre->id }}" class="text-gray-900">{{ $cadre->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Personnel Selection -->
                        <div class="group">
                            <label class="flex items-center text-sm font-semibold text-gray-700 mb-4">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-teal-400 to-cyan-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <span class="text-white font-bold text-sm">3</span>
                                </div>
                                Personnel Selection
                            </label>

                            <!-- Filter Section -->
                            <div
                                class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-2xl p-6 mb-6 border border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z">
                                        </path>
                                    </svg>
                                    Filter Personnel
                                </h3>
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                                    <div class="relative">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                                        <input type="text" id="filter-army-no" placeholder="Army No or Name..."
                                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 text-sm">
                                    </div>

                                    <div class="relative">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Rank</label>
                                        <select id="filter-rank"
                                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 text-sm">
                                            <option value="">All Ranks</option>
                                            @foreach ($ranks as $rank)
                                                <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="relative">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Company</label>
                                        <select id="filter-company"
                                            class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-200 text-sm">
                                            <option value="">All Companies</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Available Soldiers Section -->
                            @include('mpm.page.cadre_course.available_soldiers_section', [
                                'availableSoldiers' => $availableSoldiers,
                            ])


                            <!-- Assigned Personnel Section -->
                            @include('mpm.page.cadre_course.assigned_personnel_section', [
                                'assignedSoldiers' => $assignedSoldiers,
                            ])
                        </div>

                        <!-- Notes Section -->
                        <div class="group">
                            <label for="note" class="flex items-center text-sm font-semibold text-gray-700 mb-3">
                                <div
                                    class="w-8 h-8 bg-gradient-to-r from-rose-400 to-pink-500 rounded-lg flex items-center justify-center mr-3 shadow-md">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </div>
                                Additional Notes
                            </label>
                            <div class="relative">
                                <textarea name="note" id="note" rows="4"
                                    class="w-full px-4 py-3 bg-white border-2 border-gray-200 rounded-xl shadow-sm focus:ring-4 focus:ring-rose-500/20 focus:border-rose-500 transition-all duration-300 resize-none"
                                    placeholder="✍️ Add any additional notes or special instructions here..."></textarea>
                                <div class="absolute bottom-3 right-3 text-xs text-gray-400">Optional</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="border-t border-gray-200 pt-6">
                            <div
                                class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                                <div class="text-sm text-gray-500">
                                    <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Please review all details before submitting
                                </div>
                                <button type="submit"
                                    class="inline-flex items-center px-8 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold shadow-lg hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 transform hover:scale-105 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Save information
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JS for Filtering Soldiers and Dynamic Selection --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Soldier filtering
            const armyInput = document.getElementById("filter-army-no");
            const rankSelect = document.getElementById("filter-rank");
            const companySelect = document.getElementById("filter-company");
            const availableRepo = document.getElementById("available-soldier-repo");
            const assignedRepo = document.getElementById("assigned-soldier-repo");
            const availableSoldierCards = Array.from(availableRepo.querySelectorAll("label"));
            const assignedSoldierCards = Array.from(assignedRepo.querySelectorAll("div.group"));

            function normalize(str = '') {
                return String(str).toLowerCase().replace(/\s+/g, '');
            }

            function filter() {
                const armyRaw = armyInput.value.trim();
                const army = normalize(armyRaw);
                const rank = rankSelect.value;
                const company = companySelect.value;

                // Filter available soldiers
                availableSoldierCards.forEach(card => {
                    const checkbox = card.querySelector('input[type="checkbox"]');
                    const cardArmy = normalize(checkbox.dataset.armyNo || '');
                    const cardName = normalize(checkbox.dataset.fullName || '');
                    const cardRank = String(checkbox.dataset.rankId || '');
                    const cardCompany = String(checkbox.dataset.companyId || '');

                    const matchesArmy = !army || cardArmy.includes(army) || cardName.includes(army);
                    const matchesRank = !rank || cardRank === rank;
                    const matchesCompany = !company || cardCompany === company;

                    card.style.display = (matchesArmy && matchesRank && matchesCompany) ? "flex" : "none";
                });

                // Filter assigned soldiers
                assignedSoldierCards.forEach(card => {
                    // We need to add data attributes to assigned soldiers too
                    // For now, we'll just show all assigned soldiers
                    // In a real implementation, you'd add data attributes to these cards too
                });
            }

            // wire up events
            armyInput.addEventListener("input", filter);
            rankSelect.addEventListener("change", filter);
            companySelect.addEventListener("change", filter);

            // run once on load to apply any defaults
            filter();

            // Dynamic course/cadre selection
            const typeSelect = document.getElementById('type');
            const courseSelection = document.getElementById('courseSelection');
            const cadreSelection = document.getElementById('cadreSelection');
            const courseIdSelect = document.getElementById('course_id');
            const cadreIdSelect = document.getElementById('cadre_id');
            const assignmentForm = document.getElementById('assignmentForm');

            typeSelect.addEventListener('change', function() {
                const type = this.value;

                // Hide both selections
                courseSelection.classList.add('hidden');
                cadreSelection.classList.add('hidden');

                // Remove both fields from form submission
                courseIdSelect.removeAttribute('required');
                cadreIdSelect.removeAttribute('required');
                courseIdSelect.setAttribute('disabled', 'disabled');
                cadreIdSelect.setAttribute('disabled', 'disabled');

                // Show the appropriate selection based on type
                if (type === 'course') {
                    courseSelection.classList.remove('hidden');
                    courseIdSelect.setAttribute('required', 'required');
                    courseIdSelect.removeAttribute('disabled');
                    // Clear cadre value to prevent it from being sent
                    cadreIdSelect.value = '';
                } else if (type === 'cadre') {
                    cadreSelection.classList.remove('hidden');
                    cadreIdSelect.setAttribute('required', 'required');
                    cadreIdSelect.removeAttribute('disabled');
                    // Clear course value to prevent it from being sent
                    courseIdSelect.value = '';
                }
            });

            // Form submission handler to ensure proper validation
            assignmentForm.addEventListener('submit', function(e) {
                const type = typeSelect.value;

                if (!type) {
                    e.preventDefault();
                    alert('Please select an assignment type.');
                    return;
                }

                if (type === 'course' && !courseIdSelect.value) {
                    e.preventDefault();
                    alert('Please select a course.');
                    return;
                }

                if (type === 'cadre' && !cadreIdSelect.value) {
                    e.preventDefault();
                    alert('Please select a cadre.');
                    return;
                }

                // Check if at least one soldier is selected
                const selectedSoldiers = document.querySelectorAll('input[name="soldier_ids[]"]:checked');
                if (selectedSoldiers.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one soldier.');
                    return;
                }
            });

            // Handle date adjustment for soldiers who completed assignments today
            const soldierCheckboxes = document.querySelectorAll('input[name="soldier_ids[]"]');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const dateAdjustmentWarning = document.createElement('div');
            dateAdjustmentWarning.className = 'mt-2 p-3 bg-amber-50 rounded-lg border border-amber-200 hidden';
            dateAdjustmentWarning.innerHTML = `
        <div class="flex items-start">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <p class="text-sm text-amber-700">
                The start date will be adjusted to tomorrow for soldiers who completed assignments today.
            </p>
        </div>
    `;

            // Insert the warning after the date inputs
            const dateInputsContainer = startDateInput.closest('.grid');
            dateInputsContainer.parentNode.insertBefore(dateAdjustmentWarning, dateInputsContainer.nextSibling);

            // Check if any selected soldiers have completed assignments today
            function checkForCompletedToday() {
                const selectedSoldiers = document.querySelectorAll('input[name="soldier_ids[]"]:checked');
                let hasCompletedToday = false;

                selectedSoldiers.forEach(checkbox => {
                    if (checkbox.dataset.completedToday === '1') {
                        hasCompletedToday = true;
                    }
                });

                if (hasCompletedToday && startDateInput.value === new Date().toISOString().split('T')[0]) {
                    dateAdjustmentWarning.classList.remove('hidden');
                } else {
                    dateAdjustmentWarning.classList.add('hidden');
                }
            }

            // Add event listeners to all soldier checkboxes
            soldierCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', checkForCompletedToday);
            });

            // Add event listener to start date input
            startDateInput.addEventListener('change', checkForCompletedToday);

            // Initial check
            checkForCompletedToday();
        });
    </script>
@endsection
