@extends('mpm.layouts.app')

@section('title', 'Duty List')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Duty List</h1>
            <div class="w-full md:w-auto flex flex-col sm:flex-row items-center gap-2">
                 <div class="w-full sm:w-auto flex items-center space-x-2">
                    <label for="rows-per-page" class="text-sm font-medium text-gray-700">Rows:</label>
                    <select id="rows-per-page" class="w-full border rounded-lg px-2 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <input type="text" id="table-search" class="w-full sm:w-auto px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Search duties...">
                <a href="{{ url('duty/create') }}" class="w-full sm:w-auto bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors text-center no-underline">Add New Duty</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-transparent rounded-lg">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#SL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duty Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remark</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody id="duty-table-body" class="divide-y divide-gray-300">
                    <!-- Table rows will be inserted by JavaScript -->
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div id="pagination-controls" class="flex justify-center items-center mt-4 space-x-2 flex-wrap"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // --- Duty Data ---
    const dutyData = [
        { sl: 1, name: "Guard Duty", remark: "Night shift at the main gate.", status: "Active" },
        { sl: 2, name: "Kitchen Patrol", remark: "Assisting the cooks.", status: "Active" },
        { sl: 3, name: "Cleaning Duty", remark: "Barracks and common areas.", status: "Active" },
        { sl: 4, name: "Special Assignment", remark: "Classified task.", status: "Inactive" },
        { sl: 5, name: "Range Safety Officer", remark: "Overseeing live fire exercises.", status: "Active" }
    ];

    // --- Table Search and Pagination Logic ---
    const tableBody = document.getElementById('duty-table-body');
    const searchInput = document.getElementById('table-search');
    const paginationControls = document.getElementById('pagination-controls');
    const rowsPerPageSelect = document.getElementById('rows-per-page');
    
    let currentPage = 1;
    let rowsPerPage = rowsPerPageSelect.value === 'all' ? dutyData.length : parseInt(rowsPerPageSelect.value);
    let filteredData = [...dutyData];

    function displayTable(page) {
        tableBody.innerHTML = '';
        page--; // Adjust for zero-based index

        const start = rowsPerPage * page;
        const end = start + rowsPerPage;
        const paginatedItems = filteredData.slice(start, end);

        for (let i = 0; i < paginatedItems.length; i++) {
            const item = paginatedItems[i];
            const statusClass = item.status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
            const row = `<tr>
                            <td class="px-6 py-4 whitespace-nowrap">${item.sl}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.remark}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass} cursor-pointer" data-id="${item.sl}">
                                    ${item.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <a href="#" class="ml-4 text-red-600 hover:text-red-900">Delete</a>
                            </td>
                         </tr>`;
            tableBody.innerHTML += row;
        }
    }

    function setupPagination(items, wrapper) {
        wrapper.innerHTML = '';
        
        if (rowsPerPage >= items.length) return;

        const pageCount = Math.ceil(items.length / rowsPerPage);
        
        if (pageCount > 1) {
            const prevBtn = paginationNavButton('Prev');
            wrapper.appendChild(prevBtn);
        }

        for (let i = 1; i < pageCount + 1; i++) {
            const btn = paginationButton(i);
            wrapper.appendChild(btn);
        }

        if (pageCount > 1) {
            const nextBtn = paginationNavButton('Next');
            wrapper.appendChild(nextBtn);
        }
    }
    
    function updateActiveButton(page) {
        let currentActiveBtn = document.querySelector('#pagination-controls button.bg-orange-500');
        if(currentActiveBtn) {
            currentActiveBtn.classList.remove('bg-orange-500', 'text-white');
            currentActiveBtn.classList.add('hover:bg-orange-200');
        }
        
        let newActiveBtn = document.querySelector(`#pagination-controls button[data-page='${page}']`);
        if (newActiveBtn) {
            newActiveBtn.classList.add('bg-orange-500', 'text-white');
            newActiveBtn.classList.remove('hover:bg-orange-200');
        }
    }
    
    function paginationNavButton(text) {
        const button = document.createElement('button');
        button.innerText = text;
        button.classList.add('px-3', 'py-1', 'border', 'rounded-md', 'transition-colors', 'mb-2', 'hover:bg-orange-200');
        
        button.addEventListener('click', () => {
             const pageCount = Math.ceil(filteredData.length / rowsPerPage);
            if (text === 'Prev') {
                currentPage = Math.max(1, currentPage - 1);
            } else if (text === 'Next') {
                currentPage = Math.min(pageCount, currentPage + 1);
            }
            displayTable(currentPage);
            updateActiveButton(currentPage);
        });
        
        return button;
    }


    function paginationButton(page) {
        const button = document.createElement('button');
        button.innerText = page;
        button.setAttribute('data-page', page);
        button.classList.add('px-3', 'py-1', 'border', 'rounded-md', 'transition-colors', 'mb-2');

        if (currentPage == page) {
            button.classList.add('bg-orange-500', 'text-white');
        } else {
            button.classList.add('hover:bg-orange-200');
        }

        button.addEventListener('click', () => {
            currentPage = page;
            displayTable(currentPage);
            updateActiveButton(page);
        });

        return button;
    }

    function toggleStatus(dutyId) {
        const duty = dutyData.find(item => item.sl === dutyId);
        if (duty) {
            duty.status = duty.status === 'Active' ? 'Inactive' : 'Active';
        }
        const filteredDuty = filteredData.find(item => item.sl === dutyId);
        if (filteredDuty) {
            filteredDuty.status = duty.status;
        }
        displayTable(currentPage);
    }

    tableBody.addEventListener('click', (e) => {
        if (e.target.matches('span[data-id]')) {
            const dutyId = parseInt(e.target.dataset.id);
            toggleStatus(dutyId);
        }
    });
    
    searchInput.addEventListener('keyup', (e) => {
        const searchValue = e.target.value.toLowerCase();
        filteredData = dutyData.filter(item => {
            return item.name.toLowerCase().includes(searchValue) || item.remark.toLowerCase().includes(searchValue);
        });
        currentPage = 1;
        displayTable(currentPage);
        setupPagination(filteredData, paginationControls);
    });
    
    rowsPerPageSelect.addEventListener('change', (e) => {
        const selectedValue = e.target.value;
        if (selectedValue === 'all') {
            rowsPerPage = filteredData.length > 0 ? filteredData.length : dutyData.length;
        } else {
            rowsPerPage = parseInt(selectedValue);
        }
        currentPage = 1;
        displayTable(currentPage);
        setupPagination(filteredData, paginationControls);
    });

    // Initial setup
    displayTable(currentPage);
    setupPagination(filteredData, paginationControls);
</script>
@endpush
