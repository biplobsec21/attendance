@extends('mpm.layouts.app')

@section('title', 'Duty Approval Panel')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Duty Approval Panel</h1>
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
                <input type="text" id="table-search" class="w-full sm:w-auto px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Search records...">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-transparent rounded-lg">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#SL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Person Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duty Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remark</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval Action</th>
                    </tr>
                </thead>
                <tbody id="approval-table-body" class="divide-y divide-gray-300">
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
    // --- Duty Approval Data ---
    const approvalData = [
        { sl: 1, date: "2023-10-10", no: "12345", rank: "Captain", person: "John Doe", duty: "Guard Duty", startTime: "22:00", endTime: "06:00", remark: "Night shift", status: "Pending" },
        { sl: 2, date: "2023-10-10", no: "54321", rank: "Major", person: "Jane Smith", duty: "Kitchen Patrol", startTime: "08:00", endTime: "16:00", remark: "Morning shift", status: "Pending" },
        { sl: 3, date: "2023-10-11", no: "67890", rank: "Sergeant", person: "Peter Jones", duty: "Cleaning Duty", startTime: "09:00", endTime: "17:00", remark: "", status: "Pending" },
        { sl: 4, date: "2023-10-12", no: "09876", rank: "Lieutenant", person: "Mary Williams", duty: "Guard Duty", startTime: "14:00", endTime: "22:00", remark: "Day shift", status: "Pending" }
    ];

    // --- Table Search and Pagination Logic ---
    const tableBody = document.getElementById('approval-table-body');
    const searchInput = document.getElementById('table-search');
    const paginationControls = document.getElementById('pagination-controls');
    const rowsPerPageSelect = document.getElementById('rows-per-page');
    
    let currentPage = 1;
    let rowsPerPage = rowsPerPageSelect.value === 'all' ? approvalData.length : parseInt(rowsPerPageSelect.value);
    let filteredData = [...approvalData];

    function displayTable(page) {
        tableBody.innerHTML = '';
        page--; // Adjust for zero-based index

        const start = rowsPerPage * page;
        const end = start + rowsPerPage;
        const paginatedItems = filteredData.slice(start, end);

        for (let i = 0; i < paginatedItems.length; i++) {
            const item = paginatedItems[i];
            let actionHtml = `<a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>`;

            if (item.status === 'Pending') {
                actionHtml += `<button class="ml-4 text-green-600 hover:text-green-900" onclick="handleApproval(${item.sl}, 'Approved')">Approve</button>
                               <button class="ml-4 text-red-600 hover:text-red-900" onclick="handleApproval(${item.sl}, 'Rejected')">Reject</button>`;
            } else {
                const statusClass = item.status === 'Approved' ? 'text-green-800' : 'text-red-800';
                actionHtml += `<span class="ml-4 font-semibold ${statusClass}">${item.status}</span>`;
            }

            const row = `<tr>
                            <td class="px-6 py-4 whitespace-nowrap">${item.sl}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.date}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.no}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.rank}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.person}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.duty}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.startTime}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.endTime}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.remark}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">${actionHtml}</td>
                         </tr>`;
            tableBody.innerHTML += row;
        }
    }

    window.handleApproval = function(sl, status) {
        const item = filteredData.find(d => d.sl === sl);
        if (item) {
            item.status = status;
            displayTable(currentPage + 1);
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
        filteredData = approvalData.filter(item => {
            return item.person.toLowerCase().includes(searchValue) || 
                   item.duty.toLowerCase().includes(searchValue) ||
                   item.no.toLowerCase().includes(searchValue) ||
                   item.rank.toLowerCase().includes(searchValue);
        });
        currentPage = 1;
        displayTable(currentPage);
        setupPagination(filteredData, paginationControls);
    });
    
    rowsPerPageSelect.addEventListener('change', (e) => {
        const selectedValue = e.target.value;
        if (selectedValue === 'all') {
            rowsPerPage = filteredData.length > 0 ? filteredData.length : approvalData.length;
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
