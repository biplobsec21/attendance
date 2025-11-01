// main.js
class CourseCadreManager {
    constructor() {
        this.search = null;
        this.tabs = null;
        this.selection = null;
        this.modals = null;
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeComponents();
            this.bindGlobalEvents();
        });
    }

    initializeComponents() {
        // Initialize components in order
        this.modals = new ModalManager();
        this.search = new CourseCadreSearch();
        this.tabs = new CourseCadreTabs(this.search);
        this.selection = new SelectionManager();

        // Set search instance for tabs
        if (this.tabs && this.search) {
            this.tabs.setSearchInstance(this.search);
        }
    }

    bindGlobalEvents() {
        // Global event listeners can be added here
        console.log('CourseCadreManager initialized');
    }
}

// Initialize the main application
new CourseCadreManager();
