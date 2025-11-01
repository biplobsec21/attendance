// search.js
class CourseCadreSearch {
    constructor() {
        this.searchInput = document.getElementById('searchInput');
        this.clearSearch = document.getElementById('clearSearch');
        this.init();
    }

    init() {
        if (this.searchInput && this.clearSearch) {
            this.bindEvents();
        }
    }

    bindEvents() {
        this.searchInput.addEventListener('input', (e) => {
            this.handleSearch(e.target.value);
        });

        this.clearSearch.addEventListener('click', () => {
            this.clearSearchHandler();
        });
    }

    handleSearch(searchTerm) {
        const term = searchTerm.toLowerCase().trim();

        if (term) {
            this.clearSearch.classList.remove('hidden');
            this.performSearch(term);
        } else {
            this.clearSearch.classList.add('hidden');
            this.clearSearchResults();
        }
    }

    clearSearchHandler() {
        this.searchInput.value = '';
        this.clearSearch.classList.add('hidden');
        this.clearSearchResults();
    }

    performSearch(searchTerm) {
        const allTabs = [
            'current-courses', 'previous-courses',
            'current-cadres', 'previous-cadres',
            'current-ex-areas', 'previous-ex-areas'
        ];

        let hasResults = false;

        allTabs.forEach(tabName => {
            const tableBody = document.getElementById(tabName.replace(/-/g, '') + 'TableBody');
            if (!tableBody) return;

            const rows = tableBody.querySelectorAll('tr');
            let tabHasResults = false;

            rows.forEach(row => {
                if (row.querySelector('td[colspan]')) {
                    return;
                }

                const soldierName = row.querySelector('td:nth-child(3) .text-sm.font-medium')?.textContent?.toLowerCase() || '';
                const soldierDetails = row.querySelector('td:nth-child(3) .text-sm.text-gray-500')?.textContent?.toLowerCase() || '';
                const assignmentName = row.querySelector('td:nth-child(4) .text-sm.font-medium')?.textContent?.toLowerCase() || '';
                const remarks = row.querySelector('td:nth-child(4) .text-sm.text-gray-500')?.textContent?.toLowerCase() || '';

                const searchableText = `${soldierName} ${soldierDetails} ${assignmentName} ${remarks}`;

                if (searchableText.includes(searchTerm)) {
                    row.style.display = '';
                    tabHasResults = true;
                    hasResults = true;
                } else {
                    row.style.display = 'none';
                }
            });

            this.updateEmptyState(tableBody, tabName, searchTerm);
        });

        this.updateTabCounts(searchTerm);
        return hasResults;
    }

    updateEmptyState(tableBody, tabName, searchTerm) {
        const emptyRow = tableBody.querySelector('tr td[colspan]');
        if (!emptyRow) return;

        const parentTable = emptyRow.closest('tbody');
        const visibleRows = Array.from(parentTable.querySelectorAll('tr')).filter(row =>
            row.style.display !== 'none' && !row.querySelector('td[colspan]')
        );

        if (visibleRows.length === 0 && searchTerm) {
            emptyRow.style.display = '';
            emptyRow.innerHTML = this.getSearchEmptyStateHTML(emptyRow.getAttribute('colspan'));
        } else if (visibleRows.length === 0) {
            emptyRow.style.display = '';
            emptyRow.innerHTML = this.getOriginalEmptyStateHTML(emptyRow.getAttribute('colspan'), tabName);
        } else {
            emptyRow.style.display = 'none';
        }
    }

    getSearchEmptyStateHTML(colspan) {
        return `
            <td colspan="${colspan}" class="px-6 py-12 text-center">
                <div class="text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <p class="text-lg font-medium">No matching results found</p>
                    <p class="text-sm">Try adjusting your search terms</p>
                </div>
            </td>
        `;
    }

    getOriginalEmptyStateHTML(colspan, tabName) {
        if (tabName.includes('current')) {
            const type = tabName.includes('course') ? 'courses' :
                tabName.includes('cadre') ? 'cadres' : 'ex-areas';
            return `
                <td colspan="${colspan}" class="px-6 py-12 text-center">
                    <div class="text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-lg font-medium">No ${tabName.replace('-', ' ')} found</p>
                        <p class="text-sm">Get started by creating a new ${type.slice(0, -1)} assignment</p>
                    </div>
                </td>
            `;
        } else {
            return `
                <td colspan="${colspan}" class="px-6 py-12 text-center">
                    <div class="text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-lg font-medium">No ${tabName.replace('-', ' ')} found</p>
                    </div>
                </td>
            `;
        }
    }

    updateTabCounts(searchTerm) {
        const tabButtons = document.querySelectorAll('.tab-button');

        tabButtons.forEach(button => {
            const tabName = button.getAttribute('data-tab');
            const countSpan = button.querySelector('.bg-blue-100, .bg-green-100, .bg-purple-100, .bg-gray-100');
            const originalCount = button.getAttribute('data-original-count') || countSpan.textContent.trim();

            if (!button.hasAttribute('data-original-count')) {
                button.setAttribute('data-original-count', originalCount);
            }

            if (searchTerm) {
                const tableBody = document.getElementById(tabName.replace(/-/g, '') + 'TableBody');
                if (tableBody) {
                    const visibleRows = Array.from(tableBody.querySelectorAll('tr')).filter(row =>
                        row.style.display !== 'none' && !row.querySelector('td[colspan]')
                    );
                    countSpan.textContent = visibleRows.length;
                    this.updateCountSpanStyle(countSpan, tabName, visibleRows.length);
                }
            } else {
                countSpan.textContent = originalCount;
                this.updateCountSpanStyle(countSpan, tabName, originalCount);
            }
        });
    }

    updateCountSpanStyle(countSpan, tabName, count) {
        countSpan.className = 'ml-2 py-0.5 px-2 rounded-full text-xs';

        if (count > 0) {
            if (tabName.includes('current-courses')) {
                countSpan.classList.add('bg-blue-100', 'text-blue-800');
            } else if (tabName.includes('current-cadres')) {
                countSpan.classList.add('bg-green-100', 'text-green-800');
            } else if (tabName.includes('current-ex-areas')) {
                countSpan.classList.add('bg-purple-100', 'text-purple-800');
            } else {
                countSpan.classList.add('bg-gray-100', 'text-gray-800');
            }
        } else {
            countSpan.classList.add('bg-gray-100', 'text-gray-800');
        }
    }

    clearSearchResults() {
        const allTabs = [
            'current-courses', 'previous-courses',
            'current-cadres', 'previous-cadres',
            'current-ex-areas', 'previous-ex-areas'
        ];

        allTabs.forEach(tabName => {
            const tableBody = document.getElementById(tabName.replace(/-/g, '') + 'TableBody');
            if (!tableBody) return;

            const rows = tableBody.querySelectorAll('tr');
            rows.forEach(row => {
                row.style.display = '';
            });

            this.restoreOriginalEmptyState(tableBody, tabName);
        });

        this.updateTabCounts('');
    }

    restoreOriginalEmptyState(tableBody, tabName) {
        const emptyRow = tableBody.querySelector('tr td[colspan]');
        if (emptyRow) {
            emptyRow.style.display = '';
            if (Array.from(tableBody.querySelectorAll('tr')).filter(row => !row.querySelector('td[colspan]')).length === 0) {
                emptyRow.innerHTML = this.getOriginalEmptyStateHTML(emptyRow.getAttribute('colspan'), tabName);
            } else {
                emptyRow.style.display = 'none';
            }
        }
    }
}

// Initialize search when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new CourseCadreSearch();
});
