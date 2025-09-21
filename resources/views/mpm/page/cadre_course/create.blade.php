@extends('mpm.layouts.app')

@section('title', 'Course/Cadre Manager')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs_auto()" />
        @include('mpm.components.alerts')

        <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Military Course/Cadre Assignment</h1>
                <p class="text-gray-600">Assign soldiers to courses or cadres</p>
            </div>

            <form action="{{ route('coursecadremanager.store') }}" method="POST" class="space-y-6" id="assignmentForm">
                @csrf

                {{-- 1. Assignment Type Selection --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Assignment Type</label>
                    <select name="type" id="type" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Select Type --</option>
                        <option value="course">Course</option>
                        <option value="cadre">Cadre</option>
                    </select>
                </div>

                {{-- 2. Course Selection --}}
                <div id="courseSelection" class="hidden">
                    <label for="course_id" class="block text-sm font-medium text-gray-700">Select Course</label>
                    <select name="course_id" id="course_id"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Select Course --</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Cadre Selection --}}
                <div id="cadreSelection" class="hidden">
                    <label for="cadre_id" class="block text-sm font-medium text-gray-700">Select Cadre</label>
                    <select name="cadre_id" id="cadre_id"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">-- Select Cadre --</option>
                        @foreach ($cadres as $cadre)
                            <option value="{{ $cadre->id }}">{{ $cadre->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. Soldiers Repository with Filters --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Soldiers</label>

                    {{-- Filter Section --}}
                    <div class="flex flex-col sm:flex-row sm:space-x-4 mb-4">
                        <input type="text" id="filter-army-no" placeholder="Search by Army No"
                            class="w-full sm:w-1/3 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm mb-2 sm:mb-0">

                        <select id="filter-rank"
                            class="w-full sm:w-1/3 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm mb-2 sm:mb-0">
                            <option value="">All Ranks</option>
                            @foreach ($ranks as $rank)
                                <option value="{{ $rank->id }}">{{ $rank->name }}</option>
                            @endforeach
                        </select>

                        <select id="filter-company"
                            class="w-full sm:w-1/3 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Companies</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Soldiers List --}}
                    <div id="soldier-repo"
                        class="border rounded-lg p-3 h-64 overflow-y-auto bg-white shadow-inner grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach ($soldiers as $soldier)
                            <label
                                class="flex items-center space-x-2 p-2 rounded-lg border hover:bg-indigo-50 cursor-pointer">
                                <input type="checkbox" name="soldier_ids[]" value="{{ $soldier->id }}"
                                    data-rank-id="{{ $soldier->rank_id }}" data-company-id="{{ $soldier->company_id }}"
                                    data-army-no="{{ strtolower(str_replace(' ', '', $soldier->army_no ?? '')) }}"
                                    data-full-name="{{ strtolower($soldier->full_name ?? '') }}"
                                    class="form-checkbox h-5 w-5 text-indigo-600">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $soldier->full_name }}</p>
                                    <p class="text-xs text-gray-500">Army No: {{ $soldier->army_no }} |
                                        {{ $soldier->rank->name }} | {{ $soldier->company->name }}</p>
                                </div>
                            </label>
                        @endforeach

                    </div>
                </div>

                {{-- 5. Text Field --}}
                <div>
                    <label for="note" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="note" id="note" rows="3"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Write additional notes here..."></textarea>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 shadow">
                        Save Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JS for Filtering Soldiers and Dynamic Selection --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Soldier filtering
            const armyInput = document.getElementById("filter-army-no");
            const rankSelect = document.getElementById("filter-rank");
            const companySelect = document.getElementById("filter-company");
            const repo = document.getElementById("soldier-repo");
            const soldierCards = Array.from(repo.querySelectorAll("label"));

            function normalize(str = '') {
                return String(str).toLowerCase().replace(/\s+/g, '');
            }

            function filter() {
                const armyRaw = armyInput.value.trim();
                const army = normalize(armyRaw);
                const rank = rankSelect.value; // ID as string or ""
                const company = companySelect.value; // ID as string or ""

                soldierCards.forEach(card => {
                    const checkbox = card.querySelector('input[type="checkbox"]');
                    const cardArmy = normalize(checkbox.dataset.armyNo || '');
                    const cardName = normalize(checkbox.dataset.fullName || '');
                    const cardRank = String(checkbox.dataset.rankId || '');
                    const cardCompany = String(checkbox.dataset.companyId || '');

                    // Army filter matches army number or full name substring
                    const matchesArmy = !army || cardArmy.includes(army) || cardName.includes(army);

                    // Rank/company compare IDs (exact match)
                    const matchesRank = !rank || cardRank === rank;
                    const matchesCompany = !company || cardCompany === company;

                    card.style.display = (matchesArmy && matchesRank && matchesCompany) ? "flex" : "none";
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
        });
    </script>
@endsection
