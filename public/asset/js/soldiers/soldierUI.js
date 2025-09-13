// public/js/soldiers/soldierUI.js
import { getStatusFromSoldier, calculateProgress, getStatusBadge, formatDate, getLeaveBadge } from "./soldierHelpers.js";

export function renderTableRow(soldier) {
    const status = getStatusFromSoldier(soldier);
    const statusBadge = getStatusBadge(status);
    const progress = calculateProgress(soldier);
    const isleave = getLeaveBadge(soldier.is_leave);
    // console.log(soldier.is_leave);
    const defaultAvatar = "/images/default-avatar.png";

    return `
        <tr class="hover:bg-gray-50 transition-colors duration-150" data-soldier-id="${soldier.id}">
            <td class="px-6 py-4">
                <input type="checkbox" class="row-select rounded border-gray-300 text-green-600 focus:ring-green-500"
                       value="${soldier.id}">
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200"
                         src="${soldier.image ? `/storage/${soldier.image}` : defaultAvatar}"
                         alt="${soldier.name || 'Soldier'}"
                         onerror="this.src='/images/default-avatar.png'">
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${soldier.name || 'N/A'}</div>
                        <div class="text-sm text-gray-500">Army #${soldier.army_no || 'N/A'}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
             <p>${soldier.rank || 'N/A'} </p>
             <p><i>${soldier.unit || ''}</i></p>
             </td>

            <td class="px-6 py-4">
            <span class="hidden">${statusBadge}</span>
            ${isleave}
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">${formatDate(soldier.joining_date)}</td>
            <td class="px-6 py-4">
                <div class="flex items-center">
                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                        <div class="bg-${progress.color}-600 h-2 rounded-full" style="width: ${progress.percentage}%"></div>
                    </div>
                    <span class="text-xs text-gray-600">${progress.percentage}%</span>
                </div>
            </td>
            <td class="px-6 py-4 text-sm font-medium space-x-2">
                <button class="view-btn" data-id="${soldier.id}">View</button>
                <button class="edit-btn" data-id="${soldier.id}">Edit</button>
                <button class="delete-btn" data-id="${soldier.id}">Delete</button>
            </td>
        </tr>
    `;
}
