import { formatDate } from "./soldierHelpers.js";

export function generateProfileModalContent(soldier) {
    const defaultAvatar = "/images/default-avatar.png";

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
                    <h4 class="text-sm font-medium text-gray-500">Army Number</h4>
                    <p class="mt-1">${soldier.army_no || 'N/A'}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Joining Date</h4>
                    <p class="mt-1">${formatDate(soldier.joining_date)}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Blood Group</h4>
                    <p class="mt-1">${soldier.blood_group || 'N/A'}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Phone</h4>
                    <p class="mt-1">${soldier.phone || 'N/A'}</p>
                </div>
            </div>
        </div>
    `;
}

export function openProfileModal(soldier) {
    const modal = document.getElementById('profile-modal');
    const content = document.getElementById('modal-content');

    if (!modal || !content) {
        console.warn("Profile modal elements not found in DOM");
        return;
    }

    content.innerHTML = generateProfileModalContent(soldier);
    modal.classList.remove('hidden');
}

export function closeProfileModal() {
    const modal = document.getElementById('profile-modal');
    if (!modal) return;
    modal.classList.add('hidden');
}
