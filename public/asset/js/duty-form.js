// duty-form.js - ENHANCED VERSION
class DutyForm {
    #initialized = false;
    #debounceTimers = new Map();
    #isAddingGroup = false;
    #elements = {};
    #rankButtons = null;

    constructor() {
        console.log('DutyForm constructor called');
        this.init();
    }

    async init() {
        try {
            await this.#initializeElements();
            this.#initializeData();
            await this.#initializeTimePickers();
            this.#initializeEventListeners();
            this.#renderInitialData();
            this.#initialized = true;
            console.log('DutyForm initialized successfully');
        } catch (error) {
            console.error('DutyForm initialization failed:', error);
            this.#handleInitError(error);
        }
    }

    #handleInitError(error) {
        // Show user-friendly error message
        this.#showNotification('Failed to initialize duty form. Please refresh the page.', 'error');

        // Attempt to recover after a delay
        setTimeout(() => {
            if (!this.#initialized) {
                console.log('Attempting to reinitialize DutyForm...');
                this.init();
            }
        }, 2000);
    }

    #initializeElements() {
        return new Promise((resolve) => {
            // Cache all DOM elements in a single operation
            const elementIds = [
                'duty-form', 'start-time', 'end-time', 'duration-days',
                'total-manpower', 'total-manpower-display', 'rank-search',
                'selected-items-container', 'add-or-group', 'add-fixed-soldier',
                'fixed-soldiers-container', 'soldier-selection-modal',
                'soldier-search', 'soldier-options', 'duration-display',
                'schedule-display', 'multi-day-indicator', 'daily-duration',
                'total-duration'
            ];

            // Create elements object with all references
            const elements = {};
            elementIds.forEach(id => {
                elements[id] = document.getElementById(id);
            });

            // Cache rank buttons once
            this.#rankButtons = document.querySelectorAll('.rank-button');

            // Assign to private property
            this.#elements = elements;

            console.log('Elements initialized');
            resolve();
        });
    }

    #initializeData() {
        // Use efficient data structures with private fields
        this.selectionData = {
            individualRanks: new Map(),
            orGroups: new Map(),
            fixedSoldiers: new Map()
        };

        // Convert initial data to Maps for better performance
        this.#convertInitialData();

        this.availableSoldiers = window.availableSoldiers || [];
        this.groupCounter = this.selectionData.orGroups.size;

        console.log('Data initialized with optimized structures');
    }

    #convertInitialData() {
        // Convert individual ranks
        if (window.initialIndividualRanks) {
            Object.entries(window.initialIndividualRanks).forEach(([id, rank]) => {
                this.selectionData.individualRanks.set(id, rank);
            });
        }

        // Convert rank groups
        if (window.initialRankGroups) {
            window.initialRankGroups.forEach((group, index) => {
                this.selectionData.orGroups.set(group.id || `group_${index}`, {
                    ...group,
                    ranks: new Set(group.ranks || [])
                });
            });
        }

        // Convert fixed soldiers - FIXED: ensure consistent numeric keys
        if (window.initialFixedSoldiers) {
            Object.entries(window.initialFixedSoldiers).forEach(([id, soldier]) => {
                // Convert string id to number for consistency
                const numericId = parseInt(id);
                this.selectionData.fixedSoldiers.set(numericId, {
                    ...soldier,
                    id: numericId  // Ensure id is numeric
                });
            });
            console.log('Converted fixed soldiers:', Array.from(this.selectionData.fixedSoldiers.keys()));
        }
    }

    #initializeTimePickers() {
        return new Promise((resolve) => {
            console.log('Initializing Flatpickr time pickers...');

            // Check if Flatpickr is available, if not load it dynamically
            if (typeof flatpickr === 'undefined') {
                console.warn('Flatpickr not found, loading dynamically...');
                this.#loadFlatpickr().then(() => {
                    this.#setupTimePickers();
                    resolve();
                }).catch(() => {
                    console.error('Failed to load Flatpickr, using fallback');
                    this.#fallbackTimeInputs();
                    resolve();
                });
            } else {
                this.#setupTimePickers();
                resolve();
            }
        });
    }

    #loadFlatpickr() {
        return new Promise((resolve, reject) => {
            // Check if already loaded
            if (typeof flatpickr !== 'undefined') {
                resolve();
                return;
            }

            // Load CSS
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
            document.head.appendChild(link);

            // Load JS
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    #setupTimePickers() {
        // Initialize start time picker
        if (this.#elements['start-time']) {
            this.startTimePicker = flatpickr(this.#elements['start-time'], {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 30,
                defaultHour: 8,
                defaultMinute: 0,
                allowInput: true,
                clickOpens: true,
                onReady: (selectedDates, dateStr, instance) => {
                    this.#styleTimePicker(instance);
                },
                onChange: () => {
                    this.#handleTimeChange();
                },
                onClose: () => {
                    this.#validateTimeFormat(this.#elements['start-time']);
                }
            });

            this.#elements['start-time'].classList.add('flatpickr-input');
        }

        // Initialize end time picker
        if (this.#elements['end-time']) {
            this.endTimePicker = flatpickr(this.#elements['end-time'], {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 30,
                defaultHour: 17,
                defaultMinute: 0,
                allowInput: true,
                clickOpens: true,
                onReady: (selectedDates, dateStr, instance) => {
                    this.#styleTimePicker(instance);
                },
                onChange: () => {
                    this.#handleTimeChange();
                },
                onClose: () => {
                    this.#validateTimeFormat(this.#elements['end-time']);
                }
            });

            this.#elements['end-time'].classList.add('flatpickr-input');
        }

        console.log('Flatpickr time pickers initialized');
    }

    #handleTimeChange() {
        this.#validateTimeFormat(this.#elements['start-time']);
        this.#validateTimeFormat(this.#elements['end-time']);
        this.#validateTimeRange();
        this.#calculateAndDisplayDuration();
    }

    #styleTimePicker(instance) {
        setTimeout(() => {
            const calendar = instance.calendarContainer;
            if (calendar) {
                calendar.classList.add('custom-flatpickr');
                const timeContainer = calendar.querySelector('.flatpickr-time');
                if (timeContainer) {
                    timeContainer.classList.add('bg-white', 'rounded-lg', 'shadow-lg');
                }
                const buttons = calendar.querySelectorAll('.flatpickr-time .numInputWrapper');
                buttons.forEach(btn => {
                    btn.classList.add('custom-time-input');
                });
            }
        }, 100);
    }

    #fallbackTimeInputs() {
        console.log('Using fallback time inputs');
        const timeInputs = document.querySelectorAll('.time-input');
        timeInputs.forEach(input => {
            input.type = 'text';
            input.placeholder = 'HH:MM (24-hour)';
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + ':' + value.substring(2, 4);
                }
                e.target.value = value.substring(0, 5);
            });
            input.addEventListener('blur', (e) => {
                this.#validateTimeFormat(e.target);
                this.#calculateAndDisplayDuration();
            });
        });
    }

    #initializeEventListeners() {
        this.#setupEventDelegation();

        // Debounced event listeners for search
        if (this.#elements['rank-search']) {
            this.#elements['rank-search'].addEventListener('input', this.#debounce((e) => {
                this.#filterRanks(e.target.value);
            }, 300));
        }

        if (this.#elements['soldier-search']) {
            this.#elements['soldier-search'].addEventListener('input', this.#debounce((e) => {
                this.#filterSoldiers(e.target.value);
            }, 300));
        }

        // Single click listeners for buttons
        if (this.#elements['add-or-group']) {
            this.#elements['add-or-group'].addEventListener('click', () => this.#addOrGroup());
        }

        if (this.#elements['add-fixed-soldier']) {
            this.#elements['add-fixed-soldier'].addEventListener('click', () => this.#openSoldierSelectionModal());
        }

        // Time and duration listeners
        const timeInputs = [
            this.#elements['start-time'],
            this.#elements['end-time'],
            this.#elements['duration-days']
        ];

        timeInputs.forEach(input => {
            if (input) {
                input.addEventListener('change', () => this.#calculateAndDisplayDuration());
            }
        });

        // Form submission
        if (this.#elements['duty-form']) {
            this.#elements['duty-form'].addEventListener('submit', (e) => this.#handleFormSubmission(e));
        }

        console.log('Event listeners initialized with delegation');
    }

    #setupEventDelegation() {
        // Single event listener for rank buttons
        const availableRanksGrid = document.getElementById('available-ranks-grid');
        if (availableRanksGrid) {
            availableRanksGrid.addEventListener('click', (e) => {
                const rankButton = e.target.closest('.rank-button');
                if (rankButton && !rankButton.disabled) {
                    this.#addIndividualRank(rankButton);
                }
            });
        }

        // Event delegation for selected items container
        if (this.#elements['selected-items-container']) {
            this.#elements['selected-items-container'].addEventListener('click', (e) => {
                this.#handleSelectedItemsClick(e);
            });

            this.#elements['selected-items-container'].addEventListener('change', (e) => {
                this.#handleSelectedItemsChange(e);
            });
        }

        // Event delegation for fixed soldiers container - IMPROVED
        if (this.#elements['fixed-soldiers-container']) {
            this.#elements['fixed-soldiers-container'].addEventListener('click', (e) => {
                // Check for remove button first - look for closest button or SVG parent
                let removeBtn = e.target.closest('.remove-fixed-soldier');

                // If clicked on SVG or path inside the button, find the button
                if (!removeBtn && e.target.closest('svg')) {
                    removeBtn = e.target.closest('svg').closest('.remove-fixed-soldier');
                }

                if (removeBtn) {
                    e.preventDefault();
                    e.stopPropagation();

                    const soldierId = removeBtn.dataset.soldierId;
                    console.log('Attempting to remove soldier:', soldierId);

                    if (soldierId) {
                        const parsedId = parseInt(soldierId);
                        console.log('Parsed soldier ID:', parsedId);
                        console.log('Current fixedSoldiers:', Array.from(this.selectionData.fixedSoldiers.keys()));

                        this.#removeFixedSoldier(parsedId);
                        this.#showNotification('Soldier removed successfully', 'success');
                    }
                    return;
                }

                // Handle other click events
                this.#handleFixedSoldiersClick(e);
            });

            this.#elements['fixed-soldiers-container'].addEventListener('change', (e) => {
                this.#handleFixedSoldiersChange(e);
            });
        }

        // Modal close handler
        if (this.#elements['soldier-selection-modal']) {
            this.#elements['soldier-selection-modal'].addEventListener('click', (e) => {
                if (e.target === this.#elements['soldier-selection-modal']) {
                    this.#closeSoldierModal();
                }
            });
        }
    }

    #handleSelectedItemsClick(e) {
        const target = e.target;

        // Use data attributes to identify actions
        const action = target.closest('[data-action]')?.dataset.action;
        const id = target.closest('[data-id]')?.dataset.id;

        if (!action || !id) return;

        switch (action) {
            case 'remove-rank':
                this.#removeRank(id);
                break;
            case 'remove-group':
                this.#removeGroup(id);
                break;
            case 'decrease-rank-manpower':
                this.#decreaseRankManpower(id);
                break;
            case 'increase-rank-manpower':
                this.#increaseRankManpower(id);
                break;
            case 'decrease-group-manpower':
                this.#decreaseGroupManpower(id);
                break;
            case 'increase-group-manpower':
                this.#increaseGroupManpower(id);
                break;
            case 'edit-group-ranks':
                this.#openGroupRankSelector(id);
                break;
        }
    }

    #handleSelectedItemsChange(e) {
        const target = e.target;

        if (target.classList.contains('rank-manpower-input')) {
            const rankId = target.dataset.rankId;
            if (rankId) this.#updateRankManpower(rankId, target.value);
        } else if (target.classList.contains('group-manpower-input')) {
            const groupId = target.dataset.groupId;
            if (groupId) this.#updateGroupManpower(groupId, target.value);
        }
    }

    #handleFixedSoldiersClick(e) {
        const target = e.target;
        const removeSoldierBtn = target.closest('.remove-fixed-soldier');

        if (removeSoldierBtn) {
            const soldierId = removeSoldierBtn.dataset.soldierId;
            if (soldierId) this.#removeFixedSoldier(parseInt(soldierId));
        }
    }

    #handleFixedSoldiersChange(e) {
        const target = e.target;
        const soldierId = target.dataset.soldierId;

        if (!soldierId) return;

        if (target.classList.contains('fixed-soldier-priority')) {
            this.#updateFixedSoldierPriority(parseInt(soldierId), target.value);
        } else if (target.classList.contains('fixed-soldier-remarks')) {
            this.#updateFixedSoldierRemarks(parseInt(soldierId), target.value);
        }
    }

    #debounce(func, wait) {
        return (...args) => {
            clearTimeout(this.#debounceTimers.get(func));
            this.#debounceTimers.set(func, setTimeout(() => func.apply(this, args), wait));
        };
    }

    #renderInitialData() {
        console.log('Rendering initial data...');
        this.#renderSelectedItems();
        this.#renderFixedSoldiers();
        this.#calculateAndDisplayDuration();
        this.#updateTotalManpower();
        this.#updateRankButtonStates();
        console.log('Initial data rendered');
    }

    #updateRankButtonStates() {
        this.#rankButtons.forEach(btn => {
            const rankId = btn.dataset.rankId;
            const isIndividual = this.selectionData.individualRanks.has(rankId);
            const isInGroup = Array.from(this.selectionData.orGroups.values()).some(group =>
                group.ranks.has(rankId)
            );

            if (isIndividual) {
                btn.disabled = true;
                btn.classList.add('border-blue-500', 'bg-blue-100', 'text-blue-700');
            } else if (isInGroup) {
                btn.disabled = true;
                btn.classList.add('border-purple-500', 'bg-purple-100', 'text-purple-700');
            } else {
                btn.disabled = false;
                btn.classList.remove('border-blue-500', 'bg-blue-100', 'text-blue-700',
                    'border-purple-500', 'bg-purple-100', 'text-purple-700');
            }
        });
    }

    // Optional optimization for large rank lists
    #filterRanks(searchTerm) {
        const term = searchTerm.toLowerCase();

        // Use requestAnimationFrame for smooth animation
        requestAnimationFrame(() => {
            this.#rankButtons.forEach(btn => {
                const rankName = btn.dataset.rankName.toLowerCase();
                // Use display: none instead of visibility for better performance
                btn.style.display = rankName.includes(term) ? 'block' : 'none';
            });
        });
    }

    #addIndividualRank(button) {
        const rankId = button.dataset.rankId;
        const rankName = button.dataset.rankName;

        // Check if already selected as individual
        if (this.selectionData.individualRanks.has(rankId)) {
            return;
        }

        // Check if in any OR group
        for (let group of this.selectionData.orGroups.values()) {
            if (group.ranks.has(rankId)) {
                this.#showNotification('This rank is already in an OR group.', 'warning');
                return;
            }
        }

        // Add to selection
        this.selectionData.individualRanks.set(rankId, {
            id: rankId,
            name: rankName,
            manpower: 1
        });

        // Disable button immediately
        button.disabled = true;
        button.classList.add('border-blue-500', 'bg-blue-100', 'text-blue-700');

        this.#renderSelectedItems();
        this.#updateTotalManpower();
    }

    #addOrGroup() {
        // Prevent duplicate groups
        if (this.#isAddingGroup) return;
        this.#isAddingGroup = true;

        this.groupCounter++;
        const groupId = `group_${Date.now()}_${this.groupCounter}`;

        this.selectionData.orGroups.set(groupId, {
            id: groupId,
            ranks: new Set(),
            manpower: 1
        });

        this.#renderSelectedItems();
        this.#updateTotalManpower();

        // Reset flag after render
        setTimeout(() => {
            this.#isAddingGroup = false;
        }, 100);
    }

    #renderSelectedItems() {
        if (!this.#elements['selected-items-container']) return;

        // Use DocumentFragment for batch DOM updates
        const fragment = document.createDocumentFragment();
        const hasItems = this.selectionData.individualRanks.size > 0 ||
            this.selectionData.orGroups.size > 0;

        if (!hasItems) {
            this.#showEmptyState();
            return;
        }

        // Render individual ranks
        this.selectionData.individualRanks.forEach(rank => {
            fragment.appendChild(this.#createRankCard(rank));
        });

        // Render OR groups
        this.selectionData.orGroups.forEach(group => {
            fragment.appendChild(this.#createGroupCard(group));
        });

        // Single DOM update
        this.#elements['selected-items-container'].innerHTML = '';
        this.#elements['selected-items-container'].appendChild(fragment);
    }

    #showEmptyState() {
        this.#elements['selected-items-container'].innerHTML = `
            <div id="empty-state" class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gradient-to-r from-gray-50 to-blue-50/30">
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-sm text-gray-500">No ranks selected yet</p>
                <p class="text-xs text-gray-400 mt-1">Click on ranks above or create OR groups to get started</p>
            </div>
        `;
    }

    #createRankCard(rank) {
        const card = document.createElement('div');
        card.className = 'bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200';
        card.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 bg-blue-500 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">${rank.name}</h4>
                        <p class="text-xs text-gray-600">Individual Rank</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center">
                        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50" data-action="decrease-rank-manpower" data-id="${rank.id}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <input type="number" min="1" value="${rank.manpower}"
                            class="rank-manpower-input w-16 h-8 text-center border-t border-b border-gray-300 focus:outline-none"
                            data-rank-id="${rank.id}">
                        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50" data-action="increase-rank-manpower" data-id="${rank.id}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-red-500 transition-colors" data-action="remove-rank" data-id="${rank.id}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <input type="hidden" name="rank_manpower[${rank.id}][rank_id]" value="${rank.id}">
            <input type="hidden" name="rank_manpower[${rank.id}][manpower]" value="${rank.manpower}" id="rank-manpower-${rank.id}">
        `;
        return card;
    }

    #createGroupCard(group) {
        const card = document.createElement('div');
        card.className = 'bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-200';

        const rankNames = Array.from(group.ranks).map(rankId => {
            const btn = document.querySelector(`[data-rank-id="${rankId}"]`);
            return btn ? btn.dataset.rankName : 'Unknown';
        }).join(' OR ');

        card.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 bg-purple-500 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">OR Group</h4>
                        <p class="text-xs text-gray-600">Any of these ranks can fulfill</p>
                    </div>
                </div>
                <button type="button" class="text-gray-400 hover:text-red-500 transition-colors" data-action="remove-group" data-id="${group.id}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="mb-3">
                <div class="bg-white rounded-lg p-3 min-h-[50px] border border-gray-200">
                    ${rankNames || '<span class="text-gray-400 text-sm">Click below to add ranks to this group</span>'}
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 mr-3">Manpower:</span>
                    <div class="flex items-center">
                        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50" data-action="decrease-group-manpower" data-id="${group.id}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <input type="number" min="1" value="${group.manpower}"
                            class="group-manpower-input w-16 h-8 text-center border-t border-b border-gray-300 focus:outline-none"
                            data-group-id="${group.id}">
                        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50" data-action="increase-group-manpower" data-id="${group.id}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="button" class="px-3 py-1.5 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-200 transition-colors" data-action="edit-group-ranks" data-id="${group.id}">
                    ${group.ranks.size > 0 ? 'Edit Ranks' : 'Add Ranks'}
                </button>
            </div>

            <input type="hidden" name="rank_groups[${group.id}][manpower]" value="${group.manpower}" id="group-manpower-${group.id}">
            ${Array.from(group.ranks).map(rankId =>
            `<input type="hidden" name="rank_groups[${group.id}][ranks][]" value="${rankId}">`
        ).join('')}
        `;

        return card;
    }

    // Rank Manpower Methods
    #decreaseRankManpower(rankId) {
        const rank = this.selectionData.individualRanks.get(rankId);
        if (rank && rank.manpower > 1) {
            rank.manpower--;
            const input = document.getElementById(`rank-manpower-${rankId}`);
            if (input) input.value = rank.manpower;
            this.#renderSelectedItems();
            this.#updateTotalManpower();
        }
    }

    #increaseRankManpower(rankId) {
        const rank = this.selectionData.individualRanks.get(rankId);
        if (rank) {
            rank.manpower++;
            const input = document.getElementById(`rank-manpower-${rankId}`);
            if (input) input.value = rank.manpower;
            this.#renderSelectedItems();
            this.#updateTotalManpower();
        }
    }

    #updateRankManpower(rankId, value) {
        const rank = this.selectionData.individualRanks.get(rankId);
        if (rank) {
            const newValue = Math.max(1, parseInt(value) || 1);
            rank.manpower = newValue;
            const input = document.getElementById(`rank-manpower-${rankId}`);
            if (input) input.value = rank.manpower;
            this.#renderSelectedItems();
            this.#updateTotalManpower();
        }
    }

    #removeRank(rankId) {
        this.selectionData.individualRanks.delete(rankId);
        this.#enableRankButton(rankId);
        this.#renderSelectedItems();
        this.#updateTotalManpower();
    }

    // Group Manpower Methods
    #decreaseGroupManpower(groupId) {
        const group = this.selectionData.orGroups.get(groupId);
        if (group && group.manpower > 1) {
            group.manpower--;
            const input = document.getElementById(`group-manpower-${groupId}`);
            if (input) input.value = group.manpower;
            this.#renderSelectedItems();
            this.#updateTotalManpower();
        }
    }

    #increaseGroupManpower(groupId) {
        const group = this.selectionData.orGroups.get(groupId);
        if (group) {
            group.manpower++;
            const input = document.getElementById(`group-manpower-${groupId}`);
            if (input) input.value = group.manpower;
            this.#renderSelectedItems();
            this.#updateTotalManpower();
        }
    }

    #updateGroupManpower(groupId, value) {
        const group = this.selectionData.orGroups.get(groupId);
        if (group) {
            const newValue = Math.max(1, parseInt(value) || 1);
            group.manpower = newValue;
            const input = document.getElementById(`group-manpower-${groupId}`);
            if (input) input.value = group.manpower;
            this.#renderSelectedItems();
            this.#updateTotalManpower();
        }
    }

    #removeGroup(groupId) {
        const group = this.selectionData.orGroups.get(groupId);
        if (!group) return;

        // Re-enable all rank buttons in this group
        group.ranks.forEach(rankId => {
            this.#enableRankButton(rankId);
        });

        this.selectionData.orGroups.delete(groupId);
        this.#renderSelectedItems();
        this.#updateTotalManpower();
    }

    #enableRankButton(rankId) {
        const btn = document.querySelector(`[data-rank-id="${rankId}"]`);
        if (btn) {
            btn.disabled = false;
            btn.classList.remove('border-blue-500', 'bg-blue-100', 'text-blue-700',
                'border-purple-500', 'bg-purple-100', 'text-purple-700');
        }
    }

    // Group Rank Selection Modal
    #openGroupRankSelector(groupId) {
        const group = this.selectionData.orGroups.get(groupId);
        if (!group) return;

        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                    <h3 class="text-xl font-bold text-gray-800">Select Ranks for OR Group</h3>
                    <p class="text-sm text-gray-600 mt-1">Select multiple ranks - any ONE of them can fulfill this duty</p>
                </div>

                <div class="p-6 max-h-[50vh] overflow-y-auto">
                    <div class="relative mb-4">
                        <input type="text" id="modal-rank-search" placeholder="Search ranks..."
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20">
                        <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <div class="grid grid-cols-2 gap-2" id="modal-rank-grid">
                        ${Array.from(this.#rankButtons).map(btn => {
            const rankId = btn.dataset.rankId;
            const rankName = btn.dataset.rankName;
            const isSelected = group.ranks.has(rankId);
            const isDisabled = this.selectionData.individualRanks.has(rankId) ||
                (Array.from(this.selectionData.orGroups.values()).some(g =>
                    g.id !== groupId && g.ranks.has(rankId)));

            return `
                                <button type="button"
                                    class="modal-rank-btn px-4 py-3 rounded-lg border-2 text-sm font-medium transition-all ${isSelected ? 'border-purple-500 bg-purple-100 text-purple-700' :
                    isDisabled ? 'border-gray-200 bg-gray-100 text-gray-400 cursor-not-allowed' :
                        'border-gray-200 bg-white text-gray-700 hover:border-purple-300 hover:bg-purple-50'
                }"
                                    data-rank-id="${rankId}"
                                    data-rank-name="${rankName}"
                                    ${isDisabled ? 'disabled' : ''}>
                                    ${rankName}
                                    ${isSelected ? '<svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' : ''}
                                </button>
                            `;
        }).join('')}
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                    <p class="text-sm text-gray-600">
                        <span class="font-semibold" id="selected-count">${group.ranks.size}</span> rank(s) selected
                    </p>
                    <div class="flex space-x-3">
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors" onclick="this.closest('.fixed').remove()">
                            Cancel
                        </button>
                        <button type="button" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors" id="confirm-group-ranks">
                            Confirm Selection
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Modal functionality
        const modalRankSearch = modal.querySelector('#modal-rank-search');
        const modalRankBtns = modal.querySelectorAll('.modal-rank-btn:not([disabled])');
        const selectedCountEl = modal.querySelector('#selected-count');
        const confirmBtn = modal.querySelector('#confirm-group-ranks');

        let tempSelectedRanks = new Set(group.ranks);

        // Search
        modalRankSearch.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            modalRankBtns.forEach(btn => {
                const rankName = btn.dataset.rankName.toLowerCase();
                btn.style.display = rankName.includes(searchTerm) ? 'block' : 'none';
            });
        });

        // Rank selection
        modalRankBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const rankId = this.dataset.rankId;

                if (tempSelectedRanks.has(rankId)) {
                    tempSelectedRanks.delete(rankId);
                    this.classList.remove('border-purple-500', 'bg-purple-100', 'text-purple-700');
                    this.classList.add('border-gray-200', 'bg-white', 'text-gray-700');
                    this.innerHTML = this.dataset.rankName;
                } else {
                    tempSelectedRanks.add(rankId);
                    this.classList.add('border-purple-500', 'bg-purple-100', 'text-purple-700');
                    this.classList.remove('border-gray-200', 'bg-white', 'text-gray-700');
                    this.innerHTML = this.dataset.rankName +
                        ' <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                }

                selectedCountEl.textContent = tempSelectedRanks.size;
            });
        });

        // Confirm
        confirmBtn.addEventListener('click', () => {
            // Re-enable previously selected ranks
            group.ranks.forEach(rankId => {
                if (!tempSelectedRanks.has(rankId)) {
                    this.#enableRankButton(rankId);
                }
            });

            // Disable newly selected ranks
            tempSelectedRanks.forEach(rankId => {
                const btn = document.querySelector(`[data-rank-id="${rankId}"]`);
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('border-purple-500', 'bg-purple-100', 'text-purple-700');
                }
            });

            group.ranks = tempSelectedRanks;
            this.#renderSelectedItems();
            this.#updateTotalManpower();
            modal.remove();
        });

        // Close on backdrop click
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    #openSoldierSelectionModal() {
        if (this.#elements['soldier-selection-modal']) {
            this.#elements['soldier-selection-modal'].classList.remove('hidden');

            // Auto-load soldiers when modal opens
            if (window.soldierLoader) {
                window.soldierLoader.autoLoadOnModalOpen();
            }
        }
    }


    #closeSoldierModal() {
        if (this.#elements['soldier-selection-modal']) {
            this.#elements['soldier-selection-modal'].classList.add('hidden');

            // Optional: Clear search when closing
            const soldierSearch = document.getElementById('soldier-search');
            if (soldierSearch) {
                soldierSearch.value = '';
            }
        }
    }

    #filterSoldiers(searchTerm) {
        const term = searchTerm.toLowerCase();
        const soldierOptions = this.#elements['soldier-options'] ?
            this.#elements['soldier-options'].querySelectorAll('.soldier-option') : [];

        let visibleCount = 0;

        soldierOptions.forEach(option => {
            const soldierName = option.dataset.soldierName || '';
            const armyNo = option.dataset.armyNo || '';
            const rank = option.dataset.rank || '';
            const company = option.dataset.company || '';

            const matches = soldierName.includes(term) ||
                armyNo.includes(term) ||
                rank.includes(term) ||
                company.includes(term);

            if (matches) {
                option.style.display = 'block';
                visibleCount++;
            } else {
                option.style.display = 'none';
            }
        });

        const filteredCountEl = document.getElementById('filtered-soldier-count');
        if (filteredCountEl) {
            filteredCountEl.textContent = visibleCount;
        }
    }

    #addFixedSoldier(soldier) {
        const soldierId = soldier.id;

        // Check if soldier is already added
        if (this.selectionData.fixedSoldiers.has(soldierId)) {
            this.#showNotification('This soldier is already assigned to this duty.', 'warning');
            return;
        }

        // Add to data structure
        this.selectionData.fixedSoldiers.set(soldierId, {
            id: soldierId,
            soldier: soldier,
            priority: 1,
            remarks: ''
        });

        this.#renderFixedSoldiers();
    }

    #renderFixedSoldiers() {
        if (!this.#elements['fixed-soldiers-container']) return;

        console.log('Rendering fixed soldiers:', this.selectionData.fixedSoldiers.size);

        this.#elements['fixed-soldiers-container'].innerHTML = '';

        if (this.selectionData.fixedSoldiers.size === 0) {
            this.#elements['fixed-soldiers-container'].innerHTML = `
                <div class="text-center py-8 text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <p class="text-sm">No fixed soldiers assigned yet</p>
                    <p class="text-xs mt-1">Click "Add Fixed Soldier" to assign specific soldiers</p>
                </div>
            `;
            return;
        }

        const fragment = document.createDocumentFragment();
        this.selectionData.fixedSoldiers.forEach(soldierData => {
            const card = this.#createFixedSoldierCard(soldierData);
            fragment.appendChild(card);
        });
        this.#elements['fixed-soldiers-container'].appendChild(fragment);

        // Attach remove button handlers after rendering
        this.#attachRemoveButtonHandlers();
    }

    #attachRemoveButtonHandlers() {
        // Add direct click handlers to each remove button as a fallback
        document.querySelectorAll('.remove-fixed-soldier').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const soldierId = btn.dataset.soldierId;
                console.log('Remove button clicked for soldier:', soldierId);

                if (soldierId) {
                    this.#removeFixedSoldier(parseInt(soldierId));
                    this.#showNotification('Soldier removed successfully', 'success');
                }
            });
        });
    }

    #createFixedSoldierCard(soldierData) {
        const card = document.createElement('div');
        card.className = 'bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200 hover:border-green-400 transition-all duration-200';
        card.innerHTML = `
        <div class="flex items-center gap-3 p-3">
            <!-- Avatar -->
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center ring-2 ring-green-200">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>

            <!-- Soldier Info -->
            <div class="flex-1 min-w-0 flex items-center gap-3">
                <div class="min-w-0 flex-shrink">
                    <h4 class="font-semibold text-gray-900 text-sm truncate">${soldierData.soldier.full_name}</h4>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-600 flex-shrink-0">
                    <span class="font-mono font-medium bg-white px-2 py-0.5 rounded border border-gray-200">${soldierData.soldier.army_no}</span>
                    <span class="font-medium text-gray-500">${soldierData.soldier.rank}</span>
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-500">${soldierData.soldier.company}</span>
                </div>
            </div>

            <!-- Remarks Input (Compact) -->
            <div class="flex-shrink-0 w-48">
                <input type="text" value="${soldierData.remarks}" placeholder="Add remarks..."
                    class="fixed-soldier-remarks w-full px-3 py-1.5 text-xs border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition-all"
                    data-soldier-id="${soldierData.id}">
            </div>

            <!-- Fixed Badge -->
            <div class="flex-shrink-0">
                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                    FIXED
                </span>
            </div>

            <!-- Remove Button -->
            <button type="button" class="remove-fixed-soldier flex-shrink-0 text-gray-400 hover:text-red-500 transition-colors cursor-pointer pointer-events-auto" data-soldier-id="${soldierData.id}" title="Remove soldier">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Hidden Inputs -->
        <input type="hidden" name="fixed_soldiers[${soldierData.id}][soldier_id]" value="${soldierData.id}">
        <input type="hidden" name="fixed_soldiers[${soldierData.id}][priority]" value="${soldierData.priority}" id="fixed-priority-${soldierData.id}">
        <input type="hidden" name="fixed_soldiers[${soldierData.id}][remarks]" value="${soldierData.remarks}" id="fixed-remarks-${soldierData.id}">
    `;
        return card;
    }

    #removeFixedSoldier(soldierId) {
        const hadSoldier = this.selectionData.fixedSoldiers.has(soldierId);

        if (hadSoldier) {
            this.selectionData.fixedSoldiers.delete(soldierId);
            console.log('Removed soldier:', soldierId, 'Remaining:', Array.from(this.selectionData.fixedSoldiers.keys()));
            this.#renderFixedSoldiers();
            this.#updateTotalManpower();
        } else {
            console.warn('Soldier not found in selection:', soldierId);
        }
    }

    #updateFixedSoldierPriority(soldierId, priority) {
        const soldier = this.selectionData.fixedSoldiers.get(soldierId);
        if (soldier) {
            soldier.priority = Math.max(1, Math.min(10, parseInt(priority) || 1));
            const input = document.getElementById(`fixed-priority-${soldierId}`);
            if (input) input.value = soldier.priority;
        }
    }

    #updateFixedSoldierRemarks(soldierId, remarks) {
        const soldier = this.selectionData.fixedSoldiers.get(soldierId);
        if (soldier) {
            soldier.remarks = remarks;
            const input = document.getElementById(`fixed-remarks-${soldierId}`);
            if (input) input.value = remarks;
        }
    }

    // Time Validation Methods
    #validateTimeFormat(input) {
        const timeRegex = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
        const value = input.value.trim();

        if (value && !timeRegex.test(value)) {
            input.classList.add('border-rose-500', 'focus:border-rose-500', 'focus:ring-rose-500/20');
            input.classList.remove('border-gray-200', 'focus:border-blue-500', 'focus:ring-blue-500/20');
            this.#showTimeError(input, 'Please enter time in HH:MM format (00:00 to 23:59)');
            return false;
        } else {
            input.classList.remove('border-rose-500', 'focus:border-rose-500', 'focus:ring-rose-500/20');
            input.classList.add('border-gray-200', 'focus:border-blue-500', 'focus:ring-blue-500/20');
            this.#removeTimeError(input);
            return true;
        }
    }

    #showTimeError(input, message) {
        this.#removeTimeError(input);
        const errorEl = document.createElement('p');
        errorEl.className = 'text-rose-500 text-xs mt-1 time-error';
        errorEl.textContent = message;
        input.parentNode.appendChild(errorEl);
    }

    #removeTimeError(input) {
        const existingError = input.parentNode.querySelector('.time-error');
        if (existingError) {
            existingError.remove();
        }
    }

    #validateTimeRange() {
        const startTime = this.#elements['start-time'] ? this.#elements['start-time'].value : '';
        const endTime = this.#elements['end-time'] ? this.#elements['end-time'].value : '';

        if (!startTime || !endTime) return;

        this.#removeTimeRangeWarning();

        // Check for 24-hour duty
        if (startTime === endTime) {
            this.#showTimeRangeWarning('24-hour duty selected');
            return;
        }

        // Check if end time is before start time
        const [startHour, startMinute] = startTime.split(':').map(Number);
        const [endHour, endMinute] = endTime.split(':').map(Number);

        if (endHour < startHour || (endHour === startHour && endMinute < startMinute)) {
            this.#showTimeRangeWarning('Overnight duty selected');
            return;
        }
    }

    #showTimeRangeWarning(message) {
        this.#removeTimeRangeWarning();
        const warningEl = document.createElement('div');
        warningEl.id = 'time-range-warning';
        warningEl.className = 'mt-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-700 text-sm';
        warningEl.innerHTML = `
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <span>${message}</span>
            </div>
        `;

        const durationDaysContainer = this.#elements['duration-days'] ?
            this.#elements['duration-days'].closest('div') : null;
        if (durationDaysContainer) {
            durationDaysContainer.parentNode.insertBefore(warningEl, durationDaysContainer.nextSibling);
        }
    }

    #removeTimeRangeWarning() {
        const existingWarning = document.getElementById('time-range-warning');
        if (existingWarning) {
            existingWarning.remove();
        }
    }

    // Duration Calculation Methods
    #calculateAndDisplayDuration() {
        const startTime = this.#elements['start-time'] ? this.#elements['start-time'].value : '';
        const endTime = this.#elements['end-time'] ? this.#elements['end-time'].value : '';
        const durationDays = this.#elements['duration-days'] ?
            parseInt(this.#elements['duration-days'].value) || 1 : 1;

        if (!startTime || !endTime) {
            if (this.#elements['duration-display'])
                this.#elements['duration-display'].classList.add('hidden');
            return;
        }

        const dailyDurationHours = this.#calculateDailyDuration(startTime, endTime);
        const totalDurationHours = dailyDurationHours * durationDays;

        let scheduleText = `${startTime} - ${endTime}`;
        const isOvernight = this.#isOvernightDuty(startTime, endTime);

        if (isOvernight) {
            scheduleText += ' (overnight)';
        }
        if (durationDays > 1) {
            scheduleText += ` for ${durationDays} days`;
        }

        if (this.#elements['schedule-display'])
            this.#elements['schedule-display'].textContent = scheduleText;
        if (this.#elements['daily-duration'])
            this.#elements['daily-duration'].textContent = `${dailyDurationHours.toFixed(1)} hours`;
        if (this.#elements['total-duration'])
            this.#elements['total-duration'].textContent = `${totalDurationHours.toFixed(1)} hours`;

        if (this.#elements['multi-day-indicator']) {
            if (durationDays > 1) {
                this.#elements['multi-day-indicator'].classList.remove('hidden');
                this.#elements['duration-display'].classList.add('border-green-200', 'bg-green-50');
                this.#elements['duration-display'].classList.remove('border-blue-200', 'bg-blue-50', 'border-purple-200', 'bg-purple-50');
            } else if (isOvernight) {
                this.#elements['multi-day-indicator'].classList.add('hidden');
                this.#elements['duration-display'].classList.add('border-purple-200', 'bg-purple-50');
                this.#elements['duration-display'].classList.remove('border-blue-200', 'bg-blue-50', 'border-green-200', 'bg-green-50');
            } else {
                this.#elements['multi-day-indicator'].classList.add('hidden');
                this.#elements['duration-display'].classList.add('border-blue-200', 'bg-blue-50');
                this.#elements['duration-display'].classList.remove('border-purple-200', 'bg-purple-50', 'border-green-200', 'bg-green-50');
            }
        }

        if (this.#elements['duration-display'])
            this.#elements['duration-display'].classList.remove('hidden');

        // Validation warnings
        if (this.#elements['duration-display']) {
            if (dailyDurationHours < 1 && startTime !== endTime) {
                this.#elements['duration-display'].classList.add('border-rose-200', 'bg-rose-50');
            } else if (totalDurationHours > 720) {
                this.#elements['duration-display'].classList.add('border-amber-200', 'bg-amber-50');
            } else {
                this.#elements['duration-display'].classList.remove('border-rose-200', 'bg-rose-50', 'border-amber-200', 'bg-amber-50');
            }
        }
    }

    #calculateDailyDuration(startTime, endTime) {
        const [startHour, startMinute] = startTime.split(':').map(Number);
        const [endHour, endMinute] = endTime.split(':').map(Number);

        let durationHours = 0;

        if (endHour > startHour || (endHour === startHour && endMinute > startMinute)) {
            durationHours = (endHour - startHour) + (endMinute - startMinute) / 60;
        } else {
            durationHours = (24 - startHour + endHour) + (endMinute - startMinute) / 60;
        }

        return durationHours;
    }

    #isOvernightDuty(startTime, endTime) {
        const [startHour, startMinute] = startTime.split(':').map(Number);
        const [endHour, endMinute] = endTime.split(':').map(Number);

        return endHour < startHour || (endHour === startHour && endMinute < startMinute);
    }

    // Manpower Calculation
    #updateTotalManpower() {
        let total = 0;

        // Add individual ranks manpower
        this.selectionData.individualRanks.forEach(rank => {
            total += rank.manpower;
        });

        // Add rank groups manpower
        this.selectionData.orGroups.forEach(group => {
            total += group.manpower;
        });

        // Update display
        if (this.#elements['total-manpower'])
            this.#elements['total-manpower'].value = total;
        if (this.#elements['total-manpower-display'])
            this.#elements['total-manpower-display'].textContent = total;
    }

    // Form Submission
    #handleFormSubmission(e) {
        // Clear previous errors
        document.querySelectorAll('.rank-error, .time-error, .time-range-error').forEach(el => el.remove());

        // Validate time formats
        const startTimeValid = this.#elements['start-time'] ?
            this.#validateTimeFormat(this.#elements['start-time']) : true;
        const endTimeValid = this.#elements['end-time'] ?
            this.#validateTimeFormat(this.#elements['end-time']) : true;

        if (!startTimeValid || !endTimeValid) {
            e.preventDefault();
            return;
        }

        // Validate time range
        const startTime = this.#elements['start-time'] ? this.#elements['start-time'].value : '';
        const endTime = this.#elements['end-time'] ? this.#elements['end-time'].value : '';

        if (startTime && endTime) {
            const dailyDuration = this.#calculateDailyDuration(startTime, endTime);
            if (dailyDuration < 1 && startTime !== endTime) {
                e.preventDefault();
                this.#showTimeError(this.#elements['end-time'], 'Duty duration must be at least 1 hour');
                return;
            }
        }

        const hasIndividualRanks = this.selectionData.individualRanks.size > 0;
        const hasValidGroups = Array.from(this.selectionData.orGroups.values()).some(group => group.ranks.size > 0);
        const hasFixedSoldiers = this.selectionData.fixedSoldiers.size > 0;

        // Validate at least one assignment type
        if (!hasIndividualRanks && !hasValidGroups && !hasFixedSoldiers) {
            e.preventDefault();

            const errorEl = document.createElement('p');
            errorEl.className = 'text-rose-500 text-sm mt-2 flex items-center rank-error';
            errorEl.innerHTML = `
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Please select at least one rank or create an OR group with ranks, or assign fixed soldiers
            `;
            this.#elements['selected-items-container'].parentElement.appendChild(errorEl);
            return;
        }

        // Check for empty groups
        const emptyGroups = Array.from(this.selectionData.orGroups.values()).filter(group => group.ranks.size === 0);
        if (emptyGroups.length > 0) {
            e.preventDefault();

            const errorEl = document.createElement('p');
            errorEl.className = 'text-rose-500 text-sm mt-2 flex items-center rank-error';
            errorEl.innerHTML = `
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Please add ranks to all OR groups or remove empty groups
            `;
            this.#elements['selected-items-container'].parentElement.appendChild(errorEl);
            return;
        }

        // Show loading state
        const submitBtn = this.#elements['duty-form'] ?
            this.#elements['duty-form'].querySelector('button[type="submit"]') : null;
        if (submitBtn) {
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                ${submitBtn.textContent.includes('Update') ? 'Updating...' : 'Saving...'}
            `;
            submitBtn.disabled = true;
        }
    }

    #showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-transform duration-300 ${type === 'warning' ? 'bg-amber-100 border-amber-300 text-amber-800' :
            type === 'error' ? 'bg-rose-100 border-rose-300 text-rose-800' :
                'bg-green-100 border-green-300 text-green-800'
            }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'warning' ?
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>' :
                type === 'error' ?
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>'
            }
                </svg>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Public wrapper methods for global functions
    selectSoldier(soldier) {
        // Now soldier is an object, not just an ID
        this.#addFixedSoldier(soldier);
        this.#closeSoldierModal();
    }

    closeSoldierModal() {
        this.#closeSoldierModal();
    }

    isInitialized() {
        return this.#initialized;
    }

    // Memory cleanup
    destroy() {
        if (this.startTimePicker) this.startTimePicker.destroy();
        if (this.endTimePicker) this.endTimePicker.destroy();

        this.selectionData.individualRanks.clear();
        this.selectionData.orGroups.clear();
        this.selectionData.fixedSoldiers.clear();

        this.#debounceTimers.clear();
        this.#initialized = false;

        console.log('DutyForm destroyed and resources cleaned up');
    }
}

// Initialize with error handling
document.addEventListener('DOMContentLoaded', function () {
    try {
        // Instantiate the duty form
        window.dutyForm = new DutyForm();

        // Global error handler for duty form
        window.addEventListener('error', function (e) {
            if (e && e.message && e.message.includes('dutyForm')) {
                console.error('DutyForm error:', e.error || e);
                // Fallback: reinitialize if possible after a short delay
                setTimeout(() => {
                    if (window.dutyForm && !window.dutyForm.isInitialized()) {
                        window.dutyForm.init();
                    } else if (!window.dutyForm) {
                        window.dutyForm = new DutyForm();
                    }
                }, 2000);
            }
        });
    } catch (error) {
        console.error('Failed to initialize DutyForm:', error);
    }
});

// Global functions for inline event handlers (backward compatibility)
function selectSoldier(soldierId) {
    // This function might still be called from HTML onclick attributes
    // Try to find the soldier in the SoldierLoader's current list
    if (window.soldierLoader && window.soldierLoader.currentSoldiers) {
        const soldier = window.soldierLoader.currentSoldiers.find(s => s.id == soldierId);
        if (soldier && window.dutyForm) {
            window.dutyForm.selectSoldier(soldier);
        }
    } else if (window.dutyForm) {
        // Fallback to the old method if SoldierLoader isn't available
        window.dutyForm.selectSoldier(soldierId);
    }
}

function closeSoldierModal() {
    if (window.dutyForm) {
        window.dutyForm.closeSoldierModal();
    }
}
