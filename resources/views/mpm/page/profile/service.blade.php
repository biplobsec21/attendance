@extends('mpm.layouts.app')

@section('title', 'Create Service')

@section('content')
    {{-- Profile Steps Navigation --}}

    <x-profile-step-nav :steps="$profileSteps" />


    <div class="container mx-auto p-6">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Service Details</h1>
            <p class="text-gray-500">Please provide your service history and appointment details.</p>
        </div>
        <div class="bg-white border rounded-lg p-8">
            <form>
                <div class="grid grid-cols-12 gap-8 pb-8">
                    <div class="col-span-12 md:col-span-4">
                        <label class="font-bold text-gray-700">Service Dates</label>
                        <p class="text-sm text-gray-500 mt-1">Enter your date of joining. Service length will be
                            calculated automatically as of today, Thursday, August 28, 2025.</p>
                    </div>
                    <div class="col-span-12 md:col-span-8 flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-1/2">
                            <label for="joining-date" class="block text-sm font-medium text-gray-600 mb-1">Date of
                                Joining</label>
                            <input id="joining-date" type="date"
                                class="w-full p-3 border rounded-md focus:ring-2 focus:ring-orange-500 focus:outline-none">
                        </div>
                        <div class="w-full sm:w-1/2">
                            <label for="service-length" class="block text-sm font-medium text-gray-600 mb-1">Service
                                Length</label>
                            <input id="service-length" type="text"
                                class="w-full p-3 border rounded-md bg-gray-200 cursor-not-allowed" readonly>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-8 border-t pt-8 pb-8">
                    <div class="col-span-12 md:col-span-4">
                        <label class="font-bold text-gray-700">Current Appointments</label>
                        <p class="text-sm text-gray-500 mt-1">Add any appointments you currently hold. Click the
                            button to add a new row.</p>
                    </div>
                    <div class="col-span-12 md:col-span-8">
                        <div id="current-appointments-container" class="space-y-4"></div>
                        <button type="button" id="add-current-appointment"
                            class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors text-sm">+
                            Add Appointment</button>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-8 border-t pt-8">
                    <div class="col-span-12 md:col-span-4">
                        <label class="font-bold text-gray-700">Previous Appointments</label>
                        <p class="text-sm text-gray-500 mt-1">List your notable previous appointments.</p>
                    </div>
                    <div class="col-span-12 md:col-span-8">
                        <div id="previous-appointments-container" class="space-y-4"></div>
                        <button type="button" id="add-previous-appointment"
                            class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors text-sm">+
                            Add Appointment</button>
                    </div>
                </div>
                <div id="bottom-navigation" class="flex justify-between mt-6 border-t pt-6">
                    <button id="prev-btn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded"
                        style="display: inline-flex;">Previous</button>
                    <button id="next-btn"
                        class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded">Save
                        &amp; Continue</button>
                </div>
            </form>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const joiningDateInput = document.getElementById('joining-date');
        const serviceLengthInput = document.getElementById('service-length');

        function calculateDuration(startDate) {
            if (!startDate) return '';
            const fromDate = new Date(startDate);
            const toDate = new Date('2025-08-28T01:13:54');
            let years = toDate.getFullYear() - fromDate.getFullYear();
            let months = toDate.getMonth() - fromDate.getMonth();
            let days = toDate.getDate() - fromDate.getDate();
            if (days < 0) {
                months--;
                days += new Date(toDate.getFullYear(), toDate.getMonth(), 0).getDate();
            }
            if (months < 0) {
                years--;
                months += 12;
            }
            return `${years}Y ${months}M ${days}D`;
        }
        if (joiningDateInput) {
            joiningDateInput.addEventListener('change', () => {
                serviceLengthInput.value = calculateDuration(joiningDateInput.value)
            });
        }

        const addCurrentBtn = document.getElementById('add-current-appointment');
        const currentContainer = document.getElementById('current-appointments-container');
        const addPreviousBtn = document.getElementById('add-previous-appointment');
        const previousContainer = document.getElementById('previous-appointments-container');

        function createAppointmentRow() {
            const div = document.createElement('div');
            div.className = 'grid grid-cols-1 sm:grid-cols-5 gap-2 items-center border p-3 rounded-md';
            div.innerHTML = `
        <input type="text" placeholder="Name" class="sm:col-span-2 w-full p-2 border rounded-md">
        <input type="date" placeholder="Start Date" class="w-full p-2 border rounded-md">
        <input type="date" placeholder="End Date" class="w-full p-2 border rounded-md">
        <button type="button" class="remove-row bg-red-500 hover:bg-red-600 text-white p-2 rounded-md h-full">✕</button>
        `;
            return div;
        }

        if (addCurrentBtn) addCurrentBtn.addEventListener('click', () =>
            currentContainer.appendChild(createAppointmentRow()));
        if (addPreviousBtn) addPreviousBtn.addEventListener('click', () =>
            previousContainer.appendChild(createAppointmentRow()));
    </script>
@endpush
