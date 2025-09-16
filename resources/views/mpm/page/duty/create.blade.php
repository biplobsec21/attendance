@extends('mpm.layouts.app')

@section('title', 'Create Duty Record')

@section('content')

    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />

        <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full max-w-lg mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Create Duty Record</h1>

            {{-- The form now points to the 'duty.store' route using the POST method --}}
            <form method="POST" action="{{ route('duty.store') }}">
                {{-- CSRF token for security --}}
                @csrf

                {{-- Duty Name Input --}}
                <div class="mb-4">
                    <label for="duty-name" class="block text-gray-700 text-sm font-bold mb-2">Duty Name <span
                            class="text-red-600">*</span></label>
                    {{-- 'name' attribute added. 'old()' helper retains input on validation failure. --}}
                    <input type="text" id="duty-name" name="duty_name" value="{{ old('duty_name') }}"
                        class="shadow-sm appearance-none border @error('duty_name') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Enter duty name" required>
                    {{-- Displays validation error message for this field --}}
                    @error('duty_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Duty Time Inputs (Start and End) --}}
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Duty Time <span class="text-red-600">*</span>
                    </label>
                    <div class="flex space-x-4">

                        <!-- Start Time -->
                        <div class="w-1/2">
                            <label for="start-time" class="block text-gray-500 text-xs font-bold mb-1">Start Time</label>
                            <select id="start-time" name="start_time"
                                class="shadow-sm border @error('start_time') border-red-500 @enderror
                       rounded-lg w-full py-2 px-3 text-gray-700 leading-tight
                       focus:outline-none focus:ring-2 focus:ring-orange-500"
                                required>
                                <option value="">Select Start Time</option>
                                @for ($h = 0; $h < 24; $h++)
                                    @for ($m = 0; $m < 60; $m += 30)
                                        {{-- 30-minute intervals --}}
                                        @php
                                            $time = sprintf('%02d:%02d', $h, $m); // military HH:MM
                                        @endphp
                                        <option value="{{ $time }}"
                                            {{ old('start_time', isset($duty) ? \Carbon\Carbon::parse($duty->start_time)->format('H:i') : '') == $time ? 'selected' : '' }}>
                                            {{ $time }}
                                        </option>
                                    @endfor
                                @endfor
                            </select>
                            @error('start_time')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Time -->
                        <div class="w-1/2">
                            <label for="end-time" class="block text-gray-500 text-xs font-bold mb-1">End Time</label>
                            <select id="end-time" name="end_time"
                                class="shadow-sm border @error('end_time') border-red-500 @enderror
                       rounded-lg w-full py-2 px-3 text-gray-700 leading-tight
                       focus:outline-none focus:ring-2 focus:ring-orange-500"
                                required>
                                <option value="">Select End Time</option>
                                @for ($h = 0; $h < 24; $h++)
                                    @for ($m = 0; $m < 60; $m += 30)
                                        @php
                                            $time = sprintf('%02d:%02d', $h, $m); // military HH:MM
                                        @endphp
                                        <option value="{{ $time }}"
                                            {{ old('end_time', isset($duty) ? \Carbon\Carbon::parse($duty->end_time)->format('H:i') : '') == $time ? 'selected' : '' }}>
                                            {{ $time }}
                                        </option>
                                    @endfor
                                @endfor
                            </select>
                            @error('end_time')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>






                {{-- Manpower Input --}}
                <div class="mb-4">
                    <label for="manpower" class="block text-gray-700 text-sm font-bold mb-2">Manpower <span
                            class="text-red-600">*</span></label>
                    <input type="number" id="manpower" name="manpower" value="{{ old('manpower') }}" min="1"
                        class="shadow-sm appearance-none border @error('manpower') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Enter number of personnel" required>
                    @error('manpower')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remark Input --}}
                <div class="mb-4">
                    <label for="remark" class="block text-gray-700 text-sm font-bold mb-2">Remark</label>
                    {{-- 'old()' helper for textarea goes between the tags --}}
                    <textarea id="remark" name="remark"
                        class="shadow-sm appearance-none border @error('remark') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Enter any remarks..." rows="3">{{ old('remark') }}</textarea>
                    @error('remark')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status Select --}}
                <div class="mb-6">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status <span
                            class="text-red-600">*</span></label>
                    <select id="status" name="status"
                        class="shadow-sm border @error('status') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        required>
                        {{-- Logic to re-select the old status value on validation failure --}}
                        <option value="Active" @if (old('status', 'Active') == 'Active') selected @endif>Active</option>
                        <option value="Inactive" @if (old('status') == 'Inactive') selected @endif>Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between mt-6">
                    <a href="{{ url('duty/index') }}"
                        class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                        Back to List
                    </a>
                    <button
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors"
                        type="submit">
                        Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
@push('script')
    <script>
        // Grab the form and time inputs
        const dutyForm = document.querySelector('form');
        const startTimeEl = document.getElementById('start-time');
        const endTimeEl = document.getElementById('end-time');

        // Regex to validate HHMM format
        const hhmmRegex = /^([01][0-9]|2[0-3])[0-5][0-9]$/;

        dutyForm.addEventListener('submit', function(e) {
            const startTime = startTimeEl.value.trim();
            const endTime = endTimeEl.value.trim();

            // Reset previous error messages
            document.querySelectorAll('.time-error').forEach(el => el.remove());

            let hasError = false;

            // Validate start time
            if (!hhmmRegex.test(startTime)) {
                showError(startTimeEl, 'Start time must be in HHMM format (e.g., 0830, 1730).');
                hasError = true;
            }

            // Validate end time
            if (!hhmmRegex.test(endTime)) {
                showError(endTimeEl, 'End time must be in HHMM format (e.g., 1000, 2200).');
                hasError = true;
            }

            // Ensure end time is after start time
            if (hhmmRegex.test(startTime) && hhmmRegex.test(endTime) && parseInt(endTime) <= parseInt(startTime)) {
                showError(endTimeEl, 'End time must be after start time.');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault(); // stop form submission
            }
        });

        function showError(inputEl, message) {
            const errorEl = document.createElement('p');
            errorEl.classList.add('text-red-500', 'text-xs', 'mt-1', 'time-error');
            errorEl.innerText = message;
            inputEl.parentNode.appendChild(errorEl);
            inputEl.focus();
        }
    </script>
@endpush
