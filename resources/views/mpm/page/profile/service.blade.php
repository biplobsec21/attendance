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
                    'id' => $s->appointment_id,
                    'name' => $s->appointments_name,
                    'from_date' => $s->appointments_from_date
                        ? \Carbon\Carbon::parse($s->appointments_from_date)->format('Y-m-d')
                        : null,
                    'to_date' => $s->appointments_to_date
                        ? \Carbon\Carbon::parse($s->appointments_to_date)->format('Y-m-d')
                        : null,
                ];
            })
            ->toArray();

        // Get current appointment from DB
        $dbCurrentAppointment = $profile->services->where('appointment_type', 'current')->first();
        $currentId = old(
            'current_appointment_id',
            $dbCurrentAppointment ? $dbCurrentAppointment->appointment_id : null,
        );
        $currentFromDate = old(
            'current_appointment_from_date',
            $dbCurrentAppointment
                ? \Carbon\Carbon::parse($dbCurrentAppointment->appointments_from_date)->format('Y-m-d')
                : null,
        );

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
            <form action="{{ route('soldier.saveService', $profile->id) }}" method="POST" id="service-form">
                @csrf
                @php
                    $redirectAction = $profile->service_completed;
                @endphp
                <input type="hidden" name="redirect" value="{{ $redirectAction }}" />
                <div class="grid grid-cols-12 gap-8 pb-8">
                    <div class="col-span-12 md:col-span-4">
                        <label class="font-bold text-gray-700">Service Dates</label>
                        <p class="text-sm text-gray-500 mt-1">Enter your date of joining. Service length will be calculated
                            automatically.</p>
                    </div>

                    <div class="col-span-12 md:col-span-8 flex flex-col sm:flex-row gap-4">
                        <div class="w-full sm:w-1/2">
                            <label for="joining-date" class="block text-sm font-medium text-gray-600 mb-1">Date of
                                Joining</label>
                            <x-form.input name="joining_date" type="date" :value="old(
                                'joining_date',
                                $profile?->joining_date
                                    ? \Carbon\Carbon::parse($profile->joining_date)->format('Y-m-d')
                                    : '',
                            )" />
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
                                <div>
                                    <label for="current_appointment_id"
                                        class="block text-sm font-medium text-gray-600 mb-1">Appointment</label>
                                    <select name="current_appointment_id" id="current_appointment_id"
                                        class="w-full p-3 border rounded-md focus:ring-orange-500 focus:border-orange-500">
                                        <option value="">-- Select Appointment --</option>
                                        @foreach ($appointments as $appointment)
                                            <option value="{{ $appointment->id }}"
                                                {{ $currentId == $appointment->id ? 'selected' : '' }}>
                                                {{ $appointment->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="current_appointment_from_date"
                                        class="block text-sm font-medium text-gray-600 mb-1">From Date</label>
                                    <x-form.input name="current_appointment_from_date" type="date"
                                        id="current_appointment_from_date" :value="$currentFromDate" />
                                </div>
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
                                                class="remove-btn absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold">✖</button>

                                            {{-- Appointment Dropdown --}}
                                            <div class="flex-1">
                                                <label
                                                    class="block text-sm font-medium text-gray-600 mb-1">Appointment</label>
                                                <select name="previous_appointments[{{ $index }}][id]"
                                                    class="previous-appointment-select w-full p-3 border rounded-md focus:ring-orange-500 focus:border-orange-500">
                                                    <option value="">-- Select Appointment --</option>
                                                    @foreach ($appointments as $appointment)
                                                        <option value="{{ $appointment->id }}"
                                                            {{ old("previous_appointments.$index.id", $prev['id'] ?? '') == $appointment->id ? 'selected' : '' }}>
                                                            {{ $appointment->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- From Date --}}
                                            <div class="w-70">
                                                <label class="block text-sm font-medium text-gray-600 mb-1">From
                                                    Date</label>
                                                <x-form.input name="previous_appointments[{{ $index }}][from_date]"
                                                    type="date" class="previous-from-date" :value="$prev['from_date'] ?? ''" />
                                            </div>

                                            {{-- To Date --}}
                                            <div class="w-70">
                                                <label class="block text-sm font-medium text-gray-600 mb-1">To Date</label>
                                                <x-form.input name="previous_appointments[{{ $index }}][to_date]"
                                                    type="date" class="previous-to-date" :value="$prev['to_date'] ?? ''" />
                                            </div>
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
                        class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-6 rounded">Save &amp;
                        Continue</button>
                </div>
            </form>
        </div>
    </main>
@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const joiningDateInput = document.getElementById("joining_date");
            const serviceLengthInput = document.getElementById("service-length");
            const currentAppointmentSelect = document.getElementById("current_appointment_id");
            const currentFromDateInput = document.getElementById("current_appointment_from_date");
            const form = document.getElementById("service-form");

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

            // Current appointment validation
            function validateCurrentAppointment() {
                if (currentAppointmentSelect.value) {
                    currentFromDateInput.setAttribute("required", "required");
                } else {
                    currentFromDateInput.removeAttribute("required");
                }
            }

            currentAppointmentSelect.addEventListener("change", validateCurrentAppointment);
            validateCurrentAppointment(); // Initial validation

            // Fix: Ensure we always have an array
            let index = {{ $previousAppointments ? count($previousAppointments) : 0 }};

            const addBtn = document.getElementById("add-previous-btn");
            const wrapper = document.getElementById("previous-appointments-wrapper");

            // Add new row
            addBtn.addEventListener("click", function() {
                const template = `
                    <div class="previous-appointment flex items-center gap-4 p-4 border rounded-md relative">
                        <button type="button" class="remove-btn absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold">✖</button>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-600 mb-1">Appointment</label>
                            <select name="previous_appointments[${index}][id]" class="previous-appointment-select w-full p-3 border rounded-md focus:ring-orange-500 focus:border-orange-500">
                                <option value="">-- Select Appointment --</option>
                                @foreach ($appointments as $appointment)
                                    <option value="{{ $appointment->id }}">{{ $appointment->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-70">
                            <label class="block text-sm font-medium text-gray-600 mb-1">From Date</label>
                            <input type="date" name="previous_appointments[${index}][from_date]" class="previous-from-date w-full p-3 border rounded-md focus:ring-orange-500 focus:border-orange-500" />
                        </div>
                        <div class="w-70">
                            <label class="block text-sm font-medium text-gray-600 mb-1">To Date</label>
                            <input type="date" name="previous_appointments[${index}][to_date]" class="previous-to-date w-full p-3 border rounded-md focus:ring-orange-500 focus:border-orange-500" />
                        </div>
                    </div>
                `;
                wrapper.insertAdjacentHTML("beforeend", template);

                // Add event listeners to the new elements
                const newAppointment = wrapper.lastElementChild;
                const selectElement = newAppointment.querySelector(".previous-appointment-select");
                const fromDateElement = newAppointment.querySelector(".previous-from-date");
                const toDateElement = newAppointment.querySelector(".previous-to-date");

                selectElement.addEventListener("change", function() {
                    validatePreviousAppointment(selectElement, fromDateElement, toDateElement);
                });

                index++;
            });

            // Remove row with confirmation if any input has value
            wrapper.addEventListener("click", function(e) {
                if (e.target.classList.contains("remove-btn")) {
                    const parent = e.target.closest(".previous-appointment");
                    const inputs = parent.querySelectorAll("input, select");

                    const hasValue = Array.from(inputs).some(input => input.value.trim() !== "");
                    if (hasValue && !confirm(
                            "Some data is entered. Are you sure you want to remove this appointment?")) {
                        return;
                    }

                    parent.remove();
                }
            });

            // Previous appointment validation
            function validatePreviousAppointment(selectElement, fromDateElement, toDateElement) {
                if (selectElement.value) {
                    fromDateElement.setAttribute("required", "required");
                    toDateElement.setAttribute("required", "required");
                } else {
                    fromDateElement.removeAttribute("required");
                    toDateElement.removeAttribute("required");
                }
            }

            // Add event listeners to existing previous appointments
            document.querySelectorAll(".previous-appointment").forEach(appointment => {
                const selectElement = appointment.querySelector(".previous-appointment-select");
                const fromDateElement = appointment.querySelector(".previous-from-date");
                const toDateElement = appointment.querySelector(".previous-to-date");

                selectElement.addEventListener("change", function() {
                    validatePreviousAppointment(selectElement, fromDateElement, toDateElement);
                });

                // Initial validation
                validatePreviousAppointment(selectElement, fromDateElement, toDateElement);
            });

            // Form submission validation
            form.addEventListener("submit", function(e) {
                let isValid = true;
                let errorMessages = [];

                // Validate current appointment
                if (currentAppointmentSelect.value) {
                    // Check if the date field has a value using multiple approaches
                    const hasValue = currentFromDateInput.value &&
                        currentFromDateInput.value !== "" &&
                        currentFromDateInput.value !== null;

                    if (!hasValue) {
                        currentFromDateInput.classList.add("border-red-500");
                        isValid = false;
                        errorMessages.push("Current appointment From Date is required");
                    } else {
                        currentFromDateInput.classList.remove("border-red-500");
                    }
                }

                // Validate previous appointments
                document.querySelectorAll(".previous-appointment").forEach(function(appointment, index) {
                    const selectElement = appointment.querySelector(".previous-appointment-select");
                    const fromDateElement = appointment.querySelector(".previous-from-date");
                    const toDateElement = appointment.querySelector(".previous-to-date");

                    if (selectElement.value) {
                        // Check from date using multiple approaches
                        const hasFromDate = fromDateElement.value &&
                            fromDateElement.value !== "" &&
                            fromDateElement.value !== null;

                        if (!hasFromDate) {
                            fromDateElement.classList.add("border-red-500");
                            isValid = false;
                            errorMessages.push(
                                `Previous appointment ${index + 1} From Date is required`);
                        } else {
                            fromDateElement.classList.remove("border-red-500");
                        }

                        // Check to date using multiple approaches
                        const hasToDate = toDateElement.value &&
                            toDateElement.value !== "" &&
                            toDateElement.value !== null;

                        if (!hasToDate) {
                            toDateElement.classList.add("border-red-500");
                            isValid = false;
                            errorMessages.push(
                                `Previous appointment ${index + 1} To Date is required`);
                        } else {
                            toDateElement.classList.remove("border-red-500");
                        }

                        // Check if to date is after from date
                        if (hasFromDate && hasToDate) {
                            const fromDate = new Date(fromDateElement.value);
                            const toDate = new Date(toDateElement.value);

                            if (fromDate > toDate) {
                                toDateElement.classList.add("border-red-500");
                                isValid = false;
                                errorMessages.push(
                                    `Previous appointment ${index + 1} To Date must be after From Date`
                                );
                            } else {
                                toDateElement.classList.remove("border-red-500");
                            }
                        }
                    }
                });

                // if (!isValid) {
                //     e.preventDefault();
                //     alert("Please fix the following errors:\n\n" + errorMessages.join("\n"));
                // }
            });
        });
    </script>
@endpush
