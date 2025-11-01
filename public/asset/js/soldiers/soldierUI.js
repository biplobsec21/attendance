// public/js/soldiers/soldierUI.js
import { getStatusFromSoldier, calculateProgress, getStatusBadge, formatDate, getLeaveBadge, getSkill, getEducations, getCourseAndCadres, highlightText } from "./soldierHelpers.js";

export function renderTableRow(soldier) {
    const status = getStatusFromSoldier(soldier);
    const statusBadge = getStatusBadge(status);
    const progress = calculateProgress(soldier);
    const isleave = getLeaveBadge(soldier.is_leave);
    const skills = getSkill(soldier.cocurricular);
    const educations = getEducations(soldier.educations);
    const courseAndCadres = getCourseAndCadres(soldier.courses, soldier.cadres);
    const defaultAvatar = routes.defaultAvatar;

    // ERE status badge
    const ereBadge = soldier.has_ere ?
        '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">ERE</span>' :
        '';

    return `
        <tr class="hover:bg-gray-50 transition-colors duration-150" data-soldier-id="${soldier.id}">
            <td class="px-6 py-4">
                <input type="checkbox" class="row-select rounded border-gray-300 text-green-600 focus:ring-green-500"
                       value="${soldier.id}">
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center relative">
                    <div class="absolute -top-5">
                        <button class="btn-leave" data-id="${soldier.id}">${isleave}</button>
                    </div>
                   <div class="flex flex-col items-center">
                    <!-- Image -->
                    <img class="h-40 w-32 rounded-lg object-cover border-2 border-gray-200"
                        src="${soldier.image}"
                        alt="${soldier.name || 'Soldier'}"/>

                    <!-- Progress bar beneath the image -->
                    <div class="flex items-center mt-2 w-full justify-center">
                        <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-${progress.color}-600 h-2 rounded-full" style="width: ${progress.percentage}%"></div>
                        </div>
                        <span class="text-xs text-gray-600">${progress.percentage}%</span>
                    </div>
                </div>
                    <div class="ml-4">
                        <div class="flex items-center">
                            <div class="text-sm font-medium text-gray-900">${soldier.name || 'N/A'}</div>
                            ${ereBadge}
                        </div>
                        <div class="text-sm text-gray-500">Army #${soldier.army_no || 'N/A'}</div>

                        <ul class="mt-4 space-y-3 text-gray-700">

                            <p>${soldier.rank || 'N/A'} </p>
                            <li class="flex items-center"><i class="fas fa-building fa-fw w-6 text-gray-400"></i>

                                <span>${soldier.unit ?? 'N/A'}</span>
                            </li>
                            <li class="flex items-center"><i class="fas fa-mobile-alt fa-fw w-6 text-gray-400"></i>
                                <span>${soldier.mobile ?? 'N/A'}</span>
                            </li>


                        </ul>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
             <li class="flex items-center"><i class="fas fa-star fa-fw w-6 text-gray-400"></i>
                    <span class="px-2 py-1 rounded-md bg-red-50 text-green-800 text-sm">
                        ${soldier.marital_status ?? 'N/A'}
                    </span>
                </li>
                <li class="flex items-center"><i class="fas fa-tint fa-fw w-6 text-gray-400"></i>
                    <span class="px-2 py-1 rounded-md bg-red-50 text-red-600 text-sm">
                        ${soldier.blood_group ?? 'N/A'}
                    </span>
                </li>

                <li class="flex items-center"><i class="fas fa-stopwatch fa-fw w-6 text-gray-400"></i>
                    <span class="px-2 py-1 rounded-md bg-blue-50 text-blue-600 text-sm">
                        ${soldier.service_duration ?? 'N/A'}
                    </span>
                </li>
                <li class="flex items-center">
                        ${soldier.address ?? 'N/A'}
                </li>

            <!-- New ATT History Button -->
                <button class="btn-att-history px-2 py-1 bg-gradient-to-r from-orange-400 to-orange-500 text-white text-xs rounded hover:from-orange-500 hover:to-orange-600 transition-all duration-200 shadow-sm hover:shadow-md"
                        data-id="${soldier.id}" title="ATT History">
                    <i class="fas fa-clipboard-check mr-1"></i>ATT
                </button>
                 <button class="btn-cmd-history px-2 py-1 bg-gradient-to-r from-teal-400 to-teal-500 text-white text-xs rounded hover:from-teal-500 hover:to-teal-600 transition-all duration-200 shadow-sm hover:shadow-md"
                        data-id="${soldier.id}" title="CMD History">
                    <i class="fas fa-code-branch mr-1"></i>CMD
                </button>

             </td>

            <td class="px-6 py-4">
                    <p> ${courseAndCadres}</p>
                    <p> <strong>Academic:</strong> ${educations}</p>
                    <p> <strong>Skill: </strong>${skills}</p>

                 <!-- History Buttons Section -->
<div class="mt-3 flex flex-wrap gap-1">
    <button class="btn-duty-history px-2 py-1 bg-gradient-to-r from-blue-400 to-blue-500 text-white text-xs rounded hover:from-blue-500 hover:to-blue-600 transition-all duration-200 shadow-sm hover:shadow-md"
            data-id="${soldier.id}" title="Duty History">
        <i class="fas fa-tasks mr-1"></i>Duty
    </button>
    <button class="btn-leave-history px-2 py-1 bg-gradient-to-r from-amber-400 to-amber-500 text-white text-xs rounded hover:from-amber-500 hover:to-amber-600 transition-all duration-200 shadow-sm hover:shadow-md"
            data-id="${soldier.id}" title="Leave History">
        <i class="fas fa-umbrella-beach mr-1"></i>Leave
    </button>
    <button class="btn-appointment-history px-2 py-1 bg-gradient-to-r from-violet-400 to-violet-500 text-white text-xs rounded hover:from-violet-500 hover:to-violet-600 transition-all duration-200 shadow-sm hover:shadow-md"
            data-id="${soldier.id}" title="Appointment History">
        <i class="fas fa-briefcase mr-1"></i>Appointment
    </button>
</div>

                </td>


            <td class="px-6 py-4 text-sm font-medium flex flex-col items-center space-y-2">
                    <!-- View Button -->
                    <button class="view-btn bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-800 w-10 h-10 flex items-center justify-center rounded-full shadow-md"
                            data-id="${soldier.id}" title="View">
                        <i class="fas fa-eye"></i>
                    </button>

                    <!-- Edit Button -->
                    <button class="edit-btn bg-green-100 text-green-600 hover:bg-green-200 hover:text-green-800 w-10 h-10 flex items-center justify-center rounded-full shadow-md"
                            data-id="${soldier.id}" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>

                    <!-- Delete Button -->
                    <button class="delete-btn bg-red-100 text-red-600 hover:bg-red-200 hover:text-red-800 w-10 h-10 flex items-center justify-center rounded-full shadow-md"
                        data-id="${soldier.id}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>

        </tr>
    `;
}
