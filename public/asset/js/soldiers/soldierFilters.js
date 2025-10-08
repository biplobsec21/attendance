// public/js/soldiers/soldierFilters.js
export function initFilters(manager) {
    let searchTimeout;

    document.getElementById('search-input').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            manager.filters.search = e.target.value;
            manager.filterAndRender();
        }, 300);
    });

    // Add ERE filter to the list of filters
    const filterIds = ['rank-filter', 'company-filter', 'status-filter', 'skill-filter', 'course-filter', 'cadre-filter', 'ere-filter', 'att-filter', 'education-filter'];
    filterIds.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', (e) => {
                const filterType = id.replace('-filter', '');
                manager.filters[filterType] = e.target.value;
                console.log(`Filter changed: ${filterType} = ${e.target.value}`);
                manager.filterAndRender();
            });
        } else {
            console.warn(`Element with ID ${id} not found`);
        }
    });

    document.getElementById('clear-filters').addEventListener('click', () => {
        manager.clearFilters();
    });
}
