import { renderTableRow } from "./soldierUI.js";
import { initFilters } from "./soldierFilters.js";
import { SoldierBulkActions } from "./soldierBulkActions.js";
import { openProfileModal, closeProfileModal } from "./soldierProfileModal.js";
import { initExportAndBulkActions } from "./soldierExportActions.js";
import { showToast } from "./soldierHelpers.js";

export default class SoldierProfileManager {
    constructor() {
        this.filters = { search: "", rank: "", company: "", status: "" };
        this.selectedRows = new Set();
        this.soldiers = [];
        this.bulkActions = new SoldierBulkActions(this);
    }

    async init() {
        initFilters(this);
        initExportAndBulkActions(this);

        await this.loadData();

        // Select all checkbox
        document.getElementById("select-all").addEventListener("change", (e) => {
            this.bulkActions.toggleSelectAll(e.target.checked);
        });

        // Close modal button (profile modal)
        document.getElementById("close-modal")?.addEventListener("click", () => {
            closeProfileModal();
        });
    }

    async loadData() {
        const loadingState = document.getElementById('loading-state');
        const emptyState = document.getElementById('empty-state');
        const tbody = document.getElementById('soldiers-tbody');

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

            this.generateFiltersFromData();

            loadingState.classList.add('hidden');

            if (!this.soldiers.length) {
                emptyState.classList.remove('hidden');
                return;
            }

            this.filterAndRender();

            // Update stats
            document.getElementById('total-count').textContent = data.stats?.total || 0;
            document.getElementById('active-count').textContent = data.stats?.active || 0;
            document.getElementById('leave-count').textContent = data.stats?.leave || 0;

        } catch (e) {
            console.error("Error loading soldiers", e);
            loadingState.classList.add('hidden');
            emptyState.classList.remove('hidden');
        }
    }

    generateFiltersFromData() {
        const ranks = new Set();
        const companies = new Set();

        this.soldiers.forEach(s => {
            if (s.rank) ranks.add(s.rank);
            if (s.unit) companies.add(s.unit);
        });

        const rankSelect = document.getElementById('rank-filter');
        const companySelect = document.getElementById('company-filter');

        if (rankSelect) {
            rankSelect.querySelectorAll('option:not([value=""])').forEach(opt => opt.remove());
            ranks.forEach(rank => {
                const option = document.createElement('option');
                option.value = rank;
                option.textContent = rank;
                rankSelect.appendChild(option);
            });
        }

        if (companySelect) {
            companySelect.querySelectorAll('option:not([value=""])').forEach(opt => opt.remove());
            companies.forEach(company => {
                const option = document.createElement('option');
                option.value = company;
                option.textContent = company;
                companySelect.appendChild(option);
            });
        }
    }

    filterAndRender() {
        let filtered = this.soldiers;

        if (this.filters.search) {
            const term = this.filters.search.toLowerCase();
            filtered = filtered.filter(s =>
                s.name?.toLowerCase().includes(term) ||
                (s.army_no && s.army_no.toLowerCase().includes(term))
            );
        }

        if (this.filters.rank) {
            filtered = filtered.filter(s => s.rank === this.filters.rank);
        }

        if (this.filters.company) {
            filtered = filtered.filter(s => s.unit === this.filters.company);
        }

        if (this.filters.status) {
            filtered = filtered.filter(s => {
                const status = s.is_leave ? 'leave' : (s.is_sick ? 'medical' : 'active');
                return status === this.filters.status;
            });
        }

        const emptyState = document.getElementById('empty-state');
        if (filtered.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }

        this.renderData(filtered);
    }

    renderData(soldiers) {
        const tbody = document.getElementById("soldiers-tbody");
        if (!tbody) return;

        tbody.innerHTML = soldiers.map((s) => renderTableRow(s)).join("");

        // Attach event listeners
        tbody.querySelectorAll(".view-btn").forEach((btn) => {
            btn.addEventListener("click", () => {
                const id = btn.dataset.id;
                // const soldier = soldiers.find((s) => s.id == id);
                const url = routes.view.replace(':id', id);
                window.open(url, "_blank"); // opens in a new tab
                //if (soldier) openProfileModal(soldier);
            });
        });

        // Attach event listeners
        tbody.querySelectorAll(".delete-btn").forEach((btn) => {
            btn.addEventListener("click", () => {
                const id = btn.dataset.id;
                if (id) this.bulkActions.deleteProfile(id);
            });
        });

        tbody.querySelectorAll(".edit-btn").forEach((btn) => {
            btn.addEventListener("click", () => {
                const id = btn.dataset.id;
                const url = routes.edit.replace(':id', id);
                window.open(url, "_blank"); // opens in a new tab
            });
        });


        tbody.querySelectorAll(".row-select").forEach((checkbox) => {
            checkbox.addEventListener("change", (e) => {
                const soldierId = checkbox.value;
                if (checkbox.checked) {
                    this.selectedRows.add(soldierId);
                } else {
                    this.selectedRows.delete(soldierId);
                    document.getElementById("select-all").checked = false;
                }
                this.bulkActions.updateBulkActionButton();
            });
        });

        this.bulkActions.updateBulkActionButton();
    }

    clearFilters() {
        this.filters = { search: "", rank: "", company: "", status: "" };

        const searchInput = document.getElementById('search-input');
        const rankSelect = document.getElementById('rank-filter');
        const companySelect = document.getElementById('company-filter');
        const statusSelect = document.getElementById('status-filter');

        if (searchInput) searchInput.value = '';
        if (rankSelect) rankSelect.value = '';
        if (companySelect) companySelect.value = '';
        if (statusSelect) statusSelect.value = '';

        this.filterAndRender();
    }

    // Tailwind Modal Functions
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
        // alert(msg);
        showToast(msg, 'success');
    }


    showError(msg) {
        // alert(msg);
        showToast(msg, 'error');
    }
}
