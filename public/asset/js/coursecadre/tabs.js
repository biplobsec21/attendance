// tabs.js
class CourseCadreTabs {
    constructor(searchInstance = null) {
        this.searchInstance = searchInstance;
        this.activeTab = localStorage.getItem('activeCourseCadreTab') || 'current-courses';
        this.tabButtons = document.querySelectorAll('.tab-button');
        this.tabContents = document.querySelectorAll('.tab-content');
        this.init();
    }

    init() {
        this.activateTab(this.activeTab);
        this.bindEvents();
    }

    bindEvents() {
        this.tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.getAttribute('data-tab');
                this.activateTab(targetTab);
            });
        });
    }

    activateTab(tabName) {
        this.deactivateAllTabs();
        this.activateTabButton(tabName);
        this.showTabContent(tabName);
        this.storeActiveTab(tabName);
        this.applySearchOnTabSwitch();
    }

    deactivateAllTabs() {
        this.tabButtons.forEach(btn => {
            btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        this.tabContents.forEach(content => {
            content.classList.add('hidden');
        });
    }

    activateTabButton(tabName) {
        const activeButton = Array.from(this.tabButtons).find(btn => btn.getAttribute('data-tab') === tabName);
        if (activeButton) {
            activeButton.classList.add('active', 'border-blue-500', 'text-blue-600');
            activeButton.classList.remove('border-transparent', 'text-gray-500');
        }
    }

    showTabContent(tabName) {
        const targetContent = document.getElementById(tabName + '-tab');
        if (targetContent) {
            targetContent.classList.remove('hidden');
        }
    }

    storeActiveTab(tabName) {
        localStorage.setItem('activeCourseCadreTab', tabName);
        this.activeTab = tabName;
    }

    applySearchOnTabSwitch() {
        if (this.searchInstance) {
            const searchInput = document.getElementById('searchInput');
            if (searchInput && searchInput.value.trim()) {
                this.searchInstance.performSearch(searchInput.value.toLowerCase().trim());
            }
        }
    }

    setSearchInstance(searchInstance) {
        this.searchInstance = searchInstance;
    }
}
