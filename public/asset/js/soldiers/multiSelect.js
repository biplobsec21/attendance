export class MultiSelect {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        if (!this.container) return;

        this.placeholder = options.placeholder || 'Select options...';
        this.onChange = options.onChange || (() => { });
        this.selectedValues = new Set();
        this.options = [];

        this.init();
    }

    init() {
        // Create the multi-select structure
        this.container.innerHTML = `
            <div class="multi-select-container">
                <div class="multi-select-input w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 bg-white">
                    <span class="selected-text text-gray-500 truncate">${this.placeholder}</span>
                </div>
                <div class="multi-select-dropdown">
                    <div class="p-2 border-b border-gray-200 sticky top-0 bg-white">
                        <input type="text" class="search-input w-full px-3 py-2 border border-gray-300 rounded text-sm" placeholder="Search...">
                    </div>
                    <div class="options-container max-h-48 overflow-y-auto"></div>
                    <div class="p-2 border-t border-gray-200 flex justify-between items-center bg-white sticky bottom-0">
                        <button class="select-all-btn text-xs text-green-600 hover:text-green-800 font-medium px-2 py-1 rounded">Select All</button>
                        <button class="clear-all-btn text-xs text-gray-600 hover:text-gray-800 font-medium px-2 py-1 rounded">Clear All</button>
                    </div>
                </div>
                <div class="multi-select-tags"></div>
            </div>
        `;

        this.elements = {
            input: this.container.querySelector('.multi-select-input'),
            dropdown: this.container.querySelector('.multi-select-dropdown'),
            optionsContainer: this.container.querySelector('.options-container'),
            searchInput: this.container.querySelector('.search-input'),
            selectAllBtn: this.container.querySelector('.select-all-btn'),
            clearAllBtn: this.container.querySelector('.clear-all-btn'),
            tagsContainer: this.container.querySelector('.multi-select-tags'),
            selectedText: this.container.querySelector('.selected-text')
        };

        this.bindEvents();
    }

    bindEvents() {
        // Toggle dropdown
        this.elements.input.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleDropdown();
        });

        // Search functionality
        this.elements.searchInput.addEventListener('input', (e) => {
            this.filterOptions(e.target.value);
        });

        // Select all
        this.elements.selectAllBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.selectAll();
        });

        // Clear all
        this.elements.clearAllBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.clearAll();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            this.closeDropdown();
        });

        // Prevent dropdown close when clicking inside
        this.elements.dropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Handle escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.elements.dropdown.classList.contains('show')) {
                this.closeDropdown();
            }
        });
    }

    setOptions(options) {
        this.options = options;
        this.renderOptions();
    }

    renderOptions(filteredOptions = null) {
        const optionsToRender = filteredOptions || this.options;

        this.elements.optionsContainer.innerHTML = optionsToRender.map(option => `
            <label class="multi-select-option">
                <input type="checkbox" value="${option.value}" ${this.selectedValues.has(option.value) ? 'checked' : ''}>
                <span class="truncate">${option.label}</span>
            </label>
        `).join('');

        // Add event listeners to checkboxes
        this.elements.optionsContainer.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                e.stopPropagation();
                if (e.target.checked) {
                    this.selectedValues.add(e.target.value);
                } else {
                    this.selectedValues.delete(e.target.value);
                }
                this.updateDisplay();
                this.onChange(Array.from(this.selectedValues));
            });
        });
    }

    filterOptions(searchTerm) {
        const filtered = this.options.filter(option =>
            option.label.toLowerCase().includes(searchTerm.toLowerCase())
        );
        this.renderOptions(filtered);
    }

    selectAll() {
        const visibleOptions = Array.from(this.elements.optionsContainer.querySelectorAll('input[type="checkbox"]'))
            .map(checkbox => checkbox.value);

        visibleOptions.forEach(value => {
            this.selectedValues.add(value);
        });

        this.renderOptions();
        this.updateDisplay();
        this.onChange(Array.from(this.selectedValues));
    }

    clearAll() {
        const visibleOptions = Array.from(this.elements.optionsContainer.querySelectorAll('input[type="checkbox"]'))
            .map(checkbox => checkbox.value);

        visibleOptions.forEach(value => {
            this.selectedValues.delete(value);
        });

        this.renderOptions();
        this.updateDisplay();
        this.onChange(Array.from(this.selectedValues));
    }

    updateDisplay() {
        const selectedCount = this.selectedValues.size;

        // Update selected text
        if (selectedCount === 0) {
            this.elements.selectedText.textContent = this.placeholder;
            this.elements.selectedText.className = 'selected-text text-gray-500 truncate';
            this.elements.input.classList.remove('filter-active');
        } else {
            this.elements.selectedText.textContent = `${selectedCount} selected`;
            this.elements.selectedText.className = 'selected-text text-gray-900 truncate';
            this.elements.input.classList.add('filter-active');
        }

        // Update tags
        this.elements.tagsContainer.innerHTML = '';
        this.selectedValues.forEach(value => {
            const option = this.options.find(opt => opt.value === value);
            if (option) {
                const tag = document.createElement('span');
                tag.className = 'multi-select-tag';
                tag.innerHTML = `
                    <span class="truncate max-w-[120px]">${option.label}</span>
                    <button type="button" data-value="${value}" class="flex-shrink-0">×</button>
                `;
                this.elements.tagsContainer.appendChild(tag);
            }
        });

        // Add event listeners to remove buttons
        this.elements.tagsContainer.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const value = e.target.closest('button').dataset.value;
                this.selectedValues.delete(value);
                this.renderOptions();
                this.updateDisplay();
                this.onChange(Array.from(this.selectedValues));
            });
        });
    }

    toggleDropdown() {
        // Close all dropdowns first
        document.querySelectorAll('.multi-select-dropdown.show').forEach(dropdown => {
            dropdown.classList.remove('show');
        });

        // Then open this one
        this.elements.dropdown.classList.add('show');
        this.elements.searchInput.focus();
        this.elements.searchInput.value = '';
        this.renderOptions();
    }

    closeDropdown() {
        this.elements.dropdown.classList.remove('show');
    }

    getSelectedValues() {
        return Array.from(this.selectedValues);
    }

    setSelectedValues(values) {
        this.selectedValues = new Set(values);
        this.renderOptions();
        this.updateDisplay();
    }

    clear() {
        this.selectedValues.clear();
        this.renderOptions();
        this.updateDisplay();
        this.onChange([]);
    }

    destroy() {
        // Clean up event listeners if needed
        this.container.innerHTML = '';
    }

}
