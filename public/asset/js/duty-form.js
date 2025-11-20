// duty-form.js - Comprehensive JavaScript for Duty Management Forms

class DutyForm {
    constructor() {
        console.log('DutyForm constructor called');
        this.initializeElements();
        this.initializeEventListeners();
        this.initializeData();
        this.initializeTimePickers();
        this.renderInitialData();
        this.updateRankButtonStates();
    }

    initializeElements() {
        // Form elements
        this.dutyForm = document.getElementById('duty-form');
        this.startTimeEl = document.getElementById('start-time');
        this.endTimeEl = document.getElementById('end-time');
        this.durationDaysEl = document.getElementById('duration-days');
        this.totalManpowerInput = document.getElementById('total-manpower');
        this.totalManpowerDisplay = document.getElementById('total-manpower-display');

        // Rank selection elements
        this.rankSearch = document.getElementById('rank-search');
        this.rankButtons = document.querySelectorAll('.rank-button');
        this.selectedItemsContainer = document.getElementById('selected-items-container');
        this.addOrGroupBtn = document.getElementById('add-or-group');

        // Fixed soldier elements
        this.addFixedSoldierBtn = document.getElementById('add-fixed-soldier');
        this.fixedSoldiersContainer = document.getElementById('fixed-soldiers-container');
        this.soldierSelectionModal = document.getElementById('soldier-selection-modal');
        this.soldierSearch = document.getElementById('soldier-search');
        this.soldierOptions = document.getElementById('soldier-options');

        // Duration display elements
        this.durationDisplay = document.getElementById('duration-display');
        this.scheduleDisplay = document.getElementById('schedule-display');
        this.multiDayIndicator = document.getElementById('multi-day-indicator');
        this.dailyDuration = document.getElementById('daily-duration');
        this.totalDuration = document.getElementById('total-duration');
        // Time picker instances
        this.startTimePicker = null;
        this.endTimePicker = null;
        console.log('Elements initialized');
    }
    initializeElements() {
        // Form elements
        this.dutyForm = document.getElementById('duty-form');
        this.startTimeEl = document.getElementById('start-time');
        this.endTimeEl = document.getElementById('end-time');
        this.durationDaysEl = document.getElementById('duration-days');
        this.totalManpowerInput = document.getElementById('total-manpower');
        this.totalManpowerDisplay = document.getElementById('total-manpower-display');

        // Rank selection elements
        this.rankSearch = document.getElementById('rank-search');
        this.rankButtons = document.querySelectorAll('.rank-button');
        this.selectedItemsContainer = document.getElementById('selected-items-container');
        this.addOrGroupBtn = document.getElementById('add-or-group');

        // Fixed soldier elements
        this.addFixedSoldierBtn = document.getElementById('add-fixed-soldier');
        this.fixedSoldiersContainer = document.getElementById('fixed-soldiers-container');
        this.soldierSelectionModal = document.getElementById('soldier-selection-modal');
        this.soldierSearch = document.getElementById('soldier-search');
        this.soldierOptions = document.getElementById('soldier-options');

        // Duration display elements
        this.durationDisplay = document.getElementById('duration-display');
        this.scheduleDisplay = document.getElementById('schedule-display');
        this.multiDayIndicator = document.getElementById('multi-day-indicator');
        this.dailyDuration = document.getElementById('daily-duration');
        this.totalDuration = document.getElementById('total-duration');

        // Time picker instances
        this.startTimePicker = null;
        this.endTimePicker = null;

        console.log('Elements initialized');
    }

    initializeTimePickers() {
        console.log('Initializing Flatpickr time pickers...');

        // Check if Flatpickr is available
        if (typeof flatpickr === 'undefined') {
            console.error('Flatpickr is not loaded. Please include Flatpickr CSS and JS files.');
            this.fallbackTimeInputs();
            return;
        }

        // Initialize start time picker
        if (this.startTimeEl) {
            this.startTimePicker = flatpickr(this.startTimeEl, {
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
                    this.styleTimePicker(instance);
                },
                onChange: (selectedDates, dateStr, instance) => {
                    console.log('Start time changed:', dateStr);
                    this.validateTimeFormat(this.startTimeEl);
                    this.validateTimeRange();
                    this.calculateAndDisplayDuration();
                },
                onClose: (selectedDates, dateStr, instance) => {
                    this.validateTimeFormat(this.startTimeEl);
                }
            });

            // Add custom class for styling
            this.startTimeEl.classList.add('flatpickr-input');
        }

        // Initialize end time picker
        if (this.endTimeEl) {
            this.endTimePicker = flatpickr(this.endTimeEl, {
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
                    this.styleTimePicker(instance);
                },
                onChange: (selectedDates, dateStr, instance) => {
                    console.log('End time changed:', dateStr);
                    this.validateTimeFormat(this.endTimeEl);
                    this.validateTimeRange();
                    this.calculateAndDisplayDuration();
                },
                onClose: (selectedDates, dateStr, instance) => {
                    this.validateTimeFormat(this.endTimeEl);
                }
            });

            // Add custom class for styling
            this.endTimeEl.classList.add('flatpickr-input');
        }

        console.log('Flatpickr time pickers initialized');
    }
    // Style the Flatpickr instance to match your theme
    styleTimePicker(instance) {
        // Wait for the calendar to be created
        setTimeout(() => {
            const calendar = instance.calendarContainer;
            if (calendar) {
                calendar.classList.add('custom-flatpickr');

                // Style the time container
                const timeContainer = calendar.querySelector('.flatpickr-time');
                if (timeContainer) {
                    timeContainer.classList.add('bg-white', 'rounded-lg', 'shadow-lg');
                }

                // Style the buttons
                const buttons = calendar.querySelectorAll('.flatpickr-time .numInputWrapper');
                buttons.forEach(btn => {
                    btn.classList.add('custom-time-input');
                });
            }
        }, 100);
    }

    // Fallback to basic time inputs if Flatpickr fails
    fallbackTimeInputs() {
        console.log('Using fallback time inputs');

        const timeInputs = document.querySelectorAll('.time-input');
        timeInputs.forEach(input => {
            input.type = 'text';
            input.placeholder = 'HH:MM (24-hour)';

            // Add basic formatting
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + ':' + value.substring(2, 4);
                }
                e.target.value = value.substring(0, 5);
            });

            input.addEventListener('blur', (e) => {
                this.validateTimeFormat(e.target);
                this.calculateAndDisplayDuration();
            });
        });
    }

    validateTimeFormat(input) {
        const timeRegex = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
        const value = input.value.trim();

        if (value && !timeRegex.test(value)) {
            input.classList.add('border-rose-500', 'focus:border-rose-500', 'focus:ring-rose-500/20');
            input.classList.remove('border-gray-200', 'focus:border-blue-500', 'focus:ring-blue-500/20');

            this.showTimeError(input, 'Please enter time in HH:MM format (00:00 to 23:59)');
            return false;
        } else {
            input.classList.remove('border-rose-500', 'focus:border-rose-500', 'focus:ring-rose-500/20');
            input.classList.add('border-gray-200', 'focus:border-blue-500', 'focus:ring-blue-500/20');

            this.removeTimeError(input);
            return true;
        }
    }

    validateTimeRange() {
        const startTime = this.startTimeEl ? this.startTimeEl.value : '';
        const endTime = this.endTimeEl ? this.endTimeEl.value : '';

        if (!startTime || !endTime) return;

        const start = new Date(`2000-01-01T${startTime}`);
        const end = new Date(`2000-01-01T${endTime}`);

        // Remove previous warnings
        this.removeTimeRangeWarning();

        // Check for 24-hour duty (same start and end time)
        if (startTime === endTime) {
            this.showTimeRangeWarning('24-hour duty selected');
            return;
        }

        // Check if end time is before start time (overnight duty)
        if (end < start) {
            this.showTimeRangeWarning('Overnight duty selected');
            return;
        }

        // Check if duration is too short (less than 1 hour)
        const duration = this.calculateDailyDuration(startTime, endTime);
        if (duration < 1) {
            this.showTimeRangeWarning('Duty duration is less than 1 hour');
            return;
        }
    }

    showTimeRangeWarning(message) {
        this.removeTimeRangeWarning();

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

        // Insert after the duration days select
        const durationDaysContainer = this.durationDaysEl ? this.durationDaysEl.closest('div') : null;
        if (durationDaysContainer) {
            durationDaysContainer.parentNode.insertBefore(warningEl, durationDaysContainer.nextSibling);
        }
    }

    removeTimeRangeWarning() {
        const existingWarning = document.getElementById('time-range-warning');
        if (existingWarning) {
            existingWarning.remove();
        }
    }

    showTimeError(input, message) {
        this.removeTimeError(input);

        const errorEl = document.createElement('p');
        errorEl.className = 'text-rose-500 text-xs mt-1 time-error';
        errorEl.textContent = message;

        input.parentNode.appendChild(errorEl);
    }

    removeTimeError(input) {
        const existingError = input.parentNode.querySelector('.time-error');
        if (existingError) {
            existingError.remove();
        }
    }

    // Add quick time selection buttons
    addQuickTimeButtons() {
        const timeContainers = document.querySelectorAll('.time-input-container');

        timeContainers.forEach(container => {
            const input = container.querySelector('.time-input');
            const isStartTime = input.id === 'start-time';

            const quickSelect = document.createElement('div');
            quickSelect.className = 'flex flex-wrap gap-1 mt-2';
            quickSelect.innerHTML = `
                <span class="text-xs text-gray-500 mr-2">Quick select:</span>
                ${isStartTime ?
                    '<button type="button" class="quick-time-btn px-2 py-1 text-xs bg-gray-100 hover:bg-blue-100 hover:text-blue-700 rounded transition-colors" data-time="06:00">06:00</button>' +
                    '<button type="button" class="quick-time-btn px-2 py-1 text-xs bg-gray-100 hover:bg-blue-100 hover:text-blue-700 rounded transition-colors" data-time="08:00">08:00</button>' +
                    '<button type="button" class="quick-time-btn px-2 py-1 text-xs bg-gray-100 hover:bg-blue-100 hover:text-blue-700 rounded transition-colors" data-time="09:00">09:00</button>'
                    :
                    '<button type="button" class="quick-time-btn px-2 py-1 text-xs bg-gray-100 hover:bg-blue-100 hover:text-blue-700 rounded transition-colors" data-time="16:00">16:00</button>' +
                    '<button type="button" class="quick-time-btn px-2 py-1 text-xs bg-gray-100 hover:bg-blue-100 hover:text-blue-700 rounded transition-colors" data-time="17:00">17:00</button>' +
                    '<button type="button" class="quick-time-btn px-2 py-1 text-xs bg-gray-100 hover:bg-blue-100 hover:text-blue-700 rounded transition-colors" data-time="18:00">18:00</button>'
                }
            `;

            container.appendChild(quickSelect);

            // Add event listeners to quick select buttons
            quickSelect.querySelectorAll('.quick-time-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const time = btn.getAttribute('data-time');
                    input.value = time;

                    // Update Flatpickr if active
                    if (isStartTime && this.startTimePicker) {
                        this.startTimePicker.setDate(time, true);
                    } else if (!isStartTime && this.endTimePicker) {
                        this.endTimePicker.setDate(time, true);
                    }

                    this.validateTimeFormat(input);
                    this.validateTimeRange();
                    this.calculateAndDisplayDuration();
                });
            });
        });
    }

    // Update your existing calculateAndDisplayDuration method to handle Flatpickr
    calculateAndDisplayDuration() {
        const startTime = this.startTimeEl ? this.startTimeEl.value : '';
        const endTime = this.endTimeEl ? this.endTimeEl.value : '';
        const durationDays = this.durationDaysEl ? parseInt(this.durationDaysEl.value) || 1 : 1;

        if (!startTime || !endTime) {
            if (this.durationDisplay) this.durationDisplay.classList.add('hidden');
            return;
        }

        // Calculate daily duration
        const dailyDurationHours = this.calculateDailyDuration(startTime, endTime);
        const totalDurationHours = dailyDurationHours * durationDays;

        // Update display
        let scheduleText = `${startTime} - ${endTime}`;
        let isOvernight = this.isOvernightDuty(startTime, endTime);

        if (isOvernight) {
            scheduleText += ' (overnight)';
        }
        if (durationDays > 1) {
            scheduleText += ` for ${durationDays} days`;
        }

        if (this.scheduleDisplay) this.scheduleDisplay.textContent = scheduleText;
        if (this.dailyDuration) this.dailyDuration.textContent = `${dailyDurationHours.toFixed(1)} hours`;
        if (this.totalDuration) this.totalDuration.textContent = `${totalDurationHours.toFixed(1)} hours`;

        // Show/hide multi-day indicator
        if (this.multiDayIndicator) {
            if (durationDays > 1) {
                this.multiDayIndicator.classList.remove('hidden');
                this.durationDisplay.classList.add('border-green-200', 'bg-green-50');
                this.durationDisplay.classList.remove('border-blue-200', 'bg-blue-50', 'border-purple-200', 'bg-purple-50');
            } else if (isOvernight) {
                this.multiDayIndicator.classList.add('hidden');
                this.durationDisplay.classList.add('border-purple-200', 'bg-purple-50');
                this.durationDisplay.classList.remove('border-blue-200', 'bg-blue-50', 'border-green-200', 'bg-green-50');
            } else {
                this.multiDayIndicator.classList.add('hidden');
                this.durationDisplay.classList.add('border-blue-200', 'bg-blue-50');
                this.durationDisplay.classList.remove('border-purple-200', 'bg-purple-50', 'border-green-200', 'bg-green-50');
            }
        }

        if (this.durationDisplay) this.durationDisplay.classList.remove('hidden');

        // Validation warnings
        if (this.durationDisplay) {
            if (dailyDurationHours < 1 && startTime !== endTime) {
                this.durationDisplay.classList.add('border-rose-200', 'bg-rose-50');
            } else if (totalDurationHours > 720) {
                this.durationDisplay.classList.add('border-amber-200', 'bg-amber-50');
            } else {
                this.durationDisplay.classList.remove('border-rose-200', 'bg-rose-50', 'border-amber-200', 'bg-amber-50');
            }
        }
    }
    initializeData() {
        // Data structures
        this.selectionData = {
            individualRanks: window.initialIndividualRanks || {},
            orGroups: window.initialRankGroups || [],
            fixedSoldiers: window.initialFixedSoldiers || {}
        };

        this.groupCounter = this.selectionData.orGroups.length;
        this.availableSoldiers = window.availableSoldiers || [];

        console.log('Data initialized:', {
            individualRanks: Object.keys(this.selectionData.individualRanks).length,
            orGroups: this.selectionData.orGroups.length,
            fixedSoldiers: Object.keys(this.selectionData.fixedSoldiers).length,
            availableSoldiers: this.availableSoldiers.length
        });
    }

    initializeEventListeners() {
        // Time and duration listeners
        if (this.startTimeEl) {
            this.startTimeEl.addEventListener('change', () => {
                this.validateTimeFormat(this.startTimeEl);
                this.calculateAndDisplayDuration();
            });
        }
        if (this.endTimeEl) {
            this.endTimeEl.addEventListener('change', () => {
                this.validateTimeFormat(this.endTimeEl);
                this.calculateAndDisplayDuration();
            });
        }
        if (this.durationDaysEl) {
            this.durationDaysEl.addEventListener('change', () => this.calculateAndDisplayDuration());
        }
        // Rank search and selection
        if (this.rankSearch) this.rankSearch.addEventListener('input', (e) => this.filterRanks(e.target.value));
        if (this.addOrGroupBtn) this.addOrGroupBtn.addEventListener('click', () => this.addOrGroup());

        // Rank button listeners
        this.rankButtons.forEach(btn => {
            btn.addEventListener('click', () => this.addIndividualRank(btn));
        });

        // Fixed soldier listeners
        if (this.addFixedSoldierBtn) {
            this.addFixedSoldierBtn.addEventListener('click', () => this.openSoldierSelectionModal());
        }

        if (this.soldierSearch) {
            this.soldierSearch.addEventListener('input', (e) => this.filterSoldiers(e.target.value));
        }

        // Form submission
        if (this.dutyForm) this.dutyForm.addEventListener('submit', (e) => this.handleFormSubmission(e));

        // Modal close handlers
        if (this.soldierSelectionModal) {
            this.soldierSelectionModal.addEventListener('click', (e) => {
                if (e.target === this.soldierSelectionModal) this.closeSoldierModal();
            });
        }

        console.log('Event listeners initialized');
    }

    renderInitialData() {
        console.log('Rendering initial data...');
        this.renderSelectedItems();
        this.renderFixedSoldiers();
        this.calculateAndDisplayDuration();
        this.updateTotalManpower();
        this.updateRankButtonStates();
        console.log('Initial data rendered');
    }

    updateRankButtonStates() {
        this.rankButtons.forEach(btn => {
            const rankId = btn.dataset.rankId;

            // Check if rank is in individual selections
            if (this.selectionData.individualRanks[rankId]) {
                btn.disabled = true;
                btn.classList.add('border-blue-500', 'bg-blue-100', 'text-blue-700');
            }
            // Check if rank is in any OR group
            else if (this.selectionData.orGroups.some(group => group.ranks.includes(rankId))) {
                btn.disabled = true;
                btn.classList.add('border-purple-500', 'bg-purple-100', 'text-purple-700');
            }
            // Enable button if not selected
            else {
                btn.disabled = false;
                btn.classList.remove('border-blue-500', 'bg-blue-100', 'text-blue-700',
                    'border-purple-500', 'bg-purple-100', 'text-purple-700');
            }
        });
    }

    // Rank Management Methods
    filterRanks(searchTerm) {
        const term = searchTerm.toLowerCase();
        this.rankButtons.forEach(btn => {
            const rankName = btn.dataset.rankName.toLowerCase();
            if (rankName.includes(term)) {
                btn.style.display = 'block';
            } else {
                btn.style.display = 'none';
            }
        });
    }

    addIndividualRank(button) {
        const rankId = button.dataset.rankId;
        const rankName = button.dataset.rankName;

        // Check if already selected
        if (this.selectionData.individualRanks[rankId]) {
            return;
        }

        // Check if in any OR group
        const isInGroup = this.selectionData.orGroups.some(group =>
            group.ranks.includes(rankId)
        );

        if (isInGroup) {
            alert('This rank is already in an OR group.');
            return;
        }

        // Add to selection
        this.selectionData.individualRanks[rankId] = {
            id: rankId,
            name: rankName,
            manpower: 1
        };

        // Disable button
        button.disabled = true;
        button.classList.add('border-blue-500', 'bg-blue-100', 'text-blue-700');

        this.renderSelectedItems();
        this.updateTotalManpower();
    }

    addOrGroup() {
        this.groupCounter++;
        const groupId = 'group_' + this.groupCounter;

        this.selectionData.orGroups.push({
            id: groupId,
            ranks: [],
            manpower: 1
        });

        this.renderSelectedItems();
    }

    renderSelectedItems() {
        if (!this.selectedItemsContainer) return;

        this.selectedItemsContainer.innerHTML = '';

        const hasItems = Object.keys(this.selectionData.individualRanks).length > 0 ||
            this.selectionData.orGroups.length > 0;

        if (!hasItems) {
            const emptyState = document.createElement('div');
            emptyState.id = 'empty-state';
            emptyState.className = 'text-center py-8 border-2 border-dashed border-gray-200 rounded-xl bg-gradient-to-r from-gray-50 to-blue-50/30';
            emptyState.innerHTML = `
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-sm text-gray-500">No ranks selected yet</p>
                <p class="text-xs text-gray-400 mt-1">Click on ranks above or create OR groups to get started</p>
            `;
            this.selectedItemsContainer.appendChild(emptyState);
            return;
        }

        // Render individual ranks
        Object.values(this.selectionData.individualRanks).forEach(rank => {
            const rankCard = this.createRankCard(rank);
            this.selectedItemsContainer.appendChild(rankCard);
        });

        // Render OR groups
        this.selectionData.orGroups.forEach(group => {
            const groupCard = this.createGroupCard(group);
            this.selectedItemsContainer.appendChild(groupCard);
        });
    }

    createRankCard(rank) {
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
                        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50" onclick="window.dutyForm.decreaseRankManpower('${rank.id}')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <input type="number" min="1" value="${rank.manpower}"
                            class="w-16 h-8 text-center border-t border-b border-gray-300 focus:outline-none"
                            onchange="window.dutyForm.updateRankManpower('${rank.id}', this.value)">
                        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50" onclick="window.dutyForm.increaseRankManpower('${rank.id}')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-red-500 transition-colors" onclick="window.dutyForm.removeRank('${rank.id}')">
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

    createGroupCard(group) {
        const card = document.createElement('div');
        card.className = 'bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-200';

        const rankNames = group.ranks.map(rankId => {
            const btn = document.querySelector(`[data-rank-id="${rankId}"]`);
            return btn ? btn.dataset.rankName : 'Unknown';
        }).join(' OR ');

        card.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 bg-purple-500 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">OR Group</h4>
                        <p class="text-xs text-gray-600">Any of these ranks can fulfill</p>
                    </div>
                </div>
                <button type="button" class="text-gray-400 hover:text-red-500 transition-colors" onclick="window.dutyForm.removeGroup('${group.id}')">
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
                        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50" onclick="window.dutyForm.decreaseGroupManpower('${group.id}')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </button>
                        <input type="number" min="1" value="${group.manpower}"
                            class="w-16 h-8 text-center border-t border-b border-gray-300 focus:outline-none"
                            onchange="window.dutyForm.updateGroupManpower('${group.id}', this.value)">
                        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50" onclick="window.dutyForm.increaseGroupManpower('${group.id}')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="button" class="px-3 py-1.5 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-200 transition-colors" onclick="window.dutyForm.openGroupRankSelector('${group.id}')">
                    ${group.ranks.length > 0 ? 'Edit Ranks' : 'Add Ranks'}
                </button>
            </div>

            <input type="hidden" name="rank_groups[${group.id}][manpower]" value="${group.manpower}" id="group-manpower-${group.id}">
            ${group.ranks.map(rankId => `<input type="hidden" name="rank_groups[${group.id}][ranks][]" value="${rankId}">`).join('')}
        `;
        return card;
    }

    // Rank Manpower Methods
    decreaseRankManpower(rankId) {
        const rank = this.selectionData.individualRanks[rankId];
        if (rank && rank.manpower > 1) {
            rank.manpower--;
            const input = document.getElementById(`rank-manpower-${rankId}`);
            if (input) input.value = rank.manpower;
            this.renderSelectedItems();
            this.updateTotalManpower();
        }
    }

    increaseRankManpower(rankId) {
        const rank = this.selectionData.individualRanks[rankId];
        if (rank) {
            rank.manpower++;
            const input = document.getElementById(`rank-manpower-${rankId}`);
            if (input) input.value = rank.manpower;
            this.renderSelectedItems();
            this.updateTotalManpower();
        }
    }

    updateRankManpower(rankId, value) {
        const rank = this.selectionData.individualRanks[rankId];
        if (rank) {
            const newValue = Math.max(1, parseInt(value) || 1);
            rank.manpower = newValue;
            const input = document.getElementById(`rank-manpower-${rankId}`);
            if (input) input.value = rank.manpower;
            this.renderSelectedItems();
            this.updateTotalManpower();
        }
    }

    removeRank(rankId) {
        delete this.selectionData.individualRanks[rankId];

        // Re-enable button
        const btn = document.querySelector(`[data-rank-id="${rankId}"]`);
        if (btn) {
            btn.disabled = false;
            btn.classList.remove('border-blue-500', 'bg-blue-100', 'text-blue-700');
        }

        this.renderSelectedItems();
        this.updateTotalManpower();
    }

    // Group Manpower Methods
    decreaseGroupManpower(groupId) {
        const group = this.selectionData.orGroups.find(g => g.id === groupId);
        if (group && group.manpower > 1) {
            group.manpower--;
            const input = document.getElementById(`group-manpower-${groupId}`);
            if (input) input.value = group.manpower;
            this.renderSelectedItems();
            this.updateTotalManpower();
        }
    }

    increaseGroupManpower(groupId) {
        const group = this.selectionData.orGroups.find(g => g.id === groupId);
        if (group) {
            group.manpower++;
            const input = document.getElementById(`group-manpower-${groupId}`);
            if (input) input.value = group.manpower;
            this.renderSelectedItems();
            this.updateTotalManpower();
        }
    }

    updateGroupManpower(groupId, value) {
        const group = this.selectionData.orGroups.find(g => g.id === groupId);
        if (group) {
            const newValue = Math.max(1, parseInt(value) || 1);
            group.manpower = newValue;
            const input = document.getElementById(`group-manpower-${groupId}`);
            if (input) input.value = group.manpower;
            this.renderSelectedItems();
            this.updateTotalManpower();
        }
    }

    removeGroup(groupId) {
        const group = this.selectionData.orGroups.find(g => g.id === groupId);

        // Re-enable all buttons in this group
        if (group) {
            group.ranks.forEach(rankId => {
                const btn = document.querySelector(`[data-rank-id="${rankId}"]`);
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('border-purple-500', 'bg-purple-100', 'text-purple-700');
                }
            });
        }

        this.selectionData.orGroups = this.selectionData.orGroups.filter(g => g.id !== groupId);
        this.renderSelectedItems();
        this.updateTotalManpower();
    }

    // Group Rank Selection Modal
    openGroupRankSelector(groupId) {
        const group = this.selectionData.orGroups.find(g => g.id === groupId);
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
                        ${Array.from(this.rankButtons).map(btn => {
            const rankId = btn.dataset.rankId;
            const rankName = btn.dataset.rankName;
            const isSelected = group.ranks.includes(rankId);
            const isDisabled = this.selectionData.individualRanks[rankId] ||
                (this.selectionData.orGroups.some(g => g.id !== groupId && g.ranks.includes(rankId)));

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
                        <span class="font-semibold" id="selected-count">${group.ranks.length}</span> rank(s) selected
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

        let tempSelectedRanks = [...group.ranks];

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

                if (tempSelectedRanks.includes(rankId)) {
                    tempSelectedRanks = tempSelectedRanks.filter(id => id !== rankId);
                    this.classList.remove('border-purple-500', 'bg-purple-100', 'text-purple-700');
                    this.classList.add('border-gray-200', 'bg-white', 'text-gray-700');
                    this.innerHTML = this.dataset.rankName;
                } else {
                    tempSelectedRanks.push(rankId);
                    this.classList.add('border-purple-500', 'bg-purple-100', 'text-purple-700');
                    this.classList.remove('border-gray-200', 'bg-white', 'text-gray-700');
                    this.innerHTML = this.dataset.rankName +
                        ' <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                }

                selectedCountEl.textContent = tempSelectedRanks.length;
            });
        });

        // Confirm
        confirmBtn.addEventListener('click', () => {
            // Re-enable previously selected ranks
            group.ranks.forEach(rankId => {
                const btn = document.querySelector(`[data-rank-id="${rankId}"]`);
                if (btn && !tempSelectedRanks.includes(rankId)) {
                    btn.disabled = false;
                    btn.classList.remove('border-purple-500', 'bg-purple-100', 'text-purple-700');
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
            this.renderSelectedItems();
            this.updateTotalManpower();
            modal.remove();
        });

        // Close on backdrop click
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    // Fixed Soldier Methods
    openSoldierSelectionModal() {
        if (this.soldierSelectionModal) {
            this.soldierSelectionModal.classList.remove('hidden');
            this.loadAvailableSoldiers();
        }
    }

    closeSoldierModal() {
        if (this.soldierSelectionModal) {
            this.soldierSelectionModal.classList.add('hidden');
            if (this.soldierSearch) {
                this.soldierSearch.value = '';
            }
            this.filterSoldiers('');
        }
    }

    filterSoldiers(searchTerm) {
        const term = searchTerm.toLowerCase();
        const soldierOptions = this.soldierOptions ? this.soldierOptions.querySelectorAll('.soldier-option') : [];

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

        // Update filtered count
        const filteredCountEl = document.getElementById('filtered-soldier-count');
        const soldierCountEl = document.getElementById('soldier-count');

        if (filteredCountEl) {
            filteredCountEl.textContent = visibleCount;
        }
        if (soldierCountEl && visibleCount === 0 && term === '') {
            soldierCountEl.textContent = visibleCount;
        }
    }

    loadAvailableSoldiers() {
        // Soldiers are already loaded in the initial data
        console.log('Available soldiers loaded:', this.availableSoldiers.length);
    }

    selectSoldier(soldierId) {
        const soldier = this.availableSoldiers.find(s => s.id == soldierId);
        if (soldier) {
            this.addFixedSoldier(soldier);
            this.closeSoldierModal();
        }
    }

    addFixedSoldier(soldier) {
        const soldierId = soldier.id;

        // Check if soldier is already added
        if (this.selectionData.fixedSoldiers[soldierId]) {
            alert('This soldier is already assigned to this duty.');
            return;
        }

        // Add to data structure
        this.selectionData.fixedSoldiers[soldierId] = {
            id: soldierId,
            soldier: soldier,
            priority: 1,
            remarks: ''
        };

        this.renderFixedSoldiers();
    }

    renderFixedSoldiers() {
        if (!this.fixedSoldiersContainer) return;

        console.log('Rendering fixed soldiers:', Object.keys(this.selectionData.fixedSoldiers).length);

        this.fixedSoldiersContainer.innerHTML = '';

        if (Object.keys(this.selectionData.fixedSoldiers).length === 0) {
            this.fixedSoldiersContainer.innerHTML = `
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

        Object.values(this.selectionData.fixedSoldiers).forEach(soldierData => {
            const soldierCard = this.createFixedSoldierCard(soldierData);
            this.fixedSoldiersContainer.appendChild(soldierCard);
        });
    }

    createFixedSoldierCard(soldierData) {
        const card = document.createElement('div');
        card.className = 'bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-200';
        card.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 bg-green-500 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800">${soldierData.soldier.full_name}</h4>
                        <p class="text-xs text-gray-600">
                            ${soldierData.soldier.army_no} • ${soldierData.soldier.rank} • ${soldierData.soldier.company}
                        </p>
                    </div>
                </div>
                <button type="button" class="text-gray-400 hover:text-red-500 transition-colors" onclick="window.dutyForm.removeFixedSoldier(${soldierData.id})">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Priority</label>
                    <input type="number" min="1" max="10" value="${soldierData.priority}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-green-500"
                        onchange="window.dutyForm.updateFixedSoldierPriority(${soldierData.id}, this.value)">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Remarks</label>
                    <input type="text" value="${soldierData.remarks}" placeholder="Optional remarks"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-green-500"
                        onchange="window.dutyForm.updateFixedSoldierRemarks(${soldierData.id}, this.value)">
                </div>
            </div>

            <!-- Hidden inputs for form submission -->
            <input type="hidden" name="fixed_soldiers[${soldierData.id}][soldier_id]" value="${soldierData.id}">
            <input type="hidden" name="fixed_soldiers[${soldierData.id}][priority]" value="${soldierData.priority}" id="fixed-priority-${soldierData.id}">
            <input type="hidden" name="fixed_soldiers[${soldierData.id}][remarks]" value="${soldierData.remarks}" id="fixed-remarks-${soldierData.id}">
        `;
        return card;
    }

    removeFixedSoldier(soldierId) {
        delete this.selectionData.fixedSoldiers[soldierId];
        this.renderFixedSoldiers();
    }

    updateFixedSoldierPriority(soldierId, priority) {
        if (this.selectionData.fixedSoldiers[soldierId]) {
            this.selectionData.fixedSoldiers[soldierId].priority = Math.max(1, Math.min(10, parseInt(priority) || 1));
            const input = document.getElementById(`fixed-priority-${soldierId}`);
            if (input) input.value = this.selectionData.fixedSoldiers[soldierId].priority;
        }
    }

    updateFixedSoldierRemarks(soldierId, remarks) {
        if (this.selectionData.fixedSoldiers[soldierId]) {
            this.selectionData.fixedSoldiers[soldierId].remarks = remarks;
            const input = document.getElementById(`fixed-remarks-${soldierId}`);
            if (input) input.value = remarks;
        }
    }

    // Duration Calculation Methods
    calculateAndDisplayDuration() {
        const startTime = this.startTimeEl ? this.startTimeEl.value : '';
        const endTime = this.endTimeEl ? this.endTimeEl.value : '';
        const durationDays = this.durationDaysEl ? parseInt(this.durationDaysEl.value) || 1 : 1;

        if (!startTime || !endTime) {
            if (this.durationDisplay) this.durationDisplay.classList.add('hidden');
            return;
        }

        // Calculate daily duration
        const dailyDurationHours = this.calculateDailyDuration(startTime, endTime);
        const totalDurationHours = dailyDurationHours * durationDays;

        // Update display
        let scheduleText = `${startTime} - ${endTime}`;
        let isOvernight = this.isOvernightDuty(startTime, endTime);

        if (isOvernight) {
            scheduleText += ' (overnight)';
        }
        if (durationDays > 1) {
            scheduleText += ` for ${durationDays} days`;
        }

        if (this.scheduleDisplay) this.scheduleDisplay.textContent = scheduleText;
        if (this.dailyDuration) this.dailyDuration.textContent = `${dailyDurationHours.toFixed(1)} hours`;
        if (this.totalDuration) this.totalDuration.textContent = `${totalDurationHours.toFixed(1)} hours`;

        // Show/hide multi-day indicator
        if (this.multiDayIndicator) {
            if (durationDays > 1) {
                this.multiDayIndicator.classList.remove('hidden');
                this.durationDisplay.classList.add('border-green-200', 'bg-green-50');
                this.durationDisplay.classList.remove('border-blue-200', 'bg-blue-50', 'border-purple-200', 'bg-purple-50');
            } else if (isOvernight) {
                this.multiDayIndicator.classList.add('hidden');
                this.durationDisplay.classList.add('border-purple-200', 'bg-purple-50');
                this.durationDisplay.classList.remove('border-blue-200', 'bg-blue-50', 'border-green-200', 'bg-green-50');
            } else {
                this.multiDayIndicator.classList.add('hidden');
                this.durationDisplay.classList.add('border-blue-200', 'bg-blue-50');
                this.durationDisplay.classList.remove('border-purple-200', 'bg-purple-50', 'border-green-200', 'bg-green-50');
            }
        }

        if (this.durationDisplay) this.durationDisplay.classList.remove('hidden');

        // Validation warnings
        if (this.durationDisplay) {
            if (dailyDurationHours < 1) {
                this.durationDisplay.classList.add('border-rose-200', 'bg-rose-50');
            } else if (totalDurationHours > 720) {
                this.durationDisplay.classList.add('border-amber-200', 'bg-amber-50');
            } else {
                this.durationDisplay.classList.remove('border-rose-200', 'bg-rose-50', 'border-amber-200', 'bg-amber-50');
            }
        }
    }

    calculateDailyDuration(startTime, endTime) {
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

    isOvernightDuty(startTime, endTime) {
        const [startHour, startMinute] = startTime.split(':').map(Number);
        const [endHour, endMinute] = endTime.split(':').map(Number);

        return endHour < startHour || (endHour === startHour && endMinute < startMinute);
    }

    // Manpower Calculation
    updateTotalManpower() {
        let total = 0;

        // Add individual ranks manpower
        Object.values(this.selectionData.individualRanks).forEach(rank => {
            total += rank.manpower;
        });

        // Add rank groups manpower (each group counts once)
        this.selectionData.orGroups.forEach(group => {
            total += group.manpower;
        });

        // Update display
        if (this.totalManpowerInput) this.totalManpowerInput.value = total;
        if (this.totalManpowerDisplay) this.totalManpowerDisplay.textContent = total;
    }

    // Form Submission
    handleFormSubmission(e) {
        // Clear previous errors
        // Clear previous errors
        document.querySelectorAll('.rank-error, .time-error, .time-range-error').forEach(el => el.remove());

        // Validate time formats
        const startTimeValid = this.startTimeEl ? this.validateTimeFormat(this.startTimeEl) : true;
        const endTimeValid = this.endTimeEl ? this.validateTimeFormat(this.endTimeEl) : true;

        if (!startTimeValid || !endTimeValid) {
            e.preventDefault();
            return;
        }

        // Validate time range
        const startTime = this.startTimeEl ? this.startTimeEl.value : '';
        const endTime = this.endTimeEl ? this.endTimeEl.value : '';

        if (startTime && endTime) {
            const dailyDuration = this.calculateDailyDuration(startTime, endTime);
            if (dailyDuration < 1 && startTime !== endTime) {
                e.preventDefault();
                this.showTimeError(this.endTimeEl, 'Duty duration must be at least 1 hour');
                return;
            }
        }

        const hasIndividualRanks = Object.keys(this.selectionData.individualRanks).length > 0;
        const hasValidGroups = this.selectionData.orGroups.some(group => group.ranks.length > 0);
        const hasFixedSoldiers = Object.keys(this.selectionData.fixedSoldiers).length > 0;

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
            this.selectedItemsContainer.parentElement.appendChild(errorEl);
            return;
        }

        // Check for empty groups
        const emptyGroups = this.selectionData.orGroups.filter(group => group.ranks.length === 0);
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
            this.selectedItemsContainer.parentElement.appendChild(errorEl);
            return;
        }



        // Show loading state
        const submitBtn = this.dutyForm ? this.dutyForm.querySelector('button[type="submit"]') : null;
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
    // Add this to your DutyForm class or as a separate function
    initializeTimeInputs() {
        const timeInputs = document.querySelectorAll('.time-input');

        timeInputs.forEach(input => {
            // Format input as user types
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');

                if (value.length >= 2) {
                    value = value.substring(0, 2) + ':' + value.substring(2, 4);
                }

                e.target.value = value.substring(0, 5);
            });

            // Validate on blur
            input.addEventListener('blur', (e) => {
                this.validateTimeFormat(e.target);
            });

            // Validate on form submission
            input.addEventListener('change', (e) => {
                this.validateTimeFormat(e.target);
                this.calculateAndDisplayDuration();
            });
        });
    }

    validateTimeFormat(input) {
        const timeRegex = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
        const value = input.value.trim();

        if (value && !timeRegex.test(value)) {
            input.classList.add('border-rose-500', 'focus:border-rose-500', 'focus:ring-rose-500/20');
            input.classList.remove('border-gray-200', 'focus:border-blue-500', 'focus:ring-blue-500/20');

            // Show error message
            this.showTimeError(input, 'Please enter time in HH:MM format (00:00 to 23:59)');
            return false;
        } else {
            input.classList.remove('border-rose-500', 'focus:border-rose-500', 'focus:ring-rose-500/20');
            input.classList.add('border-gray-200', 'focus:border-blue-500', 'focus:ring-blue-500/20');

            // Remove error message
            this.removeTimeError(input);
            return true;
        }
    }

    showTimeError(input, message) {
        // Remove existing error
        this.removeTimeError(input);

        const errorEl = document.createElement('p');
        errorEl.className = 'text-rose-500 text-xs mt-1 time-error';
        errorEl.textContent = message;

        input.parentNode.appendChild(errorEl);
    }

    removeTimeError(input) {
        const existingError = input.parentNode.querySelector('.time-error');
        if (existingError) {
            existingError.remove();
        }
    }
}

// Initialize the duty form when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM Content Loaded - Initializing DutyForm');
    window.dutyForm = new DutyForm();
});

// Global functions for inline event handlers
function selectSoldier(soldierId) {
    if (window.dutyForm) {
        window.dutyForm.selectSoldier(soldierId);
    }
}

function closeSoldierModal() {
    if (window.dutyForm) {
        window.dutyForm.closeSoldierModal();
    }
}
// Initialize the duty form when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM Content Loaded - Initializing DutyForm with Flatpickr');

    // Check if Flatpickr is loaded, if not load it dynamically
    if (typeof flatpickr === 'undefined') {
        console.warn('Flatpickr not found, loading dynamically...');
        loadFlatpickr().then(() => {
            window.dutyForm = new DutyForm();
        }).catch(() => {
            console.error('Failed to load Flatpickr, using fallback');
            window.dutyForm = new DutyForm();
        });
    } else {
        window.dutyForm = new DutyForm();
    }
});

// Dynamic loading of Flatpickr (optional)
function loadFlatpickr() {
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

// Global functions for inline event handlers
function selectSoldier(soldierId) {
    if (window.dutyForm) {
        window.dutyForm.selectSoldier(soldierId);
    }
}

function closeSoldierModal() {
    if (window.dutyForm) {
        window.dutyForm.closeSoldierModal();
    }
}
