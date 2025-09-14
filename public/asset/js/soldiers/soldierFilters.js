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

    ['rank-filter', 'company-filter', 'status-filter', 'skill-filter', 'course-filter', 'cadre-filter'].forEach(id => {
        document.getElementById(id).addEventListener('change', (e) => {
            const filterType = id.replace('-filter', '');
            manager.filters[filterType] = e.target.value;

            console.log(manager.filters[filterType]);

            manager.filterAndRender();
        });
    });

    document.getElementById('clear-filters').addEventListener('click', () => {
        manager.clearFilters();
    });
}
