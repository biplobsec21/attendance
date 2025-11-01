// modals.js
class ModalManager {
    constructor() {
        this.init();
    }

    init() {
        this.bindModalEvents();
        this.bindFormSubmissions();
    }

    bindModalEvents() {
        // Close modals when clicking outside
        window.onclick = (event) => {
            this.handleOutsideClick(event);
        };

        // Bind individual modal close events
        this.bindModalCloseEvents();
    }

    bindModalCloseEvents() {
        const modals = [
            'completeCourseModal', 'completeCadreModal',
            'completeExAreaModal', 'errorModal', 'editAssignmentModal'
        ];

        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.closeModal(modalId);
                    }
                });
            }
        });
    }

    handleOutsideClick(event) {
        const modals = [
            'completeCourseModal', 'completeCadreModal',
            'completeExAreaModal', 'errorModal'
        ];

        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                this.closeModal(modalId);
            }
        });
    }

    bindFormSubmissions() {
        const forms = [
            'completeCourseForm', 'completeCadreForm', 'completeExAreaForm'
        ];

        forms.forEach(formId => {
            const form = document.getElementById(formId);
            if (form) {
                form.addEventListener('submit', (e) => {
                    this.handleFormSubmission(e, formId);
                });
            }
        });
    }

    handleFormSubmission(e, formId) {
        const submitBtn = e.target.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = this.getLoadingText(formId);
        }
    }

    getLoadingText(formId) {
        const loadingTexts = {
            'completeCourseForm': 'Completing...',
            'completeCadreForm': 'Completing...',
            'completeExAreaForm': 'Completing...'
        };
        return loadingTexts[formId] || 'Processing...';
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    // Utility methods for specific modals
    showError(message) {
        const errorMessage = document.getElementById('errorMessage');
        if (errorMessage) {
            errorMessage.textContent = message;
        }
        this.showModal('errorModal');
    }

    closeErrorModal() {
        this.closeModal('errorModal');
    }

    showSuccess(message) {
        const alert = document.createElement('div');
        alert.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
        alert.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
}
