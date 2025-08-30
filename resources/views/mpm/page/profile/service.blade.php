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
            </form>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
