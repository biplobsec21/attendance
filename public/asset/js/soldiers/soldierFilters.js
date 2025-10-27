// public/js/soldiers/soldierFilters.js
export function initFilters(manager) {
    let searchTimeout;

    // Function to update active filters summary
    // Function to update active filters summary
    const updateActiveFiltersSummary = () => {
        const summaryElement = document.getElementById('active-filters-summary');
        const filtersListElement = document.getElementById('active-filters-list');

        if (!summaryElement || !filtersListElement) return;

        // Get filtered count
        const filteredSoldiers = manager.applyFilters(manager.soldiers);
        const filteredCount = filteredSoldiers.length;
        const totalCount = manager.soldiers.length;

        const activeFilters = [];
        const filterLabels = {
            search: 'Search',
            rank: 'Rank',
            company: 'Company',
            status: 'Status',
            skill: 'Skill',
            course: 'Course',
            cadre: 'Cadre',
            ere: 'ERE',
            att: 'ATT',
            education: 'Education',
            leave: 'Leave',
            district: 'District',
            bloodGroup: 'Blood Group'
        };

        Object.entries(manager.filters).forEach(([key, value]) => {
            if (value && value !== '') {
                activeFilters.push({
                    key,
                    label: filterLabels[key] || key,
                    value: value
                });
            }
        });

        if (activeFilters.length > 0 || manager.filters.search) {
            summaryElement.classList.remove('hidden');

            // Update filters list
            filtersListElement.innerHTML = activeFilters.map(filter => `
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center space-x-1">
                <span>${filter.label}: ${filter.value}</span>
                <button class="clear-single-filter ml-1 hover:text-blue-900" data-filter="${filter.key}">
                    <i class="fas fa-times"></i>
                </button>
            </span>
        `).join('');

            // Add or update counter
            let counterElement = document.getElementById('filtered-counter');
            if (!counterElement) {
                counterElement = document.createElement('div');
                counterElement.id = 'filtered-counter';
                counterElement.className = 'text-green-700 text-sm font-medium';
                // Insert counter after the filters list container
                filtersListElement.parentNode.insertBefore(counterElement, filtersListElement.nextSibling);
            }
            counterElement.textContent = `Showing ${filteredCount} of ${totalCount} soldiers`;

            // Re-attach event listeners for individual filter buttons
            document.querySelectorAll('.clear-single-filter').forEach(button => {
                button.addEventListener('click', (e) => {
                    const filterKey = e.target.closest('button').dataset.filter;
                    manager.filters[filterKey] = '';

                    const elementId = filterKey === 'bloodGroup' ? 'blood-group-filter' : `${filterKey}-filter`;
                    const formElement = document.getElementById(elementId);
                    if (formElement) formElement.value = '';

                    if (filterKey === 'search') {
                        const searchInput = document.getElementById('search-input');
                        if (searchInput) searchInput.value = '';
                    }

                    updateActiveFiltersSummary();
                    updateFilterActiveStates();
                    manager.filterAndRender();
                });
            });

        } else {
            summaryElement.classList.add('hidden');
            filtersListElement.innerHTML = '';

            // Remove counter if it exists
            const counterElement = document.getElementById('filtered-counter');
            if (counterElement) {
                counterElement.remove();
            }
            // const counterElement2 = document.getElementById('filter-count');
            // if (counterElement2) {
            //     counterElement2.remove();
            // }

        }
    };
    // Function to update filter active states (from Option 1)
    const updateFilterActiveStates = () => {
        const filterIds = [
            'rank-filter', 'company-filter', 'status-filter', 'skill-filter',
            'course-filter', 'cadre-filter', 'ere-filter', 'att-filter',
            'education-filter', 'leave-filter', 'district-filter', 'bloodGroup-filter'
        ];

        filterIds.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                const filterType = id.replace('-filter', '');
                const actualFilterType = filterType === 'blood-group' ? 'bloodGroup' : filterType;

                if (manager.filters[actualFilterType] && manager.filters[actualFilterType] !== '') {
                    element.classList.add('filter-active');
                    element.parentElement.classList.add('filter-container-active');
                } else {
                    element.classList.remove('filter-active');
                    element.parentElement.classList.remove('filter-container-active');
                }
            }
        });

        // Handle search input
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            if (manager.filters.search && manager.filters.search !== '') {
                searchInput.classList.add('filter-active');
                searchInput.parentElement.classList.add('filter-container-active');
            } else {
                searchInput.classList.remove('filter-active');
                searchInput.parentElement.classList.remove('filter-container-active');
            }
        }
    };

    // Search input event
    document.getElementById('search-input').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            manager.filters.search = e.target.value;
            updateFilterActiveStates();
            updateActiveFiltersSummary();
            manager.filterAndRender();
        }, 300);
    });

    const filterIds = [
        'rank-filter', 'company-filter', 'status-filter', 'skill-filter',
        'course-filter', 'cadre-filter', 'ere-filter', 'att-filter',
        'education-filter', 'leave-filter', 'district-filter', 'bloodGroup-filter'
    ];

    filterIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', (e) => {
                let filterType = id.replace('-filter', '');

                manager.filters[filterType] = e.target.value;

                updateFilterActiveStates();
                updateActiveFiltersSummary();

                console.log(`Filter changed: ${filterType} = ${e.target.value}`);
                manager.debugFilters();
                manager.forceRerender();
                // updateFilterCount();

            });
        }
    });

    // Clear all filters button
    document.getElementById('clear-all-filters')?.addEventListener('click', () => {
        manager.clearFilters();
        updateFilterActiveStates();
        updateActiveFiltersSummary();
        updateFilterCount(); // This will now work

    });
    function updateFilterCount() {
        const filters = document.querySelectorAll('#filters-sidebar select, #filters-sidebar input');
        let activeCount = 0;

        filters.forEach(filter => {
            if (filter.value && filter.value !== '' && filter.id !== 'search-input') {
                activeCount++;
            } else if (filter.id === 'search-input' && filter.value.trim() !== '') {
                activeCount++;
            }
        });

        const filterCount = document.getElementById('filter-count');
        if (!filterCount) return;

        if (activeCount > 0) {
            filterCount.textContent = activeCount;
            filterCount.classList.remove('hidden');
        } else {
            filterCount.classList.add('hidden');
        }
    }
    // Initialize
    updateFilterActiveStates();
    updateActiveFiltersSummary();
}
