// public/js/soldiers/soldierHelpers.js
export function formatDate(dateString) {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

export function getStatusFromSoldier(soldier) {
    if (soldier.is_leave === true) return 'leave';
    if (soldier.is_sick === true) return 'medical';
    if (soldier.status === true) return 'active';
    return 'inactive';
}
export function getSkill(skillArray) {
    if (!Array.isArray(skillArray) || skillArray.length === 0) {
        return "N/A";
    }

    return skillArray
        .map(skill => {
            const name = skill.name || "Unknown";
            const result = skill.result !== null && skill.result !== undefined
                ? `(${skill.result})`
                : "";
            return `${name} ${result}`.trim();
        })
        .join(", ");
}
export function getEducations(arr) {
    if (!Array.isArray(arr) || arr.length === 0) {
        return "No education data available";
    }

    return arr
        .map(edu => {
            const name = edu.name || "Unknown";
            const status = edu.status ? `(${edu.status})` : "";
            const year = edu.year ? `Year: ${edu.year}` : "";
            const remark = edu.remark ? `Remark: ${edu.remark}` : "";

            // Join only non-empty parts with comma
            return [name, status, year, remark].filter(Boolean).join(", ");
        })
        .join(" | "); // separate multiple educations by " | "
}

export function highlightText(text, term) {
    if (!term) return text; // no search term, return original text
    const regex = new RegExp(`(${term})`, "gi"); // case-insensitive match
    return text.replace(regex, '<span class="bg-yellow-200">$1</span>');
}

export function getCourseAndCadres(courses = [], cadres = []) {
    // Helper function to format array into comma-separated string
    const formatArray = (arr) => {
        if (!Array.isArray(arr) || arr.length === 0) return "N/A";
        return arr
            .map(item => {
                const name = item.name || "Unknown";
                const result = item.result ? ` (${item.result})` : "";
                return `${name}${result}`;
            })
            .join(", ");
    };

    const coursesText = formatArray(courses);
    const cadresText = formatArray(cadres);

    return `<p><strong>Courses:</strong> ${coursesText}</p>
            <p><strong>Cadres:</strong> ${cadresText}</p>`;
}


export function calculateProgress(soldier) {
    const completedSteps = [
        soldier.personal_completed,
        soldier.service_completed,
        soldier.qualifications_completed,
        soldier.medical_completed
    ].filter(Boolean).length;

    const percentage = Math.round((completedSteps / 4) * 100);
    let color = 'red';

    if (percentage >= 80) color = 'green';
    else if (percentage >= 50) color = 'yellow';

    return { percentage, color };
}

export function getLeaveBadge(status) {
    const badges = {
        active: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check"></i></span>',
        inactive: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">On Leave</span>'
    };

    return status ? badges.inactive : badges.active;
}
export function getStatusBadge(status) {
    const badges = {
        active: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>',
        leave: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">On Leave</span>',
        medical: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Medical</span>',
        inactive: '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>'
    };
    return badges[status] || badges.inactive;
}
export function showToast(message, type) {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    const icon = type === 'success' ? 'check' : 'exclamation-triangle';

    toast.className =
        `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
    toast.innerHTML = `
    <div class="flex items-center">
        <i class="fas fa-${icon} mr-2"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
`;

    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

