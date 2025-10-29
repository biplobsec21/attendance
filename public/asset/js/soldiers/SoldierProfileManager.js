import { renderTableRow } from "./soldierUI.js";
import { initFilters } from "./soldierFilters.js";
import { SoldierBulkActions } from "./soldierBulkActions.js";
import { openProfileModal, closeProfileModal } from "./soldierProfileModal.js";
import { initExportAndBulkActions } from "./soldierExportActions.js";
import { showToast } from "./soldierHelpers.js";
import { formatDate } from "./soldierHelpers.js";
import { openHistoryModal, closeHistoryModal } from "./soldierHistoryModal.js";

export default class SoldierProfileManager {
    constructor() {
        this.filters = {
            search: "",
            rank: "",
            company: "",
            status: "",
            skill: "",
            course: "",
            cadre: "",
            ere: "",
            att: "",
            education: "",
            leave: "",
            district: "",
            bloodGroup: ""
        };
        this.selectedRows = new Set();
        this.soldiers = [];
        this.bulkActions = new SoldierBulkActions(this);
        this.isLoading = false;
        this.renderBatchSize = 50;

        // Bind methods for event listeners
        this.handleTableClick = this.handleTableClick.bind(this);
        this.handleCheckboxChange = this.handleCheckboxChange.bind(this);
    }

    async init() {
        initFilters(this);
        initExportAndBulkActions(this);

        await this.loadData();

        // Select all checkbox
        document.getElementById("select-all").addEventListener("change", (e) => {
            this.bulkActions.toggleSelectAll(e.target.checked);
        });

        // Close modal buttons
        document.getElementById("close-modal")?.addEventListener("click", () => {
            closeProfileModal();
        });

        document.getElementById('close-history-modal')?.addEventListener('click', () => {
            closeHistoryModal();
        });

        // Setup event delegation
        this.setupEventDelegation();

        // Add debounced search for better performance
        this.setupDebouncedSearch();
    }

    /**
     * Setup event delegation for table interactions
     */
    setupEventDelegation() {
        const tbody = document.getElementById('soldiers-tbody');
        if (!tbody) return;

        // Remove existing listeners to prevent duplicates
        tbody.removeEventListener('click', this.handleTableClick);

        // Add single event listener for all table clicks
        tbody.addEventListener('click', this.handleTableClick);

        // Add event listener for checkbox changes
        tbody.addEventListener('change', this.handleCheckboxChange);
    }

    /**
     * Handle all table clicks using event delegation
     */
    handleTableClick(event) {
        const target = event.target;

        // Find the closest button element
        const button = target.closest('button');
        if (!button) return;

        const soldierId = button.dataset.id;
        if (!soldierId) return;

        // Handle different button types
        if (button.classList.contains('btn-duty-history')) {
            openHistoryModal(soldierId, 'duty');
        }
        else if (button.classList.contains('btn-leave-history')) {
            openHistoryModal(soldierId, 'leave');
        }
        else if (button.classList.contains('btn-appointment-history')) {
            openHistoryModal(soldierId, 'appointment');
        }
        else if (button.classList.contains('btn-leave')) {
            const soldier = this.soldiers.find((s) => s.id == soldierId);
            if (soldier) openProfileModal(soldier, "Leave details");
        }
        else if (button.classList.contains('btn-att-history')) { // Add this case
            openHistoryModal(soldierId, 'att');
        }
        else if (button.classList.contains('btn-cmd-history')) { // Add this case
            openHistoryModal(soldierId, 'cmd');
        }
        else if (button.classList.contains('view-btn')) {
            const url = routes.view.replace(':id', soldierId);
            window.open(url, "_blank");
        }
        else if (button.classList.contains('edit-btn')) {
            const url = routes.edit.replace(':id', soldierId);
            window.open(url, "_blank");
        }
        else if (button.classList.contains('delete-btn')) {
            this.bulkActions.deleteProfile(soldierId);
        }
    }

    /**
     * Handle checkbox changes using event delegation
     */
    handleCheckboxChange(event) {
        const target = event.target;

        if (target.classList.contains('row-select')) {
            const soldierId = target.value;
            if (target.checked) {
                this.selectedRows.add(soldierId);
            } else {
                this.selectedRows.delete(soldierId);
                document.getElementById("select-all").checked = false;
            }
            this.bulkActions.updateBulkActionButton();
        }
    }

    /**
     * Setup debounced search to avoid re-rendering on every keystroke
     */
    setupDebouncedSearch() {
        const searchInput = document.getElementById('search-input');
        if (!searchInput) return;

        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);

            // Show a subtle loading indicator
            searchInput.classList.add('opacity-50');

            searchTimeout = setTimeout(() => {
                this.filters.search = e.target.value;
                this.filterAndRender();
                searchInput.classList.remove('opacity-50');
            }, 300);
        });
    }

    async loadData() {
        if (this.isLoading) return;
        this.isLoading = true;

        const loadingState = document.getElementById('loading-state');
        const emptyState = document.getElementById('empty-state');
        const tbody = document.getElementById('soldiers-tbody');

        const startTime = performance.now();

        loadingState.classList.remove('hidden');
        emptyState.classList.add('hidden');
        tbody.innerHTML = '';

        try {
            const response = await fetch(routes.getAllSoldiers, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            this.soldiers = data.data || [];

            const fetchTime = performance.now() - startTime;
            console.log(`✅ Data fetched in ${(fetchTime / 1000).toFixed(2)}s`);

            this.updateStats(data.stats);
            this.generateFiltersFromData();

            loadingState.classList.add('hidden');

            if (!this.soldiers.length) {
                emptyState.classList.remove('hidden');
                this.isLoading = false;
                return;
            }

            const renderStartTime = performance.now();
            await this.progressiveRender();

            const totalTime = performance.now() - startTime;
            const renderTime = performance.now() - renderStartTime;

            console.log(`✅ Rendered ${this.soldiers.length} soldiers in ${(renderTime / 1000).toFixed(2)}s`);
            console.log(`✅ Total time: ${(totalTime / 1000).toFixed(2)}s`);

            setTimeout(() => {
                this.showSuccess(`✨ Loaded ${this.soldiers.length} soldiers in ${(totalTime / 1000).toFixed(1)}s`);
            }, 100);

        } catch (e) {
            console.error("Error loading soldiers", e);
            loadingState.classList.add('hidden');
            emptyState.classList.remove('hidden');
            showToast('Failed to load soldier data', 'error');
        } finally {
            this.isLoading = false;
        }
    }

    updateStats(stats) {
        if (!stats) return;

        document.getElementById('total-count').textContent = stats.total || 0;
        document.getElementById('active-count').textContent = stats.active || 0;
        document.getElementById('leave-count').textContent = stats.leave || 0;
        document.getElementById('ere-count').textContent = stats.with_ere || 0;
    }

    /**
     * Progressive rendering - renders soldiers in batches with small delays
     */
    /**
 * Progressive rendering - renders soldiers in batches with small delays
 */
    async progressiveRender() {
        const filtered = this.applyFilters(this.soldiers);

        console.log('Progressive render - filtered soldiers:', filtered.length);
        console.log('Current filters:', this.filters);

        const emptyState = document.getElementById('empty-state');
        const tbody = document.getElementById('soldiers-tbody');
        const loadingState = document.getElementById('loading-state');

        // Hide loading state if it's visible
        loadingState.classList.add('hidden');

        if (filtered.length === 0) {
            console.log('No soldiers found after filtering - showing empty state');
            emptyState.classList.remove('hidden');
            tbody.innerHTML = '';
            return;
        }

        console.log(`Rendering ${filtered.length} soldiers`);
        emptyState.classList.add('hidden');
        tbody.innerHTML = '';

        // Show initial message
        const loadingRow = this.createLoadingRow(0, filtered.length);
        tbody.appendChild(loadingRow);

        let currentIndex = 0;

        const renderNextBatch = async () => {
            if (currentIndex >= filtered.length) {
                const finalLoadingRow = document.getElementById('progressive-loading');
                if (finalLoadingRow) {
                    finalLoadingRow.remove();
                }
                this.bulkActions.updateBulkActionButton();
                console.log('Finished rendering all soldiers');
                return;
            }

            const batch = filtered.slice(currentIndex, currentIndex + this.renderBatchSize);

            // Create document fragment for batch rendering
            const fragment = document.createDocumentFragment();
            batch.forEach(soldier => {
                const row = this.createSoldierRow(soldier);
                fragment.appendChild(row);
            });

            const loadingRowRef = document.getElementById('progressive-loading');
            if (loadingRowRef) {
                tbody.removeChild(loadingRowRef);
            }

            tbody.appendChild(fragment);

            currentIndex += this.renderBatchSize;

            if (currentIndex < filtered.length) {
                const newLoadingRow = this.createLoadingRow(currentIndex, filtered.length);
                tbody.appendChild(newLoadingRow);
            }

            if (currentIndex < filtered.length) {
                requestAnimationFrame(() => {
                    setTimeout(renderNextBatch, 0);
                });
            } else {
                this.bulkActions.updateBulkActionButton();
            }
        };

        requestAnimationFrame(renderNextBatch);
    }
    async filterAndRender() {
        console.log('Filter and render called');
        console.log('Current filters state:', this.filters);

        // Force a complete re-render
        await this.progressiveRender();
    }
    forceRerender() {
        const tbody = document.getElementById('soldiers-tbody');
        const emptyState = document.getElementById('empty-state');
        const loadingState = document.getElementById('loading-state');

        // Clear everything first
        tbody.innerHTML = '';
        loadingState.classList.add('hidden');
        emptyState.classList.add('hidden');

        // Then re-render
        this.progressiveRender();
    }
    /**
     * Create loading indicator row
     */
    createLoadingRow(current, total) {
        const loadingRow = document.createElement('tr');
        loadingRow.id = 'progressive-loading';
        loadingRow.innerHTML = `
            <td colspan="5" class="text-center py-4">
                <div class="flex items-center justify-center space-x-2 text-gray-500">
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Loading soldiers... <strong>${current}</strong>/<strong>${total}</strong></span>
                </div>
            </td>
        `;
        return loadingRow;
    }

    /**
     * Create a single soldier row element
     */
    createSoldierRow(soldier) {
        const tr = document.createElement('tr');
        tr.innerHTML = renderTableRow(soldier);
        tr.className = 'hover:bg-gray-50 transition-colors duration-150';
        tr.dataset.soldierId = soldier.id;

        const actualRow = tr.querySelector('tr');
        if (actualRow) {
            actualRow.className = tr.className;
            actualRow.dataset.soldierId = soldier.id;
            return actualRow;
        }

        return tr;
    }

    generateFiltersFromData() {
        const ranks = new Set();
        const companies = new Set();
        const skills = new Set();
        const courses = new Set();
        const cadres = new Set();
        const atts = new Set();
        const educations = new Set();
        const districts = new Set();
        const bloodGroups = new Set();

        this.soldiers.forEach(s => {
            if (s.rank) ranks.add(s.rank);
            if (s.unit) companies.add(s.unit);
            if (s.blood_group) bloodGroups.add(s.blood_group);
            if (s.districts) districts.add(s.districts);

            if (Array.isArray(s.cocurricular)) {
                s.cocurricular.forEach(skill => {
                    if (skill.name) skills.add(skill.name);
                });
            }

            if (Array.isArray(s.courses)) {
                s.courses.forEach(course => {
                    if (course.name) courses.add(course.name);
                });
            }

            if (Array.isArray(s.cadres)) {
                s.cadres.forEach(cadre => {
                    if (cadre.name) cadres.add(cadre.name);
                });
            }

            if (Array.isArray(s.att)) {
                s.att.forEach(att => {
                    if (att.name) atts.add(att.name);
                });
            }

            if (Array.isArray(s.educations)) {
                s.educations.forEach(education => {
                    if (education.name) educations.add(education.name);
                });
            }
        });

        this.populateSelect('rank-filter', ranks);
        this.populateSelect('company-filter', companies);
        this.populateSelect('skill-filter', skills);
        this.populateSelect('course-filter', courses);
        this.populateSelect('cadre-filter', cadres);
        this.populateSelect('att-filter', atts);
        this.populateSelect('education-filter', educations);
        this.populateSelect('district-filter', districts);
        this.populateSelect('bloodGroup-filter', bloodGroups);
    }

    populateSelect(selectId, items) {
        const select = document.getElementById(selectId);
        if (!select) return;

        select.querySelectorAll('option:not([value=""])').forEach(opt => opt.remove());
        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item;
            option.textContent = item;
            select.appendChild(option);
        });
    }

    applyFilters(soldiers) {
        let filtered = [...soldiers];

        // Search filter (always applies)
        if (this.filters.search) {
            const term = this.filters.search.toLowerCase();
            filtered = filtered.filter(s =>
                s.name?.toLowerCase().includes(term) ||
                (s.army_no && s.army_no.toLowerCase().includes(term))
            );
        }

        // Define all possible category filters
        const categoryFilters = [
            'rank', 'company', 'status', 'skill', 'course',
            'cadre', 'ere', 'att', 'education', 'leave', 'district', 'bloodGroup'
        ];

        // Apply ALL active category filters using reduce
        filtered = categoryFilters.reduce((currentFiltered, filterType) => {
            if (this.filters[filterType] && this.filters[filterType] !== '') {
                const beforeCount = currentFiltered.length;
                const result = this.applySingleCategoryFilter(currentFiltered, filterType);
                console.log(`Applied ${filterType}=${this.filters[filterType]}: ${beforeCount} -> ${result.length} soldiers`);
                return result;
            }
            return currentFiltered;
        }, filtered);

        console.log(`Final filtered count: ${filtered.length} soldiers`);
        return filtered;
    }

    /**
     * Apply a single category filter
     */
    applySingleCategoryFilter(soldiers, filterType) {
        const filterValue = this.filters[filterType];

        console.log(`Filtering by ${filterType}: ${filterValue}`);

        switch (filterType) {
            case 'rank':
                return soldiers.filter(s => s.rank === filterValue);

            case 'company':
                return soldiers.filter(s => s.unit === filterValue);

            case 'status':
                return soldiers.filter(s => {
                    const status = s.is_leave ? 'leave' : (s.is_sick ? 'medical' : 'active');
                    return status === filterValue;
                });

            case 'skill':
                return soldiers.filter(soldier =>
                    soldier.cocurricular?.some(skill => skill.name === filterValue)
                );

            case 'course':
                const courseFilterValue = filterValue.toLowerCase().trim();
                return soldiers.filter(soldier =>
                    soldier.courses?.some(course =>
                        course.name.toLowerCase().trim() === courseFilterValue
                    )
                );

            case 'cadre':
                const cadreFilterValue = filterValue.toLowerCase().trim();
                return soldiers.filter(soldier =>
                    soldier.cadres?.some(cadre =>
                        cadre.name.toLowerCase().trim() === cadreFilterValue
                    )
                );

            case 'ere':
                console.log(`ERE Filter: value=${filterValue}, soldiers count before=${soldiers.length}`);
                const ereFiltered = soldiers.filter(soldier => {
                    const hasEre = soldier.has_ere === true;
                    console.log(`Soldier ${soldier.name} (${soldier.army_no}): has_ere=${soldier.has_ere}, matches=${filterValue === "true" ? hasEre : !hasEre}`);
                    // Fixed: Compare with "true" instead of "with-ere"
                    return filterValue === "true" ? hasEre : !hasEre;
                });
                console.log(`ERE Filter result: ${ereFiltered.length} soldiers`);
                return ereFiltered;

            case 'att':
                const attFilterValue = filterValue.toLowerCase().trim();
                return soldiers.filter(soldier =>
                    soldier.att?.some(att =>
                        att.name.toLowerCase().trim() === attFilterValue
                    )
                );

            case 'education':
                const educationFilterValue = filterValue.toLowerCase().trim();
                return soldiers.filter(soldier =>
                    soldier.educations?.some(education =>
                        education.name.toLowerCase().trim() === educationFilterValue
                    )
                );

            case 'leave':
                console.log(`Leave Filter: value=${filterValue}, soldiers count before=${soldiers.length}`);
                const leaveFiltered = soldiers.filter(soldier => {
                    const isOnLeave = soldier.is_leave === true;
                    console.log(`Soldier ${soldier.name} (${soldier.army_no}): is_leave=${soldier.is_leave}, matches=${filterValue === "on-leave" ? isOnLeave : !isOnLeave}`);
                    return filterValue === "on-leave" ? isOnLeave : !isOnLeave;
                });
                console.log(`Leave Filter result: ${leaveFiltered.length} soldiers`);
                return leaveFiltered;

            case 'district':
                return soldiers.filter(soldier => {
                    // Extract district from address - you might need to adjust this based on your data structure
                    const address = soldier.address || '';
                    const districtMatch = address.toLowerCase().includes(filterValue.toLowerCase());
                    console.log(`Soldier ${soldier.name}: address="${address}", district match=${districtMatch}`);
                    return districtMatch;
                });
            case 'bloodGroup':
                return soldiers.filter(soldier => soldier.blood_group === filterValue);
            default:
                return soldiers;
        }
    }

    async filterAndRender() {
        await this.progressiveRender();
    }

    clearFilters() {
        this.filters = {
            search: "",
            rank: "",
            company: "",
            status: "",
            skill: "",
            course: "",
            cadre: "",
            ere: "",
            att: "",
            education: "",
            leave: "",
            district: "",
            bloodGroup: ""
        };

        const inputs = {
            'search-input': '',
            'rank-filter': '',
            'company-filter': '',
            'status-filter': '',
            'skill-filter': '',
            'course-filter': '',
            'cadre-filter': '',
            'ere-filter': '',
            'att-filter': '',
            'education-filter': '',
            'leave-filter': '',
            'district-filter': '',
            'bloodGroup-filter': ''
        };

        Object.entries(inputs).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.value = value;
                console.log(`Reset ${id} to: ${value}`);
            }
        });

        console.log('All filters cleared, current state:', this.filters);
        this.filterAndRender();
    }

    retryLoadHistory(soldierId, type) {
        openHistoryModal(soldierId, type);
    }

    renderData(soldiers) {
        this.progressiveRender();
    }

    showModal(title, content) {
        let modal = document.getElementById('bulk-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'bulk-modal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden';
            modal.innerHTML = `
                <div class="bg-white rounded-lg shadow-lg w-96 p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 id="bulk-modal-title" class="font-bold text-lg"></h3>
                        <button id="bulk-modal-close" class="text-gray-500 hover:text-gray-800">&times;</button>
                    </div>
                    <div id="bulk-modal-content"></div>
                </div>
            `;
            document.body.appendChild(modal);

            document.getElementById('bulk-modal-close').addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        }

        document.getElementById('bulk-modal-title').textContent = title;
        document.getElementById('bulk-modal-content').innerHTML = content;
        modal.classList.remove('hidden');
    }

    closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('hidden');
    }

    showSuccess(msg) {
        showToast(msg, 'success');
    }

    showError(msg) {
        showToast(msg, 'error');
    }
    // Add this method to SoldierProfileManager class
    debugFilters() {
        console.log('=== FILTER DEBUG ===');
        console.log('Current filters:', this.filters);

        const filtered = this.applyFilters(this.soldiers);
        console.log(`Total soldiers: ${this.soldiers.length}`);
        console.log(`Filtered soldiers: ${filtered.length}`);

        // Check specific filters
        if (this.filters.company) {
            const companySoldiers = this.soldiers.filter(s => s.unit === this.filters.company);
            console.log(`Soldiers in company ${this.filters.company}: ${companySoldiers.length}`);
        }

        if (this.filters.ere) {
            const ereSoldiers = this.soldiers.filter(s =>
                this.filters.ere === "true" ? s.has_ere === true : s.has_ere === false
            );
            console.log(`Soldiers with ERE=${this.filters.ere}: ${ereSoldiers.length}`);
        }

        if (this.filters.district) {
            const districtSoldiers = this.soldiers.filter(s =>
                s.address && s.address.toLowerCase().includes(this.filters.district.toLowerCase())
            );
            console.log(`Soldiers from district ${this.filters.district}: ${districtSoldiers.length}`);
        }

        if (this.filters.bloodGroup) {
            const bloodGroupSoldiers = this.soldiers.filter(s => s.blood_group === this.filters.bloodGroup);
            console.log(`Soldiers with blood group ${this.filters.bloodGroup}: ${bloodGroupSoldiers.length}`);
        }

        console.log('=== END DEBUG ===');
    }
}
