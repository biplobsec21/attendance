import { MultiSelect } from "./multiSelect.js";

export function initFilters(manager) {
    let searchTimeout;
    const multiSelects = {};

    console.log('🔧 Initializing filters with manager:', !!manager);

    // Initialize multi-select components for filter categories
    const initMultiSelects = () => {
        console.log('🔄 Initializing multi-select components...');

        const multiSelectConfigs = [
            { id: 'rank', placeholder: 'Select Rank...' },
            { id: 'company', placeholder: 'Select Company...' },
            { id: 'skill', placeholder: 'Select Skills...' },
            { id: 'course', placeholder: 'Select Courses...' },
            { id: 'cadre', placeholder: 'Select Cadres...' },
            { id: 'att', placeholder: 'Select ATT...' },
            { id: 'education', placeholder: 'Select Education...' },
            { id: 'district', placeholder: 'Select District...' },
            { id: 'bloodGroup', placeholder: 'Select Blood Group...' },
            { id: 'ere', placeholder: 'Select ERE Status...' },
            { id: 'cmd', placeholder: 'Select CMD...' },
            { id: 'exArea', placeholder: 'Select Ex-Areas...' },
            {
                id: 'leave',
                placeholder: 'Select Leave Status...',
                predefinedOptions: [
                    { value: 'on-leave', label: 'On Leave' },
                    { value: 'present', label: 'Present' }
                ]
            }
        ];

        multiSelectConfigs.forEach(config => {
            const containerId = `${config.id}-filter-container`;
            const container = document.getElementById(containerId);

            console.log(`🔧 Setting up ${config.id} filter:`, {
                containerId,
                containerExists: !!container
            });

            if (container) {
                try {
                    multiSelects[config.id] = new MultiSelect(containerId, {
                        placeholder: config.placeholder,
                        onChange: (selectedValues) => {
                            console.log(`🎯 ${config.id} filter changed:`, selectedValues);
                            manager.filters[config.id] = selectedValues.length > 0 ? selectedValues : '';
                            updateFilterActiveStates();
                            updateActiveFiltersSummary();
                            updateFilterCount();
                            manager.filterAndRender();
                        }
                    });
                    // Set predefined options for leave filter immediately
                    if (config.id === 'leave' && config.predefinedOptions) {
                        console.log('📝 Setting predefined options for leave filter:', config.predefinedOptions);
                        multiSelects.leave.setOptions(config.predefinedOptions);
                    }
                    console.log(`✅ ${config.id} multi-select initialized successfully`);
                } catch (error) {
                    console.error(`❌ Failed to initialize ${config.id} multi-select:`, error);
                }
            } else {
                console.warn(`⚠️ Container not found for ${config.id}: #${containerId}`);
            }

        });

        console.log('📊 Multi-select initialization complete:', {
            initialized: Object.keys(multiSelects),
            total: multiSelectConfigs.length
        });
    };

    // Function to update active filters summary
    const updateActiveFiltersSummary = () => {
        const summaryElement = document.getElementById('active-filters-summary');
        const filtersListElement = document.getElementById('active-filters-list');

        if (!summaryElement || !filtersListElement) return;

        const filteredSoldiers = manager.applyFilters(manager.soldiers);
        const filteredCount = filteredSoldiers.length;
        const totalCount = manager.soldiers.length;

        const activeFilters = [];
        const filterLabels = {
            search: 'Search',
            rank: 'Rank',
            company: 'Company',
            skill: 'Skill',
            course: 'Course',
            cadre: 'Cadre',
            ere: 'ERE',
            att: 'ATT',
            education: 'Education',
            leave: 'Leave',
            district: 'District',
            cmd: 'CMD',
            exArea: 'Ex-Areas',
            bloodGroup: 'Blood Group'
        };

        Object.entries(manager.filters).forEach(([key, value]) => {
            if (value && value !== '') {
                if (Array.isArray(value) && value.length > 0) {
                    const selectedLabels = value.map(val => {
                        if (key === 'leave') {
                            return val === 'on-leave' ? 'On Leave' : 'Present';
                        }
                        return val;
                    });

                    activeFilters.push({
                        key,
                        label: filterLabels[key] || key,
                        value: selectedLabels.join(', ')
                    });
                } else if (typeof value === 'string' && value !== '') {
                    activeFilters.push({
                        key,
                        label: filterLabels[key] || key,
                        value: value
                    });
                }
            }
        });

        // FIXED: Only show summary if there are actual active filters or non-empty search
        const hasActiveSearch = manager.filters.search && manager.filters.search.trim() !== '';
        if (activeFilters.length > 0 || hasActiveSearch) {
            summaryElement.classList.remove('hidden');
            filtersListElement.innerHTML = activeFilters.map(filter => `
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center space-x-1">
                <span class="truncate max-w-[200px]">${filter.label}: ${filter.value}</span>
                <button class="clear-single-filter ml-1 hover:text-blue-900 flex-shrink-0" data-filter="${filter.key}">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </span>
        `).join('');

            let counterElement = document.getElementById('filtered-counter');
            if (!counterElement) {
                counterElement = document.createElement('div');
                counterElement.id = 'filtered-counter';
                counterElement.className = 'text-green-700 text-sm font-medium mt-2';
                filtersListElement.parentNode.appendChild(counterElement);
            }
            counterElement.textContent = `Showing ${filteredCount} of ${totalCount} soldiers`;

            document.querySelectorAll('.clear-single-filter').forEach(button => {
                button.addEventListener('click', (e) => {
                    const filterKey = e.target.closest('button').dataset.filter;
                    manager.filters[filterKey] = '';

                    // Clear multi-select if it exists
                    if (multiSelects[filterKey]) {
                        multiSelects[filterKey].clear();
                    }

                    if (filterKey === 'search') {
                        const searchInput = document.getElementById('search-input');
                        if (searchInput) searchInput.value = '';
                    }

                    updateActiveFiltersSummary();
                    updateFilterActiveStates();
                    updateFilterCount();
                    manager.filterAndRender();
                });
            });

        } else {
            summaryElement.classList.add('hidden');
            filtersListElement.innerHTML = '';
            const counterElement = document.getElementById('filtered-counter');
            if (counterElement) counterElement.remove();
        }
    };

    // Function to update filter active states
    const updateFilterActiveStates = () => {
        // Handle multi-select filters
        Object.keys(multiSelects).forEach(filterType => {
            const multiSelect = multiSelects[filterType];
            if (multiSelect) {
                const filterValue = manager.filters[filterType];
                const hasSelection = filterValue &&
                    (Array.isArray(filterValue) ? filterValue.length > 0 : filterValue !== '');

                if (hasSelection) {
                    multiSelect.elements.input.classList.add('filter-active');
                } else {
                    multiSelect.elements.input.classList.remove('filter-active');
                }
            }
        });

        // Handle search input
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            const hasSearchValue = manager.filters.search && manager.filters.search.trim() !== '';
            if (hasSearchValue) {
                searchInput.classList.add('filter-active');
            } else {
                searchInput.classList.remove('filter-active');
            }
        }
    };

    // Update filter options when data is loaded
    const updateMultiSelectOptions = () => {
        Object.keys(multiSelects).forEach(filterType => {
            // Skip ERE and Leave as they have predefined options
            if (filterType === 'leave') return;

            const options = getOptionsForFilterType(filterType, manager.soldiers);
            multiSelects[filterType].setOptions(options);
        });
    };

    // Helper function to get options for each filter type
    const getOptionsForFilterType = (filterType, soldiers) => {
        const options = new Set();

        soldiers.forEach(soldier => {
            switch (filterType) {
                case 'rank':
                    if (soldier.rank) options.add(soldier.rank);
                    break;
                case 'company':
                    if (soldier.unit) options.add(soldier.unit);
                    break;
                case 'skill':
                    soldier.cocurricular?.forEach(skill => {
                        if (skill.name) options.add(skill.name);
                    });
                    break;
                case 'course':
                    soldier.courses?.forEach(course => {
                        if (course.name) options.add(course.name);
                    });
                    break;
                case 'cadre':
                    soldier.cadres?.forEach(cadre => {
                        if (cadre.name) options.add(cadre.name);
                    });
                    break;
                case 'att':
                    soldier.att?.forEach(att => {
                        if (att.name) options.add(att.name);
                    });
                    break;
                case 'ere':
                    soldier.ere?.forEach(ere => {
                        if (ere.name) options.add(ere.name);
                    });
                    break;
                case 'education':
                    soldier.educations?.forEach(education => {
                        if (education.name) options.add(education.name);
                    });
                    break;
                case 'district':
                    if (soldier.districts) options.add(soldier.districts);
                    break;
                case 'cmd':
                    soldier.cmd?.forEach(cmd => {
                        if (cmd.name) options.add(cmd.name);
                    });
                    break;
                case 'exArea':
                    soldier.ex_areas?.forEach(exArea => {
                        if (exArea.name) options.add(exArea.name);
                    });
                    break;
                case 'bloodGroup':
                    if (soldier.blood_group) options.add(soldier.blood_group);
                    break;
            }
        });

        return Array.from(options)
            .filter(value => value && value.trim() !== '')
            .map(value => ({
                value: value,
                label: value
            }))
            .sort((a, b) => a.label.localeCompare(b.label));
    };

    // Update filter count badge
    function updateFilterCount() {
        let activeCount = 0;

        // Count search filter only if it has value
        if (manager.filters.search && manager.filters.search.trim() !== '') {
            activeCount++;
        }

        // Count multi-select filters only if they have selected values
        Object.keys(multiSelects).forEach(filterType => {
            const filterValue = manager.filters[filterType];
            if (filterValue &&
                (Array.isArray(filterValue) ? filterValue.length > 0 : filterValue !== '')) {
                activeCount++;
            }
        });

        const filterCount = document.getElementById('filter-count');
        const filteredCounter = document.getElementById('filtered-counter');

        console.log('=== FILTER COUNT DEBUG ===');
        console.log('Active filters count:', activeCount);
        console.log('Filter count element:', filterCount);
        console.log('Filter count element content:', filterCount?.textContent);
        console.log('Filtered counter element:', filteredCounter);
        console.log('Filtered counter content:', filteredCounter?.textContent);
        console.log('Total soldiers:', manager.soldiers?.length || 0);
        console.log('Filtered soldiers:', manager.applyFilters ? manager.applyFilters(manager.soldiers).length : 'N/A');
        console.log('=== END DEBUG ===');

        if (!filterCount) return;

        // Force set the correct value
        filterCount.textContent = activeCount.toString();

        if (activeCount > 0) {
            filterCount.classList.remove('hidden');
        } else {
            filterCount.classList.add('hidden');
        }
    }

    // Search input event
    document.getElementById('search-input')?.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchValue = e.target.value.trim();
            manager.filters.search = searchValue; // Set to empty string if no value

            updateFilterActiveStates();
            updateActiveFiltersSummary();
            updateFilterCount();
            manager.filterAndRender();
        }, 300);
    });

    // Clear all filters button
    document.getElementById('clear-all-filters')?.addEventListener('click', () => {
        manager.clearFilters();

        // Clear all multi-selects
        Object.values(multiSelects).forEach(multiSelect => {
            multiSelect.clear();
        });

        // Clear search input
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.value = '';
            searchInput.classList.remove('filter-active');
        }

        updateFilterActiveStates();
        updateActiveFiltersSummary();
        updateFilterCount();
        manager.filterAndRender();
    });

    // Initialize multi-selects when manager is ready
    setTimeout(() => {
        initMultiSelects();
        if (manager.soldiers && manager.soldiers.length > 0) {
            updateMultiSelectOptions();
        }
    }, 100);

    // Re-initialize when data is loaded
    const originalLoadData = manager.loadData.bind(manager);
    manager.loadData = async function () {
        await originalLoadData();
        setTimeout(() => {
            updateMultiSelectOptions();
        }, 150);
    };

    // Expose updateFilterCount for external use
    manager.updateFilterCount = updateFilterCount;

    // Initialize
    updateFilterActiveStates();
    updateActiveFiltersSummary();
    updateFilterCount();

    console.log('✅ Filters initialization complete');
}
