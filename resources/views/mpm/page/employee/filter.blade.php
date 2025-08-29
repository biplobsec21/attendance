@extends('mpm.layouts.app')

@section('title', 'Filter Employees')

@section('content')

<div class="container mx-auto p-4">
    <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Filter Employees</h1>
        <form id="filter-employee-form">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Company Dropdown -->
                <div class="mb-4">
                    <label for="company" class="block text-gray-700 text-sm font-bold mb-2">Company</label>
                    <select id="company" class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">All Companies</option>
                        <option value="Alpha">Alpha</option>
                        <option value="Bravo">Bravo</option>
                        <option value="Charlie">Charlie</option>
                        <option value="Delta">Delta</option>
                    </select>
                </div>

                <!-- Rank Dropdown -->
                <div class="mb-4">
                    <label for="rank" class="block text-gray-700 text-sm font-bold mb-2">Rank</label>
                    <select id="rank" class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">All Ranks</option>
                        <option value="Private">Private</option>
                        <option value="Corporal">Corporal</option>
                        <option value="Sergeant">Sergeant</option>
                        <option value="Lieutenant">Lieutenant</option>
                        <option value="Captain">Captain</option>
                        <option value="Major">Major</option>
                    </select>
                </div>

                <!-- Course/Cadre Multi-select -->
                <div class="mb-4">
                    <label for="course-cadre" class="block text-gray-700 text-sm font-bold mb-2">Course/Cadre</label>
                    <select id="course-cadre" multiple class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="Course A">Course A</option>
                        <option value="Course B">Course B</option>
                        <option value="Cadre X">Cadre X</option>
                        <option value="Cadre Y">Cadre Y</option>
                    </select>
                </div>

                <!-- Sports Multi-select -->
                <div class="mb-4">
                    <label for="sports" class="block text-gray-700 text-sm font-bold mb-2">Sports</label>
                    <select id="sports" multiple class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="Football">Football</option>
                        <option value="Basketball">Basketball</option>
                        <option value="Cricket">Cricket</option>
                        <option value="Hockey">Hockey</option>
                    </select>
                </div>

                <!-- Other Qual Multi-select -->
                <div class="mb-4">
                    <label for="other-qual" class="block text-gray-700 text-sm font-bold mb-2">Other Qual</label>
                    <select id="other-qual" multiple class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="Qualification 1">Qualification 1</option>
                        <option value="Qualification 2">Qualification 2</option>
                        <option value="Qualification 3">Qualification 3</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6 space-x-4">
                <button id="clear-button" type="button" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors">
                    Clear
                </button>
                <button class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors" type="submit">
                    Search
                </button>
            </div>
        </form>

        <!-- Results Table -->
        <div class="mt-8 overflow-x-auto">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Search Results</h2>
            <table class="min-w-full bg-transparent rounded-lg">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Courses</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sports</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Other Quals</th>
                    </tr>
                </thead>
                <tbody id="results-table-body" class="divide-y divide-gray-300">
                    <!-- Results will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const employeeData = [
            { name: "John Doe", company: "Alpha", rank: "Captain", courses: ["Course A", "Cadre X"], sports: ["Football"], quals: ["Qualification 1"] },
            { name: "Jane Smith", company: "Bravo", rank: "Major", courses: ["Course B"], sports: ["Basketball", "Cricket"], quals: [] },
            { name: "Peter Jones", company: "Charlie", rank: "Sergeant", courses: ["Cadre Y"], sports: [], quals: ["Qualification 2", "Qualification 3"] },
            { name: "Mary Williams", company: "Alpha", rank: "Lieutenant", courses: ["Course A", "Course B"], sports: ["Hockey"], quals: ["Qualification 1"] },
            { name: "David Brown", company: "Delta", rank: "Corporal", courses: ["Cadre X"], sports: ["Football", "Cricket"], quals: [] }
        ];

        const resultsTableBody = document.getElementById('results-table-body');
        const filterForm = document.getElementById('filter-employee-form');
        const clearButton = document.getElementById('clear-button');

        function displayResults(data) {
            resultsTableBody.innerHTML = '';
            if (data.length === 0) {
                resultsTableBody.innerHTML = '<tr><td colspan="6" class="text-center py-4">No results found.</td></tr>';
                return;
            }
            data.forEach(employee => {
                const row = `<tr>
                                <td class="px-6 py-4 whitespace-nowrap">${employee.name}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${employee.company}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${employee.rank}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${employee.courses.join(', ')}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${employee.sports.join(', ')}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${employee.quals.join(', ')}</td>
                             </tr>`;
                resultsTableBody.innerHTML += row;
            });
        }

        // Initial display of all data
        displayResults(employeeData);

        function getSelectedOptions(select) {
            const options = select.options;
            const selectedValues = [];
            for (let i = 0; i < options.length; i++) {
                if (options[i].selected) {
                    selectedValues.push(options[i].value);
                }
            }
            return selectedValues;
        }

        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const company = document.getElementById('company').value;
            const rank = document.getElementById('rank').value;
            const courseCadre = getSelectedOptions(document.getElementById('course-cadre'));
            const sports = getSelectedOptions(document.getElementById('sports'));
            const otherQual = getSelectedOptions(document.getElementById('other-qual'));

            const filteredData = employeeData.filter(employee => {
                const companyMatch = !company || employee.company === company;
                const rankMatch = !rank || employee.rank === rank;
                const courseMatch = courseCadre.length === 0 || courseCadre.every(c => employee.courses.includes(c));
                const sportMatch = sports.length === 0 || sports.every(s => employee.sports.includes(s));
                const qualMatch = otherQual.length === 0 || otherQual.every(q => employee.quals.includes(q));
                
                return companyMatch && rankMatch && courseMatch && sportMatch && qualMatch;
            });

            displayResults(filteredData);
        });

        clearButton.addEventListener('click', function() {
            filterForm.reset();
            // For multi-select, we need to manually deselect options
            document.querySelectorAll('#filter-employee-form select[multiple]').forEach(select => {
                for (let i = 0; i < select.options.length; i++) {
                    select.options[i].selected = false;
                }
            });
            displayResults(employeeData);
        });
    });
</script>
@endpush
