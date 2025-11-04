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
            cmd: "",
            exArea: "",
            bloodGroup: ""
        };
        this.selectedRows = new Set();
        this.soldiers = [];
        this.filteredSoldiers = [];
        this.bulkActions = new SoldierBulkActions(this);
        this.isLoading = false;
        this.isRendering = false;
        this.renderBatchSize = 20;
        this.currentRenderFrame = null;
        this.renderAbortController = null;

        // NEW: Render queue to prevent conflicts
        this.renderQueue = Promise.resolve();
        this.pendingRender = false;
        this.rowCache = new Map(); // Row caching
        this.lastRenderedCount = 0;

        this.cacheElements();
        this.handleTableClick = this.handleTableClick.bind(this);
        this.handleCheckboxChange = this.handleCheckboxChange.bind(this);
        this.debouncedFilterAndRender = this.debounce(this.filterAndRender.bind(this), 200);
    }

    cacheElements() {
        this.elements = {
            tbody: document.getElementById('soldiers-tbody'),
            emptyState: document.getElementById('empty-state'),
            loadingState: document.getElementById('loading-state'),
            selectAll: document.getElementById('select-all'),
            searchInput: document.getElementById('search-input')
        };
    }

    debounce(func, wait) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    async init() {
        try {
            initFilters(this);
            initExportAndBulkActions(this);

            await this.loadData();

            if (this.elements.selectAll) {
                this.elements.selectAll.addEventListener("change", (e) => {
                    this.toggleSelectAllVisible(e.target.checked);
                });
            }

            document.getElementById("close-modal")?.addEventListener("click", () => {
                closeProfileModal();
            });

            document.getElementById('close-history-modal')?.addEventListener('click', () => {
                closeHistoryModal();
            });

            this.setupEventDelegation();
            this.setupDebouncedSearch();

        } catch (error) {
            console.error('Error initializing SoldierProfileManager:', error);
            showToast('Error initializing application', 'error');
        }
    }

    /**
     * FIXED: Toggle selection with proper state management
     */
    toggleSelectAllVisible(checked) {
        console.log(`Select All Visible: ${checked ? 'Selecting' : 'Deselecting'} ${this.filteredSoldiers.length} visible soldiers`);

        // Cancel any pending renders
        this.abortCurrentRender();

        if (checked) {
            // Select only visible soldiers
            this.filteredSoldiers.forEach(soldier => {
                this.selectedRows.add(soldier.id.toString());
            });
            showToast(`Selected ${this.filteredSoldiers.length} visible soldiers`, 'success');
        } else {
            // Deselect all
            this.selectedRows.clear();
            showToast('All selections cleared', 'info');
        }

        // Update UI synchronously
        this.syncUpdateCheckboxStates();
        this.bulkActions.updateBulkActionButton();
    }

    /**
     * NEW: Synchronous checkbox update (doesn't wait for render)
     */
    syncUpdateCheckboxStates() {
        const checkboxes = document.querySelectorAll('.row-select');
        checkboxes.forEach(checkbox => {
            const soldierId = checkbox.value;
            checkbox.checked = this.selectedRows.has(soldierId);
        });

        this.updateSelectAllState();
    }

    /**
     * UPDATED: Async-safe checkbox update
     */
    updateCheckboxStates() {
        // Use requestAnimationFrame to batch DOM updates
        this.syncUpdateCheckboxStates();

    }

    setupEventDelegation() {
        if (!this.elements.tbody) return;

        this.elements.tbody.removeEventListener('click', this.handleTableClick);
        this.elements.tbody.removeEventListener('change', this.handleCheckboxChange);

        this.elements.tbody.addEventListener('click', this.handleTableClick);
        this.elements.tbody.addEventListener('change', this.handleCheckboxChange);
    }

    handleTableClick(event) {
        const target = event.target;
        const button = target.closest('button');
        if (!button) return;

        const soldierId = button.dataset.id;
        if (!soldierId) return;

        if (button.classList.contains('btn-duty-history')) {
            openHistoryModal(soldierId, 'duty');
        }
        else if (button.classList.contains('btn-leave-history')) {
            openHistoryModal(soldierId, 'leave');
        }
        else if (button.classList.contains('btn-appointment-history')) {
            openHistoryModal(soldierId, 'appointment');
        }
        else if (button.classList.contains('btn-att-history')) {
            openHistoryModal(soldierId, 'att');
        }
        else if (button.classList.contains('btn-cmd-history')) {
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

    handleCheckboxChange(event) {
        const target = event.target;

        if (target.classList.contains('row-select')) {
            const soldierId = target.value;
            if (target.checked) {
                this.selectedRows.add(soldierId);
            } else {
                this.selectedRows.delete(soldierId);
            }

            this.updateSelectAllState();
            this.bulkActions.updateBulkActionButton();
        }
    }

    updateSelectAllState() {
        if (!this.elements.selectAll) return;

        const visibleCheckboxes = Array.from(document.querySelectorAll('.row-select'));
        const checkedCount = visibleCheckboxes.filter(cb => cb.checked).length;

        if (checkedCount === 0) {
            this.elements.selectAll.checked = false;
            this.elements.selectAll.indeterminate = false;
        } else if (checkedCount === visibleCheckboxes.length) {
            this.elements.selectAll.checked = true;
            this.elements.selectAll.indeterminate = false;
        } else {
            this.elements.selectAll.checked = false;
            this.elements.selectAll.indeterminate = true;
        }
    }

    setupDebouncedSearch() {
        if (!this.elements.searchInput) return;

        this.elements.searchInput.addEventListener('input', (e) => {
            this.filters.search = e.target.value;
            this.debouncedFilterAndRender();
        });
    }

    async loadData() {
        if (this.isLoading) return;
        this.isLoading = true;

        const startTime = performance.now();

        if (this.elements.loadingState) this.elements.loadingState.classList.remove('hidden');
        if (this.elements.emptyState) this.elements.emptyState.classList.add('hidden');
        if (this.elements.tbody) this.elements.tbody.innerHTML = '';

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

            this.preprocessSoldiers();

            const fetchTime = performance.now() - startTime;
            console.log(`✅ Data fetched in ${(fetchTime / 1000).toFixed(2)}s`);

            this.updateStats(data.stats);
            this.generateFiltersFromData();

            if (this.elements.loadingState) this.elements.loadingState.classList.add('hidden');

            if (!this.soldiers.length) {
                if (this.elements.emptyState) this.elements.emptyState.classList.remove('hidden');
                this.isLoading = false;
                return;
            }

            await this.optimizedRender();

            const totalTime = performance.now() - startTime;
            console.log(`✅ Total time: ${(totalTime / 1000).toFixed(2)}s`);

            setTimeout(() => {
                this.showSuccess(`✨ Loaded ${this.soldiers.length} soldiers in ${(totalTime / 1000).toFixed(1)}s`);
            }, 100);

        } catch (e) {
            console.error("Error loading soldiers", e);
            if (this.elements.loadingState) this.elements.loadingState.classList.add('hidden');
            if (this.elements.emptyState) this.elements.emptyState.classList.remove('hidden');
            showToast('Failed to load soldier data', 'error');
        } finally {
            this.isLoading = false;
        }
    }

    preprocessSoldiers() {
        this.soldiers.forEach(soldier => {
            soldier._searchStr = `${soldier.name || ''} ${soldier.army_no || ''} ${soldier.mobile || ''} ${soldier.family_mobile_1 || ''} ${soldier.family_mobile_2 || ''}`.toLowerCase();
            soldier.is_leave = Boolean(soldier.is_leave);
            soldier._skillSet = new Set(soldier.cocurricular?.map(s => s.name) || []);
            soldier._courseSet = new Set(soldier.courses?.map(c => c.name) || []);
            soldier._cadreSet = new Set(soldier.cadres?.map(c => c.name) || []);
            soldier._attSet = new Set(soldier.att?.map(a => a.name) || []);
            soldier._educationSet = new Set(soldier.educations?.map(e => e.name) || []);
            soldier._ere = new Set(soldier.ere?.map(e => e.name) || []);
            soldier._cmd = new Set(soldier.cmd?.map(c => c.name) || []);
            soldier._exAreaSet = new Set(soldier.ex_areas?.map(e => e.name) || []);
        });
    }

    updateStats(stats) {
        if (!stats) return;

        const totalCount = document.getElementById('total-count');
        const activeCount = document.getElementById('active-count');
        const leaveCount = document.getElementById('leave-count');
        const ereCount = document.getElementById('ere-count');

        if (totalCount) totalCount.textContent = stats.total || 0;
        if (activeCount) activeCount.textContent = stats.active || 0;
        if (leaveCount) leaveCount.textContent = stats.leave || 0;
        if (ereCount) ereCount.textContent = stats.with_ere || 0;
    }

    /**
     * FIXED: Queue-based rendering to prevent conflicts
     */
    async optimizedRender() {
        // Cancel any current render
        this.abortCurrentRender();

        this.isRendering = true;
        this.renderAbortController = new AbortController();

        try {
            const filtered = this.applyFilters(this.soldiers);
            this.filteredSoldiers = filtered;

            console.log('🎯 Filter results:', {
                total: this.soldiers.length,
                filtered: filtered.length
            });

            if (!this.elements.tbody) return;

            if (filtered.length === 0) {
                if (this.elements.emptyState) this.elements.emptyState.classList.remove('hidden');
                this.elements.tbody.innerHTML = '';
                return;
            }

            if (this.elements.emptyState) this.elements.emptyState.classList.add('hidden');

            await this.batchRenderOptimized(filtered);

        } catch (error) {
            console.error('❌ Render error:', error);
        } finally {
            this.isRendering = false;
        }
    }

    /**
     * NEW: Internal render method
     */
    async _doRender() {
        if (this.isRendering) {
            throw new Error('Render already in progress');
        }

        this.isRendering = true;
        this.renderAbortController = new AbortController();

        try {
            const filtered = this.applyFilters(this.soldiers);
            this.filteredSoldiers = filtered;

            console.log('🎯 Filter results:', {
                total: this.soldiers.length,
                filtered: filtered.length,
                previousRender: this.lastRenderedCount
            });

            if (!this.elements.tbody) {
                throw new Error('Table body not found');
            }

            if (filtered.length === 0) {
                console.log('📭 No soldiers found after filtering');
                if (this.elements.emptyState) this.elements.emptyState.classList.remove('hidden');
                this.elements.tbody.innerHTML = '';
                return;
            }

            if (this.elements.emptyState) this.elements.emptyState.classList.add('hidden');

            await this.batchRenderOptimized(filtered);

        } catch (error) {
            console.error('❌ Render error:', error);
            throw error;
        } finally {
            this.isRendering = false;
        }
    }

    abortCurrentRender() {
        if (this.renderAbortController) {
            this.renderAbortController.abort();
        }
        if (this.currentRenderFrame) {
            cancelAnimationFrame(this.currentRenderFrame);
        }
    }

    /**
     * FIXED: Improved batch rendering with better state management
     */
    async batchRenderOptimized(soldiers) {
        const tbody = this.elements.tbody;
        if (!tbody) return;

        // Clear existing content
        tbody.innerHTML = '';
        if (soldiers.length === 0) {
            console.log('No soldiers to render');
            return;
        }
        const batchSize = this.renderBatchSize;
        let currentIndex = 0;
        const signal = this.renderAbortController?.signal;

        // Store checkbox states before rendering
        const checkboxStates = new Map();
        this.selectedRows.forEach(id => checkboxStates.set(id, true));

        const renderNextBatch = () => {
            if (signal?.aborted) {
                console.log('Render aborted');
                return;
            }

            const startTime = performance.now();
            const endIndex = Math.min(currentIndex + batchSize, soldiers.length);

            // Use DocumentFragment for batch DOM insertion
            const fragment = document.createDocumentFragment();

            let renderedCount = 0;
            for (let i = currentIndex; i < endIndex; i++) {
                if (signal?.aborted) break;

                const row = this.createSoldierRow(soldiers[i]);
                fragment.appendChild(row);
                renderedCount++;
            }

            // Single DOM operation per batch
            tbody.appendChild(fragment);

            currentIndex = endIndex;
            const batchTime = performance.now() - startTime;

            console.log(`🔄 Batch ${Math.ceil(currentIndex / batchSize)}: ${renderedCount} rows in ${batchTime.toFixed(2)}ms`);

            if (currentIndex < soldiers.length) {
                // Use setTimeout to allow UI thread to breathe
                setTimeout(renderNextBatch, 10);
            } else {
                // Finished rendering all soldiers
                console.log(`✅ Finished rendering all ${soldiers.length} soldiers`);
                this.lastRenderedCount = soldiers.length;

                // Update UI states after render complete
                this.syncUpdateCheckboxStates();
                this.bulkActions.updateBulkActionButton();

                // Reattach event listeners
                this.setupEventDelegation();
            }
        };
        setTimeout(renderNextBatch, 0);


    }

    async filterAndRender() {
        // Prevent multiple simultaneous renders
        if (this.isRendering) {
            console.log('⏳ Render in progress, queuing request...');
            this.pendingRender = true;
            return;
        }

        console.log('🔍 Starting filter and render process...');

        try {
            await this.optimizedRender();
        } catch (error) {
            console.error('❌ Error in filterAndRender:', error);
            this.isRendering = false;
            this.pendingRender = false;
        }

        // Check if another render was requested during this render
        if (this.pendingRender) {
            console.log('🔄 Processing pending render request...');
            this.pendingRender = false;
            setTimeout(() => this.filterAndRender(), 50);
        }
    }

    forceRerender() {
        this.abortCurrentRender();
        if (this.elements.tbody) {
            this.elements.tbody.innerHTML = '';
        }
        if (this.elements.emptyState) {
            this.elements.emptyState.classList.add('hidden');
        }
        this.optimizedRender();
    }

    createSoldierRow(soldier) {
        // Check cache first for better performance
        const cacheKey = `${soldier.id}_${JSON.stringify(this.filters)}`;
        if (this.rowCache.has(cacheKey)) {
            return this.rowCache.get(cacheKey).cloneNode(true);
        }

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 transition-colors duration-150';
        tr.dataset.soldierId = soldier.id;
        tr.innerHTML = renderTableRow(soldier);

        // Cache the row
        this.rowCache.set(cacheKey, tr.cloneNode(true));

        // Prevent cache from growing too large
        if (this.rowCache.size > 1000) {
            const firstKey = this.rowCache.keys().next().value;
            this.rowCache.delete(firstKey);
        }

        return tr;
    }

    generateFiltersFromData() {
        const filterData = {
            ranks: new Map(),
            companies: new Map(),
            skills: new Map(),
            courses: new Map(),
            cadres: new Map(),
            atts: new Map(),
            educations: new Map(),
            districts: new Map(),
            bloodGroups: new Map(),
            cmds: new Map(),
            exAreas: new Map(),
            eres: new Map(),
        };

        this.soldiers.forEach(s => {
            if (s.rank) filterData.ranks.set(s.rank, true);
            if (s.unit) filterData.companies.set(s.unit, true);
            if (s.blood_group) filterData.bloodGroups.set(s.blood_group, true);
            if (s.districts) filterData.districts.set(s.districts, true);
            if (s.ere) filterData.eres.set(s.eres, true);

            s.cocurricular?.forEach(skill => {
                if (skill.name) filterData.skills.set(skill.name, true);
            });
            s.courses?.forEach(course => {
                if (course.name) filterData.courses.set(course.name, true);
            });
            s.cadres?.forEach(cadre => {
                if (cadre.name) filterData.cadres.set(cadre.name, true);
            });
            s.att?.forEach(att => {
                if (att.name) filterData.atts.set(att.name, true);
            });
            s.cmd?.forEach(cmd => {
                if (cmd.name) filterData.cmds.set(cmd.name, true);
            });
            s.ex_areas?.forEach(exArea => {
                if (exArea.name) filterData.exAreas.set(exArea.name, true);
            });
            s.educations?.forEach(education => {
                if (education.name) filterData.educations.set(education.name, true);
            });
        });

        this.populateSelect('rank-filter', filterData.ranks.keys());
        this.populateSelect('company-filter', filterData.companies.keys());
        this.populateSelect('skill-filter', filterData.skills.keys());
        this.populateSelect('course-filter', filterData.courses.keys());
        this.populateSelect('cadre-filter', filterData.cadres.keys());
        this.populateSelect('att-filter', filterData.atts.keys());
        this.populateSelect('education-filter', filterData.educations.keys());
        this.populateSelect('district-filter', filterData.districts.keys());
        this.populateSelect('bloodGroup-filter', filterData.bloodGroups.keys());
        this.populateSelect('cmd-filter', filterData.cmds.keys());
        this.populateSelect('exArea-filter', filterData.exAreas.keys());
        this.populateSelect('ere-filter', filterData.eres.keys());
    }

    populateSelect(selectId, items) {
        const select = document.getElementById(selectId);
        if (!select) return;

        const sortedItems = Array.from(items).filter(item => item && item.trim() !== '').sort();

        const fragment = document.createDocumentFragment();
        sortedItems.forEach(item => {
            const option = document.createElement('option');
            option.value = item;
            option.textContent = item;
            fragment.appendChild(option);
        });

        Array.from(select.options).slice(1).forEach(opt => opt.remove());
        select.appendChild(fragment);
    }

    applyFilters(soldiers) {
        console.log('🎯 applyFilters called with:', {
            totalSoldiers: soldiers.length,
            activeFilters: Object.entries(this.filters).filter(([key, value]) =>
                value && value !== '' && (!Array.isArray(value) || value.length > 0)
            )
        });

        let filtered = soldiers;

        if (this.filters.search) {
            const term = this.filters.search.toLowerCase().trim();
            const beforeSearch = filtered.length;
            filtered = filtered.filter(s => {
                if (s._searchStr && s._searchStr.includes(term)) return true;
                const mobileFields = [s.mobile, s.family_mobile_1, s.family_mobile_2].filter(Boolean);
                return mobileFields.some(mobile => mobile.toLowerCase().includes(term));
            });
            console.log(`📊 Search reduced from ${beforeSearch} to ${filtered.length} soldiers`);
        }

        if (this.filters.rank && (Array.isArray(this.filters.rank) ? this.filters.rank.length > 0 : this.filters.rank !== '')) {
            const ranks = Array.isArray(this.filters.rank) ? this.filters.rank : [this.filters.rank];
            filtered = filtered.filter(s => ranks.includes(s.rank));
        }

        if (this.filters.company && (Array.isArray(this.filters.company) ? this.filters.company.length > 0 : this.filters.company !== '')) {
            const companies = Array.isArray(this.filters.company) ? this.filters.company : [this.filters.company];
            filtered = filtered.filter(s => companies.includes(s.unit));
        }

        if (this.filters.district && (Array.isArray(this.filters.district) ? this.filters.district.length > 0 : this.filters.district !== '')) {
            const districts = Array.isArray(this.filters.district) ? this.filters.district : [this.filters.district];
            filtered = filtered.filter(s => districts.includes(s.districts));
        }

        if (this.filters.bloodGroup && (Array.isArray(this.filters.bloodGroup) ? this.filters.bloodGroup.length > 0 : this.filters.bloodGroup !== '')) {
            const bloodGroups = Array.isArray(this.filters.bloodGroup) ? this.filters.bloodGroup : [this.filters.bloodGroup];
            filtered = filtered.filter(s => bloodGroups.includes(s.blood_group));
        }

        if (this.filters.skill && (Array.isArray(this.filters.skill) ? this.filters.skill.length > 0 : this.filters.skill !== '')) {
            const skills = Array.isArray(this.filters.skill) ? this.filters.skill : [this.filters.skill];
            filtered = filtered.filter(s =>
                s._skillSet && skills.some(skill => s._skillSet.has(skill))
            );
        }

        if (this.filters.course && (Array.isArray(this.filters.course) ? this.filters.course.length > 0 : this.filters.course !== '')) {
            const courses = Array.isArray(this.filters.course) ? this.filters.course : [this.filters.course];
            filtered = filtered.filter(s =>
                s._courseSet && courses.some(course => s._courseSet.has(course))
            );
        }

        if (this.filters.cadre && (Array.isArray(this.filters.cadre) ? this.filters.cadre.length > 0 : this.filters.cadre !== '')) {
            const cadres = Array.isArray(this.filters.cadre) ? this.filters.cadre : [this.filters.cadre];
            filtered = filtered.filter(s =>
                s._cadreSet && cadres.some(cadre => s._cadreSet.has(cadre))
            );
        }

        if (this.filters.att && (Array.isArray(this.filters.att) ? this.filters.att.length > 0 : this.filters.att !== '')) {
            const atts = Array.isArray(this.filters.att) ? this.filters.att : [this.filters.att];
            filtered = filtered.filter(s =>
                s._attSet && atts.some(att => s._attSet.has(att))
            );
        }

        if (this.filters.education && (Array.isArray(this.filters.education) ? this.filters.education.length > 0 : this.filters.education !== '')) {
            const educations = Array.isArray(this.filters.education) ? this.filters.education : [this.filters.education];
            filtered = filtered.filter(s =>
                s._educationSet && educations.some(education => s._educationSet.has(education))
            );
        }

        if (this.filters.ere && (Array.isArray(this.filters.ere) ? this.filters.ere.length > 0 : this.filters.ere !== '')) {
            const eres = Array.isArray(this.filters.ere) ? this.filters.ere : [this.filters.ere];
            filtered = filtered.filter(s =>
                s._ere && eres.some(ere => s._ere.has(ere))
            );
        }

        if (this.filters.cmd && (Array.isArray(this.filters.cmd) ? this.filters.cmd.length > 0 : this.filters.cmd !== '')) {
            const cmds = Array.isArray(this.filters.cmd) ? this.filters.cmd : [this.filters.cmd];
            filtered = filtered.filter(s =>
                s._cmd && cmds.some(cmd => s._cmd.has(cmd))
            );
        }

        if (this.filters.exArea && (Array.isArray(this.filters.exArea) ? this.filters.exArea.length > 0 : this.filters.exArea !== '')) {
            const exAreas = Array.isArray(this.filters.exArea) ? this.filters.exArea : [this.filters.exArea];
            filtered = filtered.filter(s =>
                s._exAreaSet && exAreas.some(exArea => s._exAreaSet.has(exArea))
            );
        }

        if (this.filters.leave && Array.isArray(this.filters.leave) && this.filters.leave.length > 0) {
            filtered = filtered.filter(soldier => {
                const isOnLeave = soldier.is_leave === true;

                if (this.filters.leave.includes('on-leave') && this.filters.leave.includes('present')) {
                    return true;
                } else if (this.filters.leave.includes('on-leave')) {
                    return isOnLeave;
                } else if (this.filters.leave.includes('present')) {
                    return !isOnLeave;
                }

                return true;
            });
        }

        console.log(`✅ Final filtered count: ${filtered.length} soldiers`);
        return filtered;
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
            cmd: "",
            exArea: "",
            bloodGroup: ""
        };

        if (this.elements.searchInput) {
            this.elements.searchInput.value = '';
        }

        this.selectedRows.clear();
        if (this.elements.selectAll) {
            this.elements.selectAll.checked = false;
            this.elements.selectAll.indeterminate = false;
        }
        this.rowCache.clear();

        console.log('All filters and selections cleared');
        this.filterAndRender();
    }

    getSelectedSoldiers() {
        return this.filteredSoldiers.filter(soldier =>
            this.selectedRows.has(soldier.id.toString())
        );
    }
    destroy() {
        this.abortCurrentRender();
        this.rowCache.clear();
        this.selectedRows.clear();

        // Clear any event listeners
        if (this.elements.tbody) {
            this.elements.tbody.removeEventListener('click', this.handleTableClick);
            this.elements.tbody.removeEventListener('change', this.handleCheckboxChange);
        }

        console.log('🧹 SoldierProfileManager cleaned up');
    }
    getSelectionStats() {
        const selected = this.selectedRows.size;
        const visible = this.filteredSoldiers.length;
        const total = this.soldiers.length;

        return {
            selected,
            visible,
            total,
            percentage: visible > 0 ? ((selected / visible) * 100).toFixed(1) : 0
        };
    }

    retryLoadHistory(soldierId, type) {
        openHistoryModal(soldierId, type);
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

    debugFilters() {
        console.log('=== FILTER DEBUG ===');
        console.log('Current filters:', this.filters);
        console.log(`Total soldiers: ${this.soldiers.length}`);
        console.log(`Filtered soldiers: ${this.filteredSoldiers.length}`);
        console.log(`Selected soldiers: ${this.selectedRows.size}`);
        console.log('=== END DEBUG ===');
    }
}
