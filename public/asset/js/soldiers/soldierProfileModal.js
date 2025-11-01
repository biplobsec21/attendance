import { formatDate } from "./soldierHelpers.js";

export function generateProfileModalContent(soldier) {
    const defaultAvatar = routes.defaultAvatar;

    return `
        <div class="px-6 py-4">
            <div class="flex items-center space-x-4">
                <img class="h-24 w-24 rounded-full object-cover border-2 border-gray-200"
                     src="${soldier.image ? `/storage/${soldier.image}` : defaultAvatar}"
                     alt="${soldier.name || 'Soldier'}"
                     onerror="this.src='${defaultAvatar}'">
                <div>
                    <h3 class="text-lg font-semibold">${soldier.name || 'N/A'}</h3>
                    <p class="text-sm text-gray-500">${soldier.rank || 'N/A'} • ${soldier.unit || 'N/A'}</p>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Leave Reason</h4>
                    <p class="mt-1">${soldier.current_leave_details ? soldier.current_leave_details.reason : 'N/A'}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Leave Type</h4>
                    <p class="mt-1">${soldier.current_leave_details ? soldier.current_leave_details.leave_type : 'N/A'}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Leave Start Date</h4>
                    <p class="mt-1">${soldier.current_leave_details ? formatDate(soldier.current_leave_details.start_date) : 'N/A'}</p>
                </div>
               <div>
                    <h4 class="text-sm font-medium text-gray-500">Leave End Date</h4>
                    <p class="mt-1">${soldier.current_leave_details ? formatDate(soldier.current_leave_details.end_date) : 'N/A'}</p>
                </div>
            </div>
        </div>
    `;
}

export function openProfileModal(soldier, text) {

    const title = document.getElementById('modal-title');
    const modal = document.getElementById('profile-modal');
    const content = document.getElementById('modal-content');

    if (!modal || !content) {
        console.warn("Profile modal elements not found in DOM");
        return;
    }

    title.innerHTML = text;
    content.innerHTML = generateProfileModalContent(soldier);
    modal.classList.remove('hidden');
}

export function closeProfileModal() {
    const modal = document.getElementById('profile-modal');
    if (!modal) return;
    modal.classList.add('hidden');
}
