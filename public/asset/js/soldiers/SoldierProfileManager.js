import { renderTableRow } from "./soldierUI.js";
import { initFilters } from "./soldierFilters.js";
import { SoldierBulkActions } from "./soldierBulkActions.js";
import { openProfileModal, closeProfileModal } from "./soldierProfileModal.js";
import { initExportAndBulkActions } from "./soldierExportActions.js";
import { showToast } from "./soldierHelpers.js";
import { formatDate } from "./soldierHelpers.js";
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
            ere: "all",
            att: "",
            education: "",
            leave: ""
        };
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
            document.getElementById('ere-count').textContent = data.stats?.with_ere || 0;

        } catch (e) {
            console.error("Error loading soldiers", e);
            loadingState.classList.add('hidden');
            emptyState.classList.remove('hidden');
        }
    }

    generateFiltersFromData() {
        const ranks = new Set();
        const companies = new Set();
        const skills = new Set();
        const courses = new Set();
        const cadres = new Set();
        const atts = new Set();
        const educations = new Set();

        this.soldiers.forEach(s => {
            if (s.rank) ranks.add(s.rank);
            if (s.unit) companies.add(s.unit);

            //skill
            if (Array.isArray(s.cocurricular)) {
                s.cocurricular.forEach(skill => {
                    if (skill.name) skills.add(skill.name);
                });
            }
            // courses
            if (Array.isArray(s.courses)) {
                s.courses.forEach(course => {
                    if (course.name) courses.add(course.name);
                });
            }
            //cadres
            if (Array.isArray(s.cadres)) {
                s.cadres.forEach(cadre => {
                    if (cadre.name) cadres.add(cadre.name);
                });
            }

            // ATT
            if (Array.isArray(s.att)) {
                s.att.forEach(att => {
                    if (att.name) atts.add(att.name);
                });
            }
            // Education
            if (Array.isArray(s.educations)) {
                s.educations.forEach(education => {
                    if (education.name) educations.add(education.name);
                });
            }

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
        // Skill filter
        const skillSelect = document.getElementById('skill-filter');
        if (skillSelect) {
            skillSelect.querySelectorAll('option:not([value=""])').forEach(opt => opt.remove());
            skills.forEach(skill => {
                const option = document.createElement('option');
                option.value = skill;
                option.textContent = skill;
                skillSelect.appendChild(option);
            });
        }

        // Course filter
        const courseSelect = document.getElementById('course-filter');
        if (courseSelect) {
            courseSelect.querySelectorAll('option:not([value=""])').forEach(opt => opt.remove());
            courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course;
                option.textContent = course;
                courseSelect.appendChild(option);
            });
        }
        // Cadre filter
        const cadreSelect = document.getElementById('cadre-filter');
        if (cadreSelect) {
            cadreSelect.querySelectorAll('option:not([value=""])').forEach(opt => opt.remove());
            cadres.forEach(cadre => {
                const option = document.createElement('option');
                option.value = cadre;
                option.textContent = cadre;
                cadreSelect.appendChild(option);
            });
        }

        // ATT filter
        const attSelect = document.getElementById('att-filter');
        if (attSelect) {
            attSelect.querySelectorAll('option:not([value=""])').forEach(opt => opt.remove());
            atts.forEach(att => {
                const option = document.createElement('option');
                option.value = att;
                option.textContent = att;
                attSelect.appendChild(option);
            });
        }
        // Education filter
        const educationSelect = document.getElementById('education-filter');
        if (educationSelect) {
            educationSelect.querySelectorAll('option:not([value=""])').forEach(opt => opt.remove());
            educations.forEach(education => {
                const option = document.createElement('option');
                option.value = education;
                option.textContent = education;
                educationSelect.appendChild(option);
            });
        }
    }

    filterAndRender() {
        let filtered = this.soldiers;

        console.log('Initial soldiers count:', filtered.length);
        console.log('Current filters:', this.filters);

        if (this.filters.search) {
            const term = this.filters.search.toLowerCase();
            filtered = filtered.filter(s =>
                s.name?.toLowerCase().includes(term) ||
                (s.army_no && s.army_no.toLowerCase().includes(term))
            );
            console.log('After search filter:', filtered.length);
        }

        if (this.filters.rank) {
            filtered = filtered.filter(s => s.rank === this.filters.rank);
            console.log('After rank filter:', filtered.length);
        }

        if (this.filters.company) {
            filtered = filtered.filter(s => s.unit === this.filters.company);
            console.log('After company filter:', filtered.length);
        }

        if (this.filters.status) {
            filtered = filtered.filter(s => {
                const status = s.is_leave ? 'leave' : (s.is_sick ? 'medical' : 'active');
                return status === this.filters.status;
            });
            console.log('After status filter:', filtered.length);
        }

        if (this.filters.skill) {
            filtered = filtered.filter(soldier =>
                soldier.cocurricular?.some(skill => skill.name === this.filters.skill)
            );
            console.log('After skill filter:', filtered.length);
        }

        if (this.filters.course) {
            const filterValue = this.filters.course.toLowerCase().trim();
            filtered = filtered.filter(soldier =>
                soldier.courses?.some(course =>
                    course.name.toLowerCase().trim() === filterValue
                )
            );
            console.log('After course filter:', filtered.length);
        }

        if (this.filters.cadre) {
            const filterValue = this.filters.cadre.toLowerCase().trim();
            filtered = filtered.filter(soldier =>
                soldier.cadres?.some(cadre =>
                    cadre.name.toLowerCase().trim() === filterValue
                )
            );
            console.log('After cadre filter:', filtered.length);
        }

        // ERE filter logic with better debugging
        if (this.filters.ere && this.filters.ere !== "all") {
            console.log('Applying ERE filter:', this.filters.ere);

            const beforeEreFilter = filtered.length;

            filtered = filtered.filter(soldier => {
                const hasEre = soldier.has_ere === true;
                const shouldInclude = this.filters.ere === "with-ere" ? hasEre : !hasEre;

                console.log(`Soldier ${soldier.id}: has_ere=${soldier.has_ere}, shouldInclude=${shouldInclude}`);

                return shouldInclude;
            });

            console.log('After ERE filter:', filtered.length, '(removed', beforeEreFilter - filtered.length, 'soldiers)');
        }

        const emptyState = document.getElementById('empty-state');
        if (filtered.length === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
        // ATT filter logic
        if (this.filters.att) {
            const filterValue = this.filters.att.toLowerCase().trim();
            filtered = filtered.filter(soldier =>
                soldier.att?.some(att =>
                    att.name.toLowerCase().trim() === filterValue
                )
            );
            console.log('After ATT filter:', filtered.length);
        }
        // Education filter logic
        if (this.filters.education) {
            const filterValue = this.filters.education.toLowerCase().trim();
            filtered = filtered.filter(soldier =>
                soldier.educations?.some(education =>
                    education.name.toLowerCase().trim() === filterValue
                )
            );
            console.log('After Education filter:', filtered.length);
        }

        // Leave filter logic
        if (this.filters.leave) {
            console.log('Applying Leave filter:', this.filters.leave);

            const beforeLeaveFilter = filtered.length;

            filtered = filtered.filter(soldier => {
                const isOnLeave = soldier.is_leave === true;
                const shouldInclude = this.filters.leave === "on-leave" ? isOnLeave : !isOnLeave;

                console.log(`Soldier ${soldier.id}: is_leave=${soldier.is_leave}, shouldInclude=${shouldInclude}`);

                return shouldInclude;
            });

            console.log('After Leave filter:', filtered.length, '(removed', beforeLeaveFilter - filtered.length, 'soldiers)');
        }

        console.log('Final filtered count:', filtered.length);
        this.renderData(filtered);
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
            ere: "all", // Reset ERE filter to default value
            att: "",
            education: "",
            leave: ""   // Reset Leave filter

        };

        const searchInput = document.getElementById('search-input');
        const rankSelect = document.getElementById('rank-filter');
        const companySelect = document.getElementById('company-filter');
        const statusSelect = document.getElementById('status-filter');
        const skillSelect = document.getElementById('skill-filter');
        const courseSelect = document.getElementById('course-filter');
        const cadreSelect = document.getElementById('cadre-filter');
        const ereSelect = document.getElementById('ere-filter');
        const attSelect = document.getElementById('att-filter');
        const educationSelect = document.getElementById('education-filter');

        if (searchInput) searchInput.value = '';
        if (rankSelect) rankSelect.value = '';
        if (companySelect) companySelect.value = '';
        if (statusSelect) statusSelect.value = '';
        if (skillSelect) skillSelect.value = '';
        if (courseSelect) courseSelect.value = '';
        if (cadreSelect) cadreSelect.value = '';
        if (ereSelect) ereSelect.value = 'all'; // Reset ERE filter to default
        if (attSelect) attSelect.value = '';
        if (educationSelect) educationSelect.value = '';
        const leaveSelect = document.getElementById('leave-filter');
        if (leaveSelect) leaveSelect.value = '';
        console.log('All filters cleared');
        this.filterAndRender();
    }
    renderData(soldiers) {
        const tbody = document.getElementById("soldiers-tbody");
        if (!tbody) return;

        tbody.innerHTML = soldiers.map((s) => renderTableRow(s)).join("");


        tbody.querySelectorAll(".btn-leave").forEach((btn) => {
            btn.addEventListener("click", () => {
                const id = btn.dataset.id;
                const soldier = soldiers.find((s) => s.id == id);
                if (soldier) openProfileModal(soldier, "Leave details");
            });
        });

        // NEW: History buttons event listeners
        tbody.querySelectorAll(".btn-duty-history").forEach((btn) => {
            btn.addEventListener("click", () => {
                const id = btn.dataset.id;
                const soldier = soldiers.find((s) => s.id == id);
                if (soldier) this.showHistoryModal(soldier, 'duty');
            });
        });

        tbody.querySelectorAll(".btn-leave-history").forEach((btn) => {
            btn.addEventListener("click", () => {
                const id = btn.dataset.id;
                const soldier = soldiers.find((s) => s.id == id);
                if (soldier) this.showHistoryModal(soldier, 'leave');
            });
        });

        tbody.querySelectorAll(".btn-appointment-history").forEach((btn) => {
            btn.addEventListener("click", () => {
                const id = btn.dataset.id;
                const soldier = soldiers.find((s) => s.id == id);
                if (soldier) this.showHistoryModal(soldier, 'appointment');
            });
        });

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
    showHistoryModal(soldier, type) {
        const title = document.getElementById('modal-title');
        const modal = document.getElementById('profile-modal');
        const content = document.getElementById('modal-content');

        if (!modal || !content) {
            console.warn("Profile modal elements not found in DOM");
            return;
        }

        const typeTitles = {
            'duty': 'Duty History',
            'leave': 'Leave History',
            'appointment': 'Appointment History'
        };

        title.innerHTML = `${typeTitles[type]} - ${soldier.name}`;
        content.innerHTML = this.generateHistoryContent(soldier, type);
        modal.classList.remove('hidden');
    }

    generateHistoryContent(soldier, type) {
        const historyData = soldier[`${type}_history`] || [];

        if (!historyData || historyData.length === 0) {
            return `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-${this.getHistoryIcon(type)} text-3xl mb-3 text-gray-300"></i>
                <p class="text-lg">No ${type} history found</p>
            </div>
        `;
        }

        return `
        <div class="space-y-4 max-h-96 overflow-y-auto">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">${this.getHistoryTitle(type)} (${historyData.length})</h4>
            ${historyData.map(item => this.renderHistoryItem(type, item)).join('')}
        </div>
    `;
    }

    getHistoryIcon(type) {
        const icons = {
            'duty': 'tasks',
            'leave': 'umbrella-beach',
            'appointment': 'briefcase'
        };
        return icons[type] || 'history';
    }

    getHistoryTitle(type) {
        const titles = {
            'duty': 'Duty History',
            'leave': 'Leave History',
            'appointment': 'Appointment History'
        };
        return titles[type] || 'History';
    }

    renderHistoryItem(type, item) {
        switch (type) {
            case 'duty':
                return this.renderDutyItem(item);
            case 'leave':
                return this.renderLeaveItem(item);
            case 'appointment':
                return this.renderAppointmentItem(item);
            default:
                return `<div class="border border-gray-200 rounded-lg p-4">Unknown history type</div>`;
        }
    }

    renderDutyItem(duty) {
        return `
        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
            <div class="flex justify-between items-start mb-2">
                <h5 class="font-medium text-gray-900">${duty.name || 'Unnamed Duty'}</h5>
                <span class="px-2 py-1 text-xs rounded-full ${duty.is_active ? 'bg-green-100 text-green-800' :
                duty.status === 'completed' ? 'bg-gray-100 text-gray-800' :
                    'bg-blue-100 text-blue-800'
            }">
                    ${duty.is_active ? 'Active' : (duty.status || 'Unknown')}
                </span>
            </div>
            ${duty.type ? `<p class="text-sm text-gray-600 mb-1"><strong>Type:</strong> ${duty.type}</p>` : ''}
            ${duty.start_date ? `<p class="text-sm text-gray-600 mb-1"><strong>Start:</strong> ${formatDate(duty.start_date)}</p>` : ''}
            ${duty.end_date ? `<p class="text-sm text-gray-600 mb-1"><strong>End:</strong> ${formatDate(duty.end_date)}</p>` : ''}
            ${duty.duration_days ? `<p class="text-sm text-gray-600 mb-1"><strong>Duration:</strong> ${duty.duration_days} days</p>` : ''}
            ${duty.remarks ? `<p class="text-sm text-gray-600"><strong>Remarks:</strong> ${duty.remarks}</p>` : ''}
        </div>
    `;
    }

    renderLeaveItem(leave) {
        return `
        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
            <div class="flex justify-between items-start mb-2">
                <h5 class="font-medium text-gray-900">${leave.leave_type || 'Unknown Leave Type'}</h5>
                <span class="px-2 py-1 text-xs rounded-full ${leave.status === 'approved' ? 'bg-green-100 text-green-800' :
                leave.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                    leave.status === 'rejected' ? 'bg-red-100 text-red-800' :
                        'bg-gray-100 text-gray-800'
            }">
                    ${leave.status || 'Unknown'}
                </span>
            </div>
            ${leave.reason ? `<p class="text-sm text-gray-600 mb-1"><strong>Reason:</strong> ${leave.reason}</p>` : ''}
            ${leave.start_date ? `<p class="text-sm text-gray-600 mb-1"><strong>From:</strong> ${formatDate(leave.start_date)}</p>` : ''}
            ${leave.end_date ? `<p class="text-sm text-gray-600 mb-1"><strong>To:</strong> ${formatDate(leave.end_date)}</p>` : ''}
            ${leave.duration_days ? `<p class="text-sm text-gray-600 mb-1"><strong>Duration:</strong> ${leave.duration_days} days</p>` : ''}
            ${leave.application_date ? `<p class="text-sm text-gray-600"><strong>Applied:</strong> ${formatDate(leave.application_date)}</p>` : ''}
        </div>
    `;
    }

    renderAppointmentItem(appointment) {
        return `
        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
            <div class="flex justify-between items-start mb-2">
                <h5 class="font-medium text-gray-900">${appointment.appointment_name || 'Unknown Appointment'}</h5>
                <span class="px-2 py-1 text-xs rounded-full ${appointment.is_current ? 'bg-green-100 text-green-800' :
                appointment.is_active ? 'bg-blue-100 text-blue-800' :
                    appointment.is_completed ? 'bg-gray-100 text-gray-800' :
                        'bg-yellow-100 text-yellow-800'
            }">
                    ${appointment.is_current ? 'Current' :
                appointment.is_active ? 'Active' :
                    appointment.is_completed ? 'Completed' :
                        appointment.status || 'Unknown'}
                </span>
            </div>
            ${appointment.appointment_type ? `<p class="text-sm text-gray-600 mb-1"><strong>Type:</strong> ${appointment.appointment_type}</p>` : ''}
            ${appointment.from_date ? `<p class="text-sm text-gray-600 mb-1"><strong>From:</strong> ${formatDate(appointment.from_date)}</p>` : ''}
            ${appointment.to_date ? `<p class="text-sm text-gray-600 mb-1"><strong>To:</strong> ${formatDate(appointment.to_date)}</p>` : ''}
            ${appointment.duration_days ? `<p class="text-sm text-gray-600 mb-1"><strong>Duration:</strong> ${appointment.duration_days} days</p>` : ''}
            ${appointment.note ? `<p class="text-sm text-gray-600"><strong>Note:</strong> ${appointment.note}</p>` : ''}
        </div>
    `;
    }
}
