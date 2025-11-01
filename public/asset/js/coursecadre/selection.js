// selection.js
class SelectionManager {
    constructor() {
        this.init();
    }

    init() {
        this.initCourseSelection();
        this.initCadreSelection();
        this.initExAreaSelection();
    }

    initCourseSelection() {
        const selectAllCheckbox = document.getElementById('selectAllCourses');
        const checkboxes = document.querySelectorAll('.course-checkbox');
        const buttonContainer = document.getElementById('completeCoursesButtonContainer');

        if (selectAllCheckbox && checkboxes.length > 0) {
            this.setupSelection(selectAllCheckbox, checkboxes, buttonContainer);
        }
    }

    initCadreSelection() {
        const selectAllCheckbox = document.getElementById('selectAllCadres');
        const checkboxes = document.querySelectorAll('.cadre-checkbox');
        const buttonContainer = document.getElementById('completeCadresButtonContainer');

        if (selectAllCheckbox && checkboxes.length > 0) {
            this.setupSelection(selectAllCheckbox, checkboxes, buttonContainer);
        }
    }

    initExAreaSelection() {
        const selectAllCheckbox = document.getElementById('selectAllExAreas');
        const checkboxes = document.querySelectorAll('.ex-area-checkbox');
        const buttonContainer = document.getElementById('completeExAreasButtonContainer');

        if (selectAllCheckbox && checkboxes.length > 0) {
            this.setupSelection(selectAllCheckbox, checkboxes, buttonContainer);
        }
    }

    setupSelection(selectAll, checkboxes, buttonContainer) {
        selectAll.addEventListener('change', () => {
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            this.toggleCompleteButton(buttonContainer, checkboxes);
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                const someChecked = Array.from(checkboxes).some(cb => cb.checked);

                selectAll.checked = allChecked;
                selectAll.indeterminate = someChecked && !allChecked;

                this.toggleCompleteButton(buttonContainer, checkboxes);
            });
        });
    }

    toggleCompleteButton(buttonContainer, checkboxes) {
        const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
        if (checkedBoxes.length > 0) {
            buttonContainer.classList.remove('hidden');
        } else {
            buttonContainer.classList.add('hidden');
        }
    }
}
