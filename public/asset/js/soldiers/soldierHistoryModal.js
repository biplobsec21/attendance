// public/js/soldiers/soldierHistoryModal.js
import { formatDate } from "./soldierHelpers.js";

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
        'appointment': 'Appointment History'
    };

    title.innerHTML = typeTitles[type] || 'History';
    modal.classList.remove('hidden');

    // Fetch history data from API
    fetchHistoryData(soldierId, type)
        .then(data => {
            displayHistoryContent(type, data);
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

function displayHistoryContent(type, responseData) {
    const content = document.getElementById('history-modal-content');
    const data = responseData.data || [];
    const soldierName = responseData.soldier_name || '';
    const soldierArmyNo = responseData.soldier_army_no || '';
    const soldierRank = responseData.soldier_rank || '';

    if (!data || data.length === 0) {
        content.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-${getHistoryIcon(type)} text-3xl mb-3 text-gray-300"></i>
                <p class="text-lg">No ${type} history found</p>
                ${soldierName ? `
                    <div class="mt-2 text-sm text-gray-400">
                        <p>${soldierName}</p>
                        ${soldierRank ? `<p>${soldierRank} • ${soldierArmyNo}</p>` : ''}
                    </div>
                ` : ''}
            </div>
        `;
        return;
    }

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
            ${data.map(item => renderHistoryItem(type, item)).join('')}
        </div>
    `;
}

function getHistoryIcon(type) {
    const icons = {
        'duty': 'tasks',
        'leave': 'umbrella-beach',
        'appointment': 'briefcase'
    };
    return icons[type] || 'history';
}

function getHistoryTitle(type) {
    const titles = {
        'duty': 'Duty History',
        'leave': 'Leave History',
        'appointment': 'Appointment History'
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
        default:
            return `<div class="border border-gray-200 rounded-lg p-4">Unknown history type</div>`;
    }
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



