// public/js/soldiers/soldierHistoryModal.js
import { formatDate, showToast } from "./soldierHelpers.js";

export function openHistoryModal(soldierId, type) {
    const title = document.getElementById('history-modal-title');
    const modal = document.getElementById('history-modal');
    const content = document.getElementById('history-modal-content');

    if (!modal || !content) {
        console.warn("History modal elements not found in DOM");
        return;
    }

    // Show loading state
    content.innerHTML = `
        <div class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
            <span class="ml-2 text-gray-600">Loading history...</span>
        </div>
    `;

    const typeTitles = {
        'duty': 'Duty History',
        'leave': 'Leave History',
        'appointment': 'Appointment History',
        'att': 'ATT History',
        'cmd': 'CMD History'
    };

    title.innerHTML = typeTitles[type] || 'History';
    modal.classList.remove('hidden');

    // Fetch history data from API
    fetchHistoryData(soldierId, type)
        .then(data => {
            displayHistoryContent(soldierId, type, data);
        })
        .catch(error => {
            console.error('Error fetching history:', error);
            content.innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                    <p>Failed to load history data</p>
                    <button onclick="window.soldierManager.retryLoadHistory(${soldierId}, '${type}')"
                            class="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Retry
                    </button>
                </div>
            `;
        });
}

export function closeHistoryModal() {
    const modal = document.getElementById('history-modal');
    if (!modal) return;
    modal.classList.add('hidden');
}

async function fetchHistoryData(soldierId, type) {
    const response = await fetch(routes.getHistory.replace(':id', soldierId) + `?type=${type}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });

    if (!response.ok) {
        throw new Error('Network response was not ok');
    }

    return await response.json();
}

async function fetchAttTypes() {
    const response = await fetch(routes.getAttTypes, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });

    if (!response.ok) {
        throw new Error('Failed to fetch ATT types');
    }

    return await response.json();
}

async function fetchCmdTypes() {
    const response = await fetch(routes.getCmdTypes, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });

    if (!response.ok) {
        throw new Error('Failed to fetch CMD types');
    }

    return await response.json();
}

function displayHistoryContent(soldierId, type, responseData) {
    const content = document.getElementById('history-modal-content');
    const data = responseData.data || [];
    const soldierName = responseData.soldier_name || '';
    const soldierArmyNo = responseData.soldier_army_no || '';
    const soldierRank = responseData.soldier_rank || '';

    if (type === 'att' || type === 'cmd') {
        displayHistoryWithForm(soldierId, type, data, soldierName, soldierArmyNo, soldierRank);
    } else {
        displayRegularHistory(type, data, soldierName, soldierArmyNo, soldierRank);
    }
}

function displayHistoryWithForm(soldierId, type, data, soldierName, soldierArmyNo, soldierRank) {
    const content = document.getElementById('history-modal-content');
    const typeConfig = {
        'att': {
            title: 'ATT History',
            icon: 'clipboard-check',
            color: 'orange',
            formTitle: 'Add New ATT Record',
            buttonText: 'Add ATT Record'
        },
        'cmd': {
            title: 'CMD History',
            icon: 'code-branch',
            color: 'teal',
            formTitle: 'Add New CMD Record',
            buttonText: 'Add CMD Record'
        }
    };

    const config = typeConfig[type];

    content.innerHTML = `
        <div class="space-y-6">
            <!-- Soldier Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800">${config.title} (${data.length})</h4>
                        ${soldierName ? `
                            <div class="text-sm text-gray-600 mt-1">
                                <span class="font-medium">${soldierName}</span>
                                ${soldierRank ? `<span class="mx-2">•</span>${soldierRank}` : ''}
                                ${soldierArmyNo ? `<span class="mx-2">•</span>${soldierArmyNo}` : ''}
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>

            <!-- Add Form -->
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <h5 class="font-medium text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-${config.icon} text-${config.color}-600 mr-2"></i>
                    ${config.formTitle}
                </h5>
                <form id="add-${type}-form" class="space-y-3">
                    <input type="hidden" name="soldier_id" value="${soldierId}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">${type.toUpperCase()} Type *</label>
                            <select name="${type}_id" required
                                class="${type}-type-select w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-${config.color}-500 focus:border-${config.color}-500">
                                <option value="">Select ${type.toUpperCase()} Type</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                            <input type="date" name="start_date" required
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-${config.color}-500 focus:border-${config.color}-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-${config.color}-500 focus:border-${config.color}-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                            <input type="text" name="remarks" placeholder="Optional remarks"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-${config.color}-500 focus:border-${config.color}-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" onclick="clearForm('${type}')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Clear
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-${config.color}-600 border border-transparent rounded-md hover:bg-${config.color}-700 focus:outline-none focus:ring-2 focus:ring-${config.color}-500 flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            ${config.buttonText}
                        </button>
                    </div>
                </form>
            </div>

            <!-- History List -->
            <div class="space-y-3">
                <h5 class="font-medium text-gray-900 flex items-center">
                    <i class="fas fa-history text-${config.color}-600 mr-2"></i>
                    ${type.toUpperCase()} Records
                </h5>

                ${data.length === 0 ? `
                    <div class="text-center py-8 text-gray-500 border-2 border-dashed border-gray-300 rounded-lg">
                        <i class="fas fa-${config.icon} text-4xl mb-3 text-gray-300"></i>
                        <p class="text-lg">No ${type.toUpperCase()} records found</p>
                        <p class="text-sm mt-1">Add a new ${type.toUpperCase()} record using the form above</p>
                    </div>
                ` : data.map(item => renderHistoryItem(type, item)).join('')}
            </div>
        </div>
    `;

    // Load types and setup form
    loadTypes(type);
    setupForm(soldierId, type);
}

function displayRegularHistory(type, data, soldierName, soldierArmyNo, soldierRank) {
    const content = document.getElementById('history-modal-content');

    content.innerHTML = `
        <div class="space-y-4">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h4 class="text-lg font-semibold text-gray-800">${getHistoryTitle(type)} (${data.length})</h4>
                    ${soldierName ? `
                        <div class="text-sm text-gray-600 mt-1">
                            <span class="font-medium">${soldierName}</span>
                            ${soldierRank ? `<span class="mx-2">•</span>${soldierRank}` : ''}
                            ${soldierArmyNo ? `<span class="mx-2">•</span>${soldierArmyNo}` : ''}
                        </div>
                    ` : ''}
                </div>
            </div>
            ${data.length === 0 ? `
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-${getHistoryIcon(type)} text-3xl mb-3 text-gray-300"></i>
                    <p class="text-lg">No ${type} history found</p>
                </div>
            ` : data.map(item => renderHistoryItem(type, item)).join('')}
        </div>
    `;
}

async function loadTypes(type) {
    try {
        const select = document.querySelector(`.${type}-type-select`);
        let response;

        if (type === 'att') {
            response = await fetchAttTypes();
        } else if (type === 'cmd') {
            response = await fetchCmdTypes();
        } else {
            return;
        }

        if (response.error) {
            throw new Error(response.error);
        }

        const firstOption = select.querySelector('option[value=""]');
        select.innerHTML = '';
        select.appendChild(firstOption);

        response.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name;
            select.appendChild(option);
        });

    } catch (error) {
        console.error(`Error loading ${type.toUpperCase()} types:`, error);
        showToast(`Failed to load ${type.toUpperCase()} types`, 'error');
    }
}

function setupForm(soldierId, type) {
    const form = document.getElementById(`add-${type}-form`);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const data = {
            [`${type}_id`]: formData.get(`${type}_id`),
            start_date: formData.get('start_date'),
            end_date: formData.get('end_date') || null,
            remarks: formData.get('remarks') || null
        };

        // Validation
        if (!data[`${type}_id`] || !data.start_date) {
            showToast('Please fill in all required fields', 'error');
            return;
        }

        if (data.end_date && data.end_date < data.start_date) {
            showToast('End date cannot be before start date', 'error');
            return;
        }

        if (type === 'att') {
            await submitAttRecord(soldierId, data);
        } else if (type === 'cmd') {
            await submitCmdRecord(soldierId, data);
        }
    });
}

async function submitAttRecord(soldierId, data) {
    const submitBtn = document.querySelector('#add-att-form button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    try {
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
        submitBtn.disabled = true;

        const response = await fetch(routes.addAttRecord.replace(':id', soldierId), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || result.errors || 'Failed to add ATT record');
        }

        showToast('ATT record added successfully', 'success');
        clearForm('att');

        // Reload the history to show the new record
        const historyResponse = await fetchHistoryData(soldierId, 'att');
        displayHistoryWithForm(soldierId, 'att', historyResponse.data, historyResponse.soldier_name, historyResponse.soldier_army_no, historyResponse.soldier_rank);

    } catch (error) {
        console.error('Error adding ATT record:', error);
        showToast(error.message, 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

async function submitCmdRecord(soldierId, data) {
    const submitBtn = document.querySelector('#add-cmd-form button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    try {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
        submitBtn.disabled = true;

        const response = await fetch(routes.addCmdRecord.replace(':id', soldierId), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || result.errors || 'Failed to add CMD record');
        }

        showToast('CMD record added successfully', 'success');
        clearForm('cmd');

        // Reload the history
        const historyResponse = await fetchHistoryData(soldierId, 'cmd');
        displayHistoryWithForm(soldierId, 'cmd', historyResponse.data, historyResponse.soldier_name, historyResponse.soldier_army_no, historyResponse.soldier_rank);

    } catch (error) {
        console.error('Error adding CMD record:', error);
        showToast(error.message, 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

function clearForm(type) {
    const form = document.getElementById(`add-${type}-form`);
    if (form) {
        form.reset();
    }
}

// Global functions for form actions
window.clearForm = clearForm;

function getHistoryIcon(type) {
    const icons = {
        'duty': 'tasks',
        'leave': 'umbrella-beach',
        'appointment': 'briefcase',
        'att': 'clipboard-check',
        'cmd': 'code-branch'
    };
    return icons[type] || 'history';
}

function getHistoryTitle(type) {
    const titles = {
        'duty': 'Duty History',
        'leave': 'Leave History',
        'appointment': 'Appointment History',
        'att': 'ATT History',
        'cmd': 'CMD (Command) History'
    };
    return titles[type] || 'History';
}

function renderHistoryItem(type, item) {
    switch (type) {
        case 'duty':
            return renderDutyItem(item);
        case 'leave':
            return renderLeaveItem(item);
        case 'appointment':
            return renderAppointmentItem(item);
        case 'att':
            return renderAttItem(item);
        case 'cmd':
            return renderCmdItem(item);
        default:
            return `<div class="border border-gray-200 rounded-lg p-4">Unknown history type</div>`;
    }
}

function renderAttItem(att) {
    return `
        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
            <div class="flex justify-between items-start mb-2">
                <h5 class="font-medium text-gray-900">${att.name || 'ATT Record'}</h5>
                <div class="flex space-x-1">
                    <span class="px-2 py-1 text-xs rounded-full ${att.is_active ? 'bg-green-100 text-green-800' :
            att.status === 'completed' ? 'bg-blue-100 text-blue-800' :
                'bg-yellow-100 text-yellow-800'
        }">
                        ${att.is_active ? 'Active' : (att.status || 'Unknown')}
                    </span>
                    ${att.att_status !== undefined ? `
                        <span class="px-2 py-1 text-xs rounded-full ${att.att_status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${att.status_badge || (att.att_status ? 'Active' : 'Inactive')}
                        </span>
                    ` : ''}
                </div>
            </div>
            ${att.type ? `<p class="text-sm text-gray-600 mb-1"><strong>Type:</strong> ${att.type}</p>` : ''}
            ${att.start_date ? `<p class="text-sm text-gray-600 mb-1"><strong>Start:</strong> ${formatDate(att.start_date)}</p>` : ''}
            ${att.end_date ? `<p class="text-sm text-gray-600 mb-1"><strong>End:</strong> ${formatDate(att.end_date)}</p>` : ''}
            ${att.duration_days ? `<p class="text-sm text-gray-600 mb-1"><strong>Duration:</strong> ${att.duration_days} days</p>` : ''}
            ${att.test_period ? `<p class="text-sm text-gray-600 mb-1"><strong>Test Period:</strong> ${att.test_period}</p>` : ''}
            ${att.remarks ? `<p class="text-sm text-gray-600"><strong>Remarks:</strong> ${att.remarks}</p>` : ''}
        </div>
    `;
}

function renderCmdItem(cmd) {
    return `
        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
            <div class="flex justify-between items-start mb-2">
                <h5 class="font-medium text-gray-900">${cmd.name || 'CMD Record'}</h5>
                <div class="flex space-x-1">
                    <span class="px-2 py-1 text-xs rounded-full ${cmd.is_active ? 'bg-green-100 text-green-800' :
            cmd.status === 'completed' ? 'bg-blue-100 text-blue-800' :
                'bg-yellow-100 text-yellow-800'
        }">
                        ${cmd.is_active ? 'Active' : (cmd.status || 'Unknown')}
                    </span>
                    ${cmd.cmd_status !== undefined ? `
                        <span class="px-2 py-1 text-xs rounded-full ${cmd.cmd_status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${cmd.status_badge || (cmd.cmd_status ? 'Active' : 'Inactive')}
                        </span>
                    ` : ''}
                </div>
            </div>
            ${cmd.type ? `<p class="text-sm text-gray-600 mb-1"><strong>Type:</strong> ${cmd.type}</p>` : ''}
            ${cmd.start_date ? `<p class="text-sm text-gray-600 mb-1"><strong>Start:</strong> ${formatDate(cmd.start_date)}</p>` : ''}
            ${cmd.end_date ? `<p class="text-sm text-gray-600 mb-1"><strong>End:</strong> ${formatDate(cmd.end_date)}</p>` : ''}
            ${cmd.duration_days ? `<p class="text-sm text-gray-600 mb-1"><strong>Duration:</strong> ${cmd.duration_days} days</p>` : ''}
            ${cmd.command_period ? `<p class="text-sm text-gray-600 mb-1"><strong>Command Period:</strong> ${cmd.command_period}</p>` : ''}
            ${cmd.remarks ? `<p class="text-sm text-gray-600"><strong>Remarks:</strong> ${cmd.remarks}</p>` : ''}
        </div>
    `;
}

function renderDutyItem(duty) {
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

function renderLeaveItem(leave) {
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

function renderAppointmentItem(appointment) {
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
