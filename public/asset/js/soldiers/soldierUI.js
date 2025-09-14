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
    // console.log(soldier.is_leave);
    const defaultAvatar = "/images/default-avatar.png";


    return `
        <tr class="hover:bg-gray-50 transition-colors duration-150" data-soldier-id="${soldier.id}">
            <td class="px-6 py-4">
                <input type="checkbox" class="row-select rounded border-gray-300 text-green-600 focus:ring-green-500"
                       value="${soldier.id}">
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center relative">
                    <div class="absolute -top-5">${isleave}</div>
                   <div class="flex flex-col items-center">
                    <!-- Image -->
                    <img class="h-40 w-32 rounded-lg object-cover border-2 border-gray-200"
                        src="${soldier.image ? `/storage/${soldier.image}` : defaultAvatar}"
                        alt="${soldier.name || 'Soldier'}"
                        onerror="this.src='/images/default-avatar.png'">

                    <!-- Progress bar beneath the image -->
                    <div class="flex items-center mt-2 w-full justify-center">
                        <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                            <div class="bg-${progress.color}-600 h-2 rounded-full" style="width: ${progress.percentage}%"></div>
                        </div>
                        <span class="text-xs text-gray-600">${progress.percentage}%</span>
                    </div>
                </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${soldier.name || 'N/A'}</div>
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



             </td>

            <td class="px-6 py-4">
                    <p> ${courseAndCadres}</p>
                    <p> <strong>Academic:</strong> ${educations}</p>
                    <p> <strong>Skill: </strong>${skills}</p>


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
