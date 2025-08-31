@extends('mpm.layouts.app')

@section('title', 'Create Service')

@section('content')
    {{-- Profile Steps Navigation --}}
    @php
        // Get previous appointments from DB
        $dbPreviousAppointments = $profile->services
            ->where('appointment_type', 'previous')
            ->map(function ($s) {
                return [
                    'name' => $s->appointments_name,
                    'from_date' => $s->appointments_from_date,
                    'to_date' => $s->appointments_to_date,
                ];
            })
            ->toArray();

        // Use old input if exists, otherwise DB data
        $previousAppointments = old('previous_appointments', $dbPreviousAppointments);
    @endphp



    <x-profile-step-nav :steps="$profileSteps" :profileId="$profile->id ?? null" />


    <main class="container mx-auto p-6">


        <div class="grid md:grid-cols-12 gap-6 items-center">
            <div class="md:col-span-7 mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Service Details</h1>
                <p class="text-gray-500">Please provide your service history and appointment details.</p>
            </div>
            <div class="md:col-span-5">
                @include('mpm.components.alerts')
            </div>
        </div>
        <div class="bg-white border rounded-lg p-8">
            <form action="{{ route('profile.saveService', $profile->id) }}" method="POST">
                @csrf
                @php
                    $redirectAction = $profile->service_completed;
                @endphp
                <input type="hidden" name="redirect" value="{{ $redirectAction }}" />
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
                            <x-form.input name="joining_date" type="date" :value="old('joining_date', $profile?->joining_date)" />

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
                        <p class="text-sm text-gray-500 mt-1">Add any appointments you currently hold.</p>
                    </div>
                    <div class="col-span-12 md:col-span-8">
                        {{-- Current Appointment Section --}}
                        <div class="mb-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-form.input name="current_appointment_name" label="Appointment Name"
                                    placeholder="e.g., Platoon Commander" :value="old('current_appointment_name', $current?->appointments_name)" />

                                <x-form.input name="current_appointment_from_date" type="date" label="From Date"
                                    :value="old('current_appointment_from_date', $current?->appointments_from_date)" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-8 border-t pt-8">
                    <div class="col-span-12 md:col-span-4">
                        <label class="font-bold text-gray-700">Previous Appointments</label>
                        <p class="text-sm text-gray-500 mt-1">List your notable previous appointments.</p>
                    </div>
                    <div class="col-span-12 md:col-span-8">
                        <div id="previous-appointments-container" class="space-y-4"></div>
                        <div id="bottom-navigation">
                            {{-- Previous Appointments Section --}}
                            <div class="mb-8">

                                <div id="previous-appointments-wrapper" class="space-y-4">
                                    @foreach ($previousAppointments as $index => $prev)
                                        <div
                                            class="previous-appointment flex items-center gap-4 p-4 border rounded-md relative">
                                            {{-- Remove button --}}
                                            <button type="button"
                                                class="remove-btn absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold">
                                                ✖
                                            </button>

                                            {{-- Appointment Name --}}
                                            <x-form.input class="flex-1"
                                                name="previous_appointments[{{ $index }}][name]"
                                                label="Appointment Name" placeholder="e.g., Company Clerk"
                                                :value="$prev['name'] ?? ''" />

                                            {{-- From Date --}}
                                            <x-form.input class="w-70"
                                                name="previous_appointments[{{ $index }}][from_date]" type="date"
                                                label="From Date" :value="$prev['from_date'] ?? ''" />

                                            {{-- To Date --}}
                                            <x-form.input class="w-70"
                                                name="previous_appointments[{{ $index }}][to_date]" type="date"
                                                label="To Date" :value="$prev['to_date'] ?? ''" />
                                        </div>
                                    @endforeach
                                </div>

                                <p class="text-md font-semibold text-gray-700 mb-4 flex justify-between items-center">
                                    <button type="button" id="add-previous-btn"
                                        class="bg-gray-200 text-dark px-4 py-2 rounded-md shadow hover:bg-gray-300">
                                        + Add Previous Appointments
                                    </button>
                                </p>

                            </div>
                        </div>
                    </div>
                </div>
                <div id="bottom-navigation" class="flex justify-between mt-6 border-t pt-6">
                    <button id="prev-btn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded"
                        style="display: inline-flex;">Previous</button>
                    <button id="next-btn"
                        class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded">Save
                        &amp; Continue</button>
                    </di </form>
                </div>

    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const joiningDateInput = document.getElementById("joining_date");
            const serviceLengthInput = document.getElementById("service-length");

            function calculateServiceLength() {
                const joinDate = new Date(joiningDateInput.value);
                const today = new Date();

                if (!joiningDateInput.value) {
                    serviceLengthInput.value = "";
                    return;
                }

                // Calculate difference in milliseconds
                let diff = today - joinDate;
                if (diff < 0) diff = 0; // joining date in future

                // Convert milliseconds to days
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const years = Math.floor(days / 365);
                const months = Math.floor((days % 365) / 30);
                const remainingDays = (days % 365) % 30;

                serviceLengthInput.value = `${years} years, ${months} months, ${remainingDays} days`;
            }

            // Calculate on page load
            calculateServiceLength();
            joiningDateInput.addEventListener("change", calculateServiceLength);

            let index = {{ count($previousAppointments) }};

            const addBtn = document.getElementById("add-previous-btn");
            const wrapper = document.getElementById("previous-appointments-wrapper");

            // Add new row
            addBtn.addEventListener("click", function() {
                const template = `
            <div class="previous-appointment flex items-center gap-4 p-4 border rounded-md relative">
                <button type="button" class="remove-btn absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold">✖</button>
                <x-form.input class="flex-1" name="previous_appointments[${index}][name]" label="Appointment Name" placeholder="e.g., Company Clerk" />
                <x-form.input class="w-70" name="previous_appointments[${index}][from_date]" type="date" label="From Date" />
                <x-form.input class="w-70" name="previous_appointments[${index}][to_date]" type="date" label="To Date" />
            </div>
        `;
                wrapper.insertAdjacentHTML("beforeend", template);
                index++;
            });

            // Remove row with confirmation if any input has value
            wrapper.addEventListener("click", function(e) {
                if (e.target.classList.contains("remove-btn")) {
                    const parent = e.target.closest(".previous-appointment");
                    const inputs = parent.querySelectorAll("input");

                    const hasValue = Array.from(inputs).some(input => input.value.trim() !== "");
                    if (hasValue && !confirm(
                            "Some data is entered. Are you sure you want to remove this appointment?")) {
                        return;
                    }

                    parent.remove();
                }
            });
        });
    </script>
@endpush
