@extends('mpm.layouts.app')

@section('title', 'Create Duty Record')

@section('content')

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8 px-4">
        <div class="container mx-auto max-w-2xl">
            <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />

            <!-- Header Section -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Duty Record</h1>
                <p class="text-gray-600">Fill in the details to create a new duty assignment</p>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-3xl border border-white/50 overflow-hidden">
                <div class="p-8">
                    {{-- The form now points to the 'duty.store' route using the POST method --}}
                    <form method="POST" action="{{ route('duty.store') }}" id="duty-form">
                        {{-- CSRF token for security --}}
                        @csrf

                        <div class="space-y-6">
                            {{-- Duty Name Input --}}
                            <div class="group">
                                <label for="duty-name" class="block text-sm font-semibold text-gray-700 mb-3">
                                    Duty Name <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="duty-name" name="duty_name" value="{{ old('duty_name') }}"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 @error('duty_name') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                        placeholder="Enter duty name" required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('duty_name')
                                    <p class="text-rose-500 text-sm mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Duty Time Inputs (Start and End) --}}
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    Duty Time <span class="text-rose-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Start Time -->
                                    <div>
                                        <label for="start-time"
                                            class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">Start
                                            Time</label>
                                        <div class="relative">
                                            <select id="start-time" name="start_time"
                                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 appearance-none @error('start_time') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                                required>
                                                <option value="">Select Start Time</option>
                                                @for ($h = 0; $h < 24; $h++)
                                                    @for ($m = 0; $m < 60; $m += 30)
                                                        @php
                                                            $time = sprintf('%02d:%02d', $h, $m);
                                                        @endphp
                                                        <option value="{{ $time }}"
                                                            {{ old('start_time', isset($duty) ? \Carbon\Carbon::parse($duty->start_time)->format('H:i') : '') == $time ? 'selected' : '' }}>
                                                            {{ $time }}
                                                        </option>
                                                    @endfor
                                                @endfor
                                            </select>
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                        @error('start_time')
                                            <p class="text-rose-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- End Time -->
                                    <div>
                                        <label for="end-time"
                                            class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">End
                                            Time</label>
                                        <div class="relative">
                                            <select id="end-time" name="end_time"
                                                class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 appearance-none @error('end_time') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                                required>
                                                <option value="">Select End Time</option>
                                                @for ($h = 0; $h < 24; $h++)
                                                    @for ($m = 0; $m < 60; $m += 30)
                                                        @php
                                                            $time = sprintf('%02d:%02d', $h, $m);
                                                        @endphp
                                                        <option value="{{ $time }}"
                                                            {{ old('end_time', isset($duty) ? \Carbon\Carbon::parse($duty->end_time)->format('H:i') : '') == $time ? 'selected' : '' }}>
                                                            {{ $time }}
                                                        </option>
                                                    @endfor
                                                @endfor
                                            </select>
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                        @error('end_time')
                                            <p class="text-rose-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Ranks with Manpower Section --}}
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    Soldier Ranks & Manpower <span class="text-rose-500">*</span>
                                </label>

                                <!-- Available Ranks Dropdown -->
                                <div class="relative mb-4" id="rank-manpower-container">
                                    <div class="relative">
                                        <button type="button" id="add-rank-trigger"
                                            class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-left text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20">
                                            <div class="flex items-center justify-between">
                                                <span id="add-rank-display" class="text-gray-400">Add a rank with
                                                    manpower</span>
                                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                                    id="add-rank-arrow" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </button>

                                        <!-- Dropdown options -->
                                        <div id="add-rank-dropdown"
                                            class="absolute top-full left-0 right-0 mt-2 bg-white border-2 border-gray-200 rounded-xl shadow-lg max-h-64 overflow-y-auto z-50 hidden">
                                            <div class="p-3">
                                                <div class="relative mb-3">
                                                    <input type="text" id="rank-search" placeholder="Search ranks..."
                                                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:border-blue-500">
                                                    <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                                                </div>
                                                <div class="space-y-1" id="rank-options">
                                                    @foreach ($ranks as $rank)
                                                        <div class="rank-option flex items-center p-3 hover:bg-blue-50 rounded-lg cursor-pointer transition-all duration-200 border border-transparent hover:border-blue-200"
                                                            data-value="{{ $rank->id }}"
                                                            data-text="{{ $rank->name }}">
                                                            <div class="flex items-center w-full">
                                                                <div
                                                                    class="flex items-center justify-center w-5 h-5 rounded border-2 border-gray-300 mr-3 transition-all duration-200 rank-checkbox">
                                                                    <svg class="w-3 h-3 text-blue-600 opacity-0 transition-opacity duration-200 rank-checkmark"
                                                                        fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="3"
                                                                            d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                </div>
                                                                <span
                                                                    class="text-sm text-gray-700 font-medium">{{ $rank->name }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selected Ranks with Manpower -->
                                <div id="selected-ranks-with-manpower" class="space-y-3 mt-4">
                                    <!-- Selected ranks will be added here dynamically -->
                                </div>

                                <p class="text-gray-500 text-sm mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Add ranks and specify the required manpower for each
                                </p>

                                @error('rank_manpower')
                                    <p class="text-rose-500 text-sm mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Remark Input --}}
                            <div class="group">
                                <label for="remark"
                                    class="block text-sm font-semibold text-gray-700 mb-3">Remark</label>
                                <div class="relative">
                                    <textarea id="remark" name="remark" rows="4"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 resize-none @error('remark') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                        placeholder="Enter any remarks or additional information...">{{ old('remark') }}</textarea>
                                    <div class="absolute top-3 right-3">
                                        <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('remark')
                                    <p class="text-rose-500 text-sm mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Status Select --}}
                            <div class="group">
                                <label for="status" class="block text-sm font-semibold text-gray-700 mb-3">
                                    Status <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="status" name="status"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 appearance-none @error('status') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                        required>
                                        <option value="Active" @if (old('status', 'Active') == 'Active') selected @endif>
                                            🟢 Active
                                        </option>
                                        <option value="Inactive" @if (old('status') == 'Inactive') selected @endif>
                                            🔴 Inactive
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                @error('status')
                                    <p class="text-rose-500 text-sm mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('duty.index') }}"
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:border-gray-400 hover:bg-gray-50 transition-all duration-300 no-underline group">
                                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to List
                            </a>
                            <button type="submit"
                                class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl text-white font-semibold hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-500/50 transition-all duration-300 shadow-lg hover:shadow-xl group">
                                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Save Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Create script loaded successfully');

            // Form elements
            const dutyForm = document.getElementById('duty-form');
            const startTimeEl = document.getElementById('start-time');
            const endTimeEl = document.getElementById('end-time');

            // Rank selection elements
            const addRankTrigger = document.getElementById('add-rank-trigger');
            const addRankDropdown = document.getElementById('add-rank-dropdown');
            const addRankDisplay = document.getElementById('add-rank-display');
            const addRankArrow = document.getElementById('add-rank-arrow');
            const rankSearch = document.getElementById('rank-search');
            const rankOptions = document.querySelectorAll('.rank-option');
            const selectedRanksContainer = document.getElementById('selected-ranks-with-manpower');

            // Track selected ranks with their manpower
            let selectedRanks = {};

            console.log('Elements found:', {
                dutyForm: !!dutyForm,
                addRankTrigger: !!addRankTrigger,
                addRankDropdown: !!addRankDropdown,
                rankOptions: rankOptions.length
            });

            // Check if we have any ranks
            if (rankOptions.length === 0) {
                addRankTrigger.disabled = true;
                addRankTrigger.classList.add('opacity-50', 'cursor-not-allowed');
                addRankDisplay.textContent = 'No ranks available';
                return;
            }

            let isDropdownOpen = false;

            // Toggle dropdown
            addRankTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Toggle dropdown clicked');
                isDropdownOpen = !isDropdownOpen;

                if (isDropdownOpen) {
                    addRankDropdown.classList.remove('hidden');
                    addRankArrow.style.transform = 'rotate(180deg)';
                    addRankTrigger.classList.add('border-blue-500', 'ring-4', 'ring-blue-500/20');
                    rankSearch.focus();
                } else {
                    addRankDropdown.classList.add('hidden');
                    addRankArrow.style.transform = 'rotate(0deg)';
                    addRankTrigger.classList.remove('border-blue-500', 'ring-4', 'ring-blue-500/20');
                    rankSearch.value = '';
                    filterRankOptions('');
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!document.getElementById('rank-manpower-container').contains(e.target)) {
                    if (isDropdownOpen) {
                        isDropdownOpen = false;
                        addRankDropdown.classList.add('hidden');
                        addRankArrow.style.transform = 'rotate(0deg)';
                        addRankTrigger.classList.remove('border-blue-500', 'ring-4', 'ring-blue-500/20');
                    }
                }
            });

            // Search functionality
            rankSearch.addEventListener('input', function() {
                filterRankOptions(this.value.toLowerCase());
            });

            function filterRankOptions(searchTerm) {
                rankOptions.forEach(option => {
                    const text = option.dataset.text.toLowerCase();
                    if (text.includes(searchTerm)) {
                        option.style.display = 'flex';
                    } else {
                        option.style.display = 'none';
                    }
                });
            }

            // Handle rank selection
            rankOptions.forEach(option => {
                const checkboxContainer = option.querySelector('.rank-checkbox');
                const checkmark = option.querySelector('.rank-checkmark');
                const rankId = option.dataset.value;
                const rankName = option.dataset.text;

                // Make the entire option clickable
                option.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Check if rank is already selected
                    if (selectedRanks[rankId]) {
                        // Rank already selected, show message or ignore
                        return;
                    }

                    // Add animation effect
                    option.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        option.style.transform = 'scale(1)';
                    }, 150);

                    // Add the rank with default manpower
                    selectedRanks[rankId] = {
                        id: rankId,
                        name: rankName,
                        manpower: 1
                    };

                    // Update visual state
                    checkboxContainer.classList.add('bg-blue-500', 'border-blue-500');
                    checkboxContainer.classList.remove('border-gray-300');
                    checkmark.classList.remove('opacity-0');
                    checkmark.classList.add('opacity-100');
                    option.classList.add('bg-blue-50', 'border-blue-200');

                    // Update the UI
                    updateSelectedRanksDisplay();

                    // Close dropdown
                    isDropdownOpen = false;
                    addRankDropdown.classList.add('hidden');
                    addRankArrow.style.transform = 'rotate(0deg)';
                    addRankTrigger.classList.remove('border-blue-500', 'ring-4',
                    'ring-blue-500/20');
                    rankSearch.value = '';
                    filterRankOptions('');
                });
            });

            function updateSelectedRanksDisplay() {
                selectedRanksContainer.innerHTML = '';

                if (Object.keys(selectedRanks).length === 0) {
                    // Show a helper message when nothing is selected
                    const helperMessage = document.createElement('div');
                    helperMessage.className =
                        'text-center py-4 text-gray-500 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200';
                    helperMessage.innerHTML = `
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <p class="text-sm">No ranks added yet. Click "Add a rank with manpower" to get started.</p>
                    `;
                    selectedRanksContainer.appendChild(helperMessage);
                    return;
                }

                // Create a card for each selected rank
                Object.values(selectedRanks).forEach(rank => {
                    const rankCard = document.createElement('div');
                    rankCard.className =
                        'bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100 shadow-sm';
                    rankCard.dataset.rankId = rank.id;

                    rankCard.innerHTML = `
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 bg-blue-500 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-medium text-gray-800">${rank.name}</h3>
                            </div>
                            <button type="button" class="text-gray-400 hover:text-red-500 transition-colors remove-rank" data-rank-id="${rank.id}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center">
                            <label class="text-sm text-gray-600 mr-3">Manpower:</label>
                            <div class="flex items-center">
                                <button type="button" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50 decrease-manpower" data-rank-id="${rank.id}">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <input type="number" min="1" value="${rank.manpower}" class="w-16 h-8 text-center border-t border-b border-gray-300 manpower-input" data-rank-id="${rank.id}">
                                <button type="button" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50 increase-manpower" data-rank-id="${rank.id}">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <!-- Hidden input for form submission -->
                        <input type="hidden" name="rank_manpower[${rank.id}][rank_id]" value="${rank.id}">
                        <input type="hidden" name="rank_manpower[${rank.id}][manpower]" value="${rank.manpower}" class="manpower-hidden-input" data-rank-id="${rank.id}">
                    `;

                    selectedRanksContainer.appendChild(rankCard);

                    // Add event listeners for the buttons and input
                    const removeBtn = rankCard.querySelector('.remove-rank');
                    const decreaseBtn = rankCard.querySelector('.decrease-manpower');
                    const increaseBtn = rankCard.querySelector('.increase-manpower');
                    const manpowerInput = rankCard.querySelector('.manpower-input');
                    const hiddenManpowerInput = rankCard.querySelector('.manpower-hidden-input');

                    removeBtn.addEventListener('click', function() {
                        const rankIdToRemove = this.dataset.rankId;
                        delete selectedRanks[rankIdToRemove];

                        // Reset the option in the dropdown
                        const optionToReset = document.querySelector(
                            `.rank-option[data-value="${rankIdToRemove}"]`);
                        if (optionToReset) {
                            const checkboxContainer = optionToReset.querySelector('.rank-checkbox');
                            const checkmark = optionToReset.querySelector('.rank-checkmark');

                            checkboxContainer.classList.remove('bg-blue-500', 'border-blue-500');
                            checkboxContainer.classList.add('border-gray-300');
                            checkmark.classList.add('opacity-0');
                            checkmark.classList.remove('opacity-100');
                            optionToReset.classList.remove('bg-blue-50', 'border-blue-200');
                        }

                        updateSelectedRanksDisplay();
                    });

                    decreaseBtn.addEventListener('click', function() {
                        const rankId = this.dataset.rankId;
                        const currentValue = parseInt(manpowerInput.value);
                        if (currentValue > 1) {
                            const newValue = currentValue - 1;
                            manpowerInput.value = newValue;
                            hiddenManpowerInput.value = newValue;
                            selectedRanks[rankId].manpower = newValue;
                        }
                    });

                    increaseBtn.addEventListener('click', function() {
                        const rankId = this.dataset.rankId;
                        const currentValue = parseInt(manpowerInput.value);
                        const newValue = currentValue + 1;
                        manpowerInput.value = newValue;
                        hiddenManpowerInput.value = newValue;
                        selectedRanks[rankId].manpower = newValue;
                    });

                    manpowerInput.addEventListener('change', function() {
                        const rankId = this.dataset.rankId;
                        let newValue = parseInt(this.value);

                        // Ensure minimum value is 1
                        if (isNaN(newValue) || newValue < 1) {
                            newValue = 1;
                            this.value = newValue;
                        }

                        hiddenManpowerInput.value = newValue;
                        selectedRanks[rankId].manpower = newValue;
                    });
                });
            }

            // Initialize the display
            updateSelectedRanksDisplay();

            // Form validation
            const hhmmRegex = /^([01][0-9]|2[0-3]):[0-5][0-9]$/;

            dutyForm.addEventListener('submit', function(e) {
                console.log('Form submitted');

                const startTime = startTimeEl.value.trim();
                const endTime = endTimeEl.value.trim();

                // Remove existing error messages
                document.querySelectorAll('.time-error, .rank-error').forEach(el => el.remove());

                let hasError = false;

                if (!hhmmRegex.test(startTime)) {
                    showError(startTimeEl, 'Start time must be in HH:MM format (e.g., 08:30, 17:30).',
                        'time-error');
                    hasError = true;
                }

                if (!hhmmRegex.test(endTime)) {
                    showError(endTimeEl, 'End time must be in HH:MM format (e.g., 10:00, 22:00).',
                        'time-error');
                    hasError = true;
                }

                if (hhmmRegex.test(startTime) && hhmmRegex.test(endTime) && endTime <= startTime) {
                    showError(endTimeEl, 'End time must be after start time.', 'time-error');
                    hasError = true;
                }

                // Check if at least one rank is selected
                if (Object.keys(selectedRanks).length === 0) {
                    const errorEl = document.createElement('p');
                    errorEl.classList.add('text-rose-500', 'text-sm', 'mt-2', 'rank-error');
                    errorEl.innerHTML = `
                        <svg class="w-4 h-4 mr-1 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        At least one rank must be selected with manpower.
                    `;
                    selectedRanksContainer.parentNode.appendChild(errorEl);
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                }
            });

            function showError(inputEl, message, errorClass) {
                const errorEl = document.createElement('p');
                errorEl.classList.add('text-rose-500', 'text-sm', 'mt-1', errorClass);
                errorEl.innerText = message;
                inputEl.parentNode.appendChild(errorEl);
                inputEl.focus();
            }

            // Enhanced time validation with real-time feedback
            function validateTimes() {
                const startTime = startTimeEl.value;
                const endTime = endTimeEl.value;

                // Remove existing validation styles
                startTimeEl.classList.remove('border-rose-500');
                endTimeEl.classList.remove('border-rose-500');
                startTimeEl.classList.add('border-green-500');
                endTimeEl.classList.add('border-green-500');

                // Remove existing error messages
                document.querySelectorAll('.time-error').forEach(el => el.remove());

                if (startTime && endTime) {
                    if (endTime <= startTime) {
                        endTimeEl.classList.remove('border-green-500');
                        endTimeEl.classList.add('border-rose-500');

                        const errorEl = document.createElement('p');
                        errorEl.classList.add('text-rose-500', 'text-sm', 'mt-1', 'time-error');
                        errorEl.innerHTML = '⚠️ End time must be after start time';
                        endTimeEl.parentNode.appendChild(errorEl);
                    }
                }
            }

            startTimeEl.addEventListener('change', validateTimes);
            endTimeEl.addEventListener('change', validateTimes);

            // Initial validation if values are pre-filled
            validateTimes();

            // Form submission enhancement with loading state
            dutyForm.addEventListener('submit', function(e) {
                // Basic validation passed, show loading state
                if (dutyForm.checkValidity() && Object.keys(selectedRanks).length > 0) {
                    const submitBtn = dutyForm.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    `;
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                }
            });

            // Add smooth animations and interactions
            const formElements = document.querySelectorAll('input, select, textarea');
            formElements.forEach(element => {
                element.addEventListener('focus', function() {
                    this.parentElement.classList.add('transform', 'scale-105');
                });

                element.addEventListener('blur', function() {
                    this.parentElement.classList.remove('transform', 'scale-105');
                });
            });

            // Add pulse animation to required fields
            const requiredLabels = document.querySelectorAll('label:has(+ * [required])');
            requiredLabels.forEach(label => {
                label.classList.add('animate-pulse');
                setTimeout(() => {
                    label.classList.remove('animate-pulse');
                }, 3000);
            });
        });
    </script>
@endpush
