@extends('takbir.layouts.app')

@section('title', 'Business List')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Business List</h1>
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
                <input type="text" id="table-search" class="w-full sm:w-auto px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Search businesses...">
                <a href="{{ url('/business/create') }}" class="w-full sm:w-auto bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors text-center">
                    Add New Business
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-transparent rounded-lg">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#SL</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody id="business-table-body" class="divide-y divide-gray-300">
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
    document.addEventListener('DOMContentLoaded', function() {
        // --- Business Data (Sample) ---
        const businessData = [
            { sl: 1, name: "Tech Solutions Inc.", contact: "John Doe", status: "Active" },
            { sl: 2, name: "Creative Designs", contact: "Jane Smith", status: "Active" },
            { sl: 3, "name": "Gourmet Eats", "contact": "Peter Jones", "status": "Inactive" },
            { sl: 4, "name": "Global Logistics", "contact": "Mary Johnson", "status": "Active" },
            { sl: 5, "name": "HealthFirst Clinic", "contact": "Robert Brown", "status": "Active" },
            { sl: 6, "name": "Innovatech", "contact": "Emily Davis", "status": "Inactive" },
            { sl: 7, "name": "GreenScape Landscaping", "contact": "Michael Wilson", "status": "Active" },
            { sl: 8, "name": "AutoFix Masters", "contact": "Sarah Miller", "status": "Active" },
            { sl: 9, "name": "The Book Nook", "contact": "David Martinez", "status": "Inactive" },
            { sl: 10, "name": "ProClean Services", "contact": "Jessica Garcia", "status": "Active" },
            { sl: 7, "name": "GreenScape Landscaping", "contact": "Michael Wilson", "status": "Active" },
            { sl: 8, "name": "AutoFix Masters", "contact": "Sarah Miller", "status": "Active" },
            { sl: 9, "name": "The Book Nook", "contact": "David Martinez", "status": "Inactive" },
            { sl: 10, "name": "ProClean Services", "contact": "Jessica Garcia", "status": "Active" },
            { sl: 7, "name": "GreenScape Landscaping", "contact": "Michael Wilson", "status": "Active" },
            { sl: 8, "name": "AutoFix Masters", "contact": "Sarah Miller", "status": "Active" },
            { sl: 9, "name": "The Book Nook", "contact": "David Martinez", "status": "Inactive" },
            { sl: 10, "name": "ProClean Services", "contact": "Jessica Garcia", "status": "Active" },
            { sl: 7, "name": "GreenScape Landscaping", "contact": "Michael Wilson", "status": "Active" },
            { sl: 8, "name": "AutoFix Masters", "contact": "Sarah Miller", "status": "Active" },
            { sl: 9, "name": "The Book Nook", "contact": "David Martinez", "status": "Inactive" },
            { sl: 10, "name": "ProClean Services", "contact": "Jessica Garcia", "status": "Active" },
            { sl: 10, "name": "ProClean Services", "contact": "Jessica Garcia", "status": "Active" }
        ];

        const tableBody = document.getElementById('business-table-body');
        const searchInput = document.getElementById('table-search');
        const paginationControls = document.getElementById('pagination-controls');
        const rowsPerPageSelect = document.getElementById('rows-per-page');
        
        let currentPage = 1;
        let rowsPerPage = rowsPerPageSelect.value === 'all' ? businessData.length : parseInt(rowsPerPageSelect.value);
        let filteredData = [...businessData];

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
                                <td class="px-6 py-4 whitespace-nowrap">${item.contact}</td>
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

            for (let i = 1; i < pageCount + 1; i++) {
                const btn = paginationButton(i);
                wrapper.appendChild(btn);
            }
        }

        function paginationButton(page) {
            const button = document.createElement('button');
            button.innerText = page;
            button.classList.add('px-3', 'py-1', 'border', 'rounded-md', 'transition-colors', 'mb-2');

            if (currentPage == page) {
                button.classList.add('bg-orange-500', 'text-white');
            } else {
                button.classList.add('hover:bg-orange-200');
            }

            button.addEventListener('click', () => {
                currentPage = page;
                displayTable(currentPage);
                
                let currentBtn = document.querySelector('#pagination-controls button.bg-orange-500');
                if(currentBtn) {
                    currentBtn.classList.remove('bg-orange-500', 'text-white');
                    currentBtn.classList.add('hover:bg-orange-200');
                }

                button.classList.add('bg-orange-500', 'text-white');
                button.classList.remove('hover:bg-orange-200');
            });

            return button;
        }
        
        function toggleStatus(businessId) {
            const business = businessData.find(item => item.sl === businessId);
            if (business) {
                business.status = business.status === 'Active' ? 'Inactive' : 'Active';
            }
            const filteredBusiness = filteredData.find(item => item.sl === businessId);
            if (filteredBusiness) {
                filteredBusiness.status = business.status;
            }
            displayTable(currentPage);
        }

        tableBody.addEventListener('click', (e) => {
            if (e.target.matches('span[data-id]')) {
                const businessId = parseInt(e.target.dataset.id);
                toggleStatus(businessId);
            }
        });

        searchInput.addEventListener('keyup', (e) => {
            const searchValue = e.target.value.toLowerCase();
            filteredData = businessData.filter(item => {
                return item.name.toLowerCase().includes(searchValue) || item.contact.toLowerCase().includes(searchValue);
            });
            currentPage = 1;
            displayTable(currentPage);
            setupPagination(filteredData, paginationControls);
        });
        
        rowsPerPageSelect.addEventListener('change', (e) => {
            const selectedValue = e.target.value;
            if (selectedValue === 'all') {
                rowsPerPage = filteredData.length;
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
    });
</script>
@endpush
