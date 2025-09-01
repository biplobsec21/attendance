@extends('mpm.layouts.app')

@section('title', 'Leave Request List')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Leave Request List</h1>
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
                <input type="text" id="table-search" class="w-full sm:w-auto px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Search requests...">
                <a href="{{ url('leave/create') }}" class="w-full sm:w-auto bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors text-center no-underline">New Leave Request</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-transparent rounded-lg">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#SL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody id="leave-table-body" class="divide-y divide-gray-300">
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
    // --- Leave Request Data ---
    const leaveData = [
        { sl: 1, no: "12345", rank: "Captain", name: "John Doe", type: "Annual Leave", from: "2023-11-01", to: "2023-11-10", status: "Approved" },
        { sl: 2, no: "54321", rank: "Major", name: "Jane Smith", type: "Sick Leave", from: "2023-11-05", to: "2023-11-07", status: "Waiting" },
        { sl: 3, no: "67890", rank: "Sergeant", name: "Peter Jones", type: "Compassionate Leave", from: "2023-11-12", to: "2023-11-15", status: "Rejected" }
    ];

    // --- Table Search and Pagination Logic ---
    const tableBody = document.getElementById('leave-table-body');
    const searchInput = document.getElementById('table-search');
    const paginationControls = document.getElementById('pagination-controls');
    const rowsPerPageSelect = document.getElementById('rows-per-page');
    
    let currentPage = 1;
    let rowsPerPage = rowsPerPageSelect.value === 'all' ? leaveData.length : parseInt(rowsPerPageSelect.value);
    let filteredData = [...leaveData];

    function calculateDuration(from, to) {
        const fromDate = new Date(from);
        const toDate = new Date(to);
        const diffTime = Math.abs(toDate - fromDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Include start day
        return diffDays > 1 ? `${diffDays} days` : `${diffDays} day`;
    }

    function getStatusClass(status) {
        switch (status) {
            case 'Approved':
                return 'bg-green-100 text-green-800';
            case 'Rejected':
                return 'bg-red-100 text-red-800';
            case 'Waiting':
            default:
                return 'bg-yellow-100 text-yellow-800';
        }
    }

    function displayTable(page) {
        tableBody.innerHTML = '';
        page--; // Adjust for zero-based index

        const start = rowsPerPage * page;
        const end = start + rowsPerPage;
        const paginatedItems = filteredData.slice(start, end);

        for (let i = 0; i < paginatedItems.length; i++) {
            const item = paginatedItems[i];
            const duration = calculateDuration(item.from, item.to);
            const statusClass = getStatusClass(item.status);
            const row = `<tr>
                            <td class="px-6 py-4 whitespace-nowrap">${item.sl}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.no}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.rank}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.type}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.from}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.to}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${duration}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
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
    
    searchInput.addEventListener('keyup', (e) => {
        const searchValue = e.target.value.toLowerCase();
        filteredData = leaveData.filter(item => {
            return item.name.toLowerCase().includes(searchValue) || 
                   item.no.toLowerCase().includes(searchValue) ||
                   item.rank.toLowerCase().includes(searchValue) ||
                   item.type.toLowerCase().includes(searchValue) ||
                   item.status.toLowerCase().includes(searchValue);
        });
        currentPage = 1;
        displayTable(currentPage);
        setupPagination(filteredData, paginationControls);
    });
    
    rowsPerPageSelect.addEventListener('change', (e) => {
        const selectedValue = e.target.value;
        if (selectedValue === 'all') {
            rowsPerPage = filteredData.length > 0 ? filteredData.length : leaveData.length;
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
