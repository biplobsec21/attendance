@extends('mpm.layouts.app')

@section('title', 'Site Settings')

@section('content')

    <div class="container mx-auto px-4 py-8">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />

        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Site Settings</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Manage your site configuration and timing settings</p>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div
                    class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-green-800 dark:text-green-300 font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- Settings Form -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="p-6 space-y-6">
                        <!-- Site Name -->
                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Site Name
                            </label>
                            <input type="text" name="site_name" id="site_name"
                                value="{{ old('site_name', $settings->site_name) }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition duration-200"
                                placeholder="Enter your site name">
                            @error('site_name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Timing Settings Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- PT Time -->
                            <div>
                                <label for="pt_time"
                                    class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
                                    PT Time
                                </label>
                                <div class="relative">
                                    <input type="text" id="pt_time" name="pt_time"
                                        value="{{ old('pt_time', $settings->pt_time ? $settings->pt_time->format('H:i') : '06:00') }}"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 @error('pt_time') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror flatpickr-time-input"
                                        placeholder="Select PT Time" readonly required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('pt_time')
                                    <p class="text-rose-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Games Time -->
                            <div>
                                <label for="games_time"
                                    class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
                                    Games Time
                                </label>
                                <div class="relative">
                                    <input type="text" id="games_time" name="games_time"
                                        value="{{ old('games_time', $settings->games_time ? $settings->games_time->format('H:i') : '16:00') }}"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 @error('games_time') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror flatpickr-time-input"
                                        placeholder="Select Games Time" readonly required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('games_time')
                                    <p class="text-rose-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Parade Time -->
                            <div>
                                <label for="parade_time"
                                    class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
                                    Parade Time
                                </label>
                                <div class="relative">
                                    <input type="text" id="parade_time" name="parade_time"
                                        value="{{ old('parade_time', $settings->parade_time ? $settings->parade_time->format('H:i') : '08:00') }}"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 @error('parade_time') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror flatpickr-time-input"
                                        placeholder="Select Parade Time" readonly required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('parade_time')
                                    <p class="text-rose-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Roll Call Time -->
                            <div>
                                <label for="roll_call_time"
                                    class="block text-xs font-medium text-gray-500 mb-2 uppercase tracking-wide">
                                    Roll Call Time
                                </label>
                                <div class="relative">
                                    <input type="text" id="roll_call_time" name="roll_call_time"
                                        value="{{ old('roll_call_time', $settings->roll_call_time ? $settings->roll_call_time->format('H:i') : '07:30') }}"
                                        class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-900 transition-all duration-300 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 @error('roll_call_time') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror flatpickr-time-input"
                                        placeholder="Select Roll Call Time" readonly required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                @error('roll_call_time')
                                    <p class="text-rose-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Times Display -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Current Schedule</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">PT Time:</span>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $settings->pt_time ? $settings->pt_time->format('H:i') : 'Not set' }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Games Time:</span>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $settings->games_time ? $settings->games_time->format('H:i') : 'Not set' }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Parade Time:</span>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $settings->parade_time ? $settings->parade_time->format('H:i') : 'Not set' }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Roll Call:</span>
                                    <p class="font-medium text-gray-900 dark:text-white">
                                        {{ $settings->roll_call_time ? $settings->roll_call_time->format('H:i') : 'Not set' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ url()->previous() }}"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition duration-200">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition duration-200">
                                Update Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize flatpickr for time inputs
            const timeInputs = document.querySelectorAll('.flatpickr-time-input');

            timeInputs.forEach(input => {
                flatpickr(input, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    minuteIncrement: 30,
                    defaultHour: 6,
                    defaultMinute: 0,
                    // Optional: Add custom styling to the calendar
                    onReady: function(selectedDates, dateStr, instance) {
                        instance.calendarContainer.classList.add('dark:bg-gray-800',
                            'dark:text-white');
                    }
                });
            });

            // Add event listeners for time changes
            timeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    console.log(`${this.name} changed to: ${this.value}`);
                });
            });
        });
    </script>
@endpush
