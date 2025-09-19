@extends('mpm.layouts.app')

@section('title', 'Appointments Manager')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs_auto()" />
        @include('mpm.components.alerts')

        <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Military Appointment Management</h1>
                <p class="text-gray-600">Manage soldier appointments and view all appointed personnel</p>
            </div>

            <!-- Action Buttons -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('appointmanager.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Appointment
                    </a>
                </div>

                <!-- Search and Filter -->
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text" id="searchInput" placeholder="Search soldiers..."
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button
                            class="tab-button active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600"
                            data-tab="current">
                            Current Appointments
                            <span class="ml-2 bg-blue-100 text-blue-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $currentServices->count() }}
                            </span>
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                            data-tab="previous">
                            Previous Appointments
                            <span class="ml-2 bg-gray-100 text-gray-800 py-0.5 px-2 rounded-full text-xs">
                                {{ $previousServices->count() }}
                            </span>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Current Appointments Tab -->
            <div id="current-tab" class="tab-content">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Soldier Details
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        NAME
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        appointed Date
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="currentTableBody">
                                @forelse($currentServices as $index => $service)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ strtoupper(substr($service->soldier->full_name ?? 'N/A', 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $service->soldier->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $service->soldier->rank->name ?? 'No Rank' }} |
                                                        {{ $service->soldier->company->name ?? 'No Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $service->appointments_name }}
                                            </div>
                                            @if ($service->note)
                                                <div class="text-sm text-gray-500 truncate max-w-xs"
                                                    title="{{ $service->note }}">
                                                    {{ Str::limit($service->note, 30) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $service->appointments_from_date ? \Carbon\Carbon::parse($service->appointments_from_date)->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <!-- View Button -->
                                                {{-- <button onclick="viewAppointment({{ $service->id }})"
                                                    class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                    title="View Details">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </button> --}}

                                                <!-- Edit Button -->
                                                <button
                                                    onclick="editAppointment({{ $service->id }}, '{{ $service->appointments_name }}', '{{ $service->note ?? '' }}')"
                                                    class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200"
                                                    title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <!-- Release Button -->
                                                <button
                                                    onclick="releaseAppointment({{ $service->id }}, '{{ $service->soldier->full_name ?? 'N/A' }}')"
                                                    class="text-orange-600 hover:text-orange-900 transition-colors duration-200"
                                                    title="Release from Duty">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </button>

                                                <!-- Delete Button -->
                                                <button onclick="deleteAppointment({{ $service->id }})"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                    title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium">No current appointments found</p>
                                                <p class="text-sm">Get started by creating a new appointment</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Previous Appointments Tab -->
            <div id="previous-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Soldier Details
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Appointment
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Period
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="previousTableBody">
                                @forelse($previousServices as $index => $service)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            {{ strtoupper(substr($service->soldier->full_name ?? 'N/A', 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $service->soldier->full_name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $service->soldier->rank->name ?? 'No Rank' }} |
                                                        {{ $service->soldier->company->name ?? 'No Company' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $service->appointments_name }}
                                            </div>
                                            @if ($service->note)
                                                <div class="text-sm text-gray-500 truncate max-w-xs"
                                                    title="{{ $service->note }}">
                                                    {{ Str::limit($service->note, 30) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div>
                                                <div class="text-xs text-gray-500">From:</div>
                                                {{ $service->appointments_from_date ? \Carbon\Carbon::parse($service->appointments_from_date)->format('M d, Y') : 'N/A' }}
                                            </div>
                                            @if ($service->appointments_to_date)
                                                <div class="mt-1">
                                                    <div class="text-xs text-gray-500">To:</div>
                                                    {{ \Carbon\Carbon::parse($service->appointments_to_date)->format('M d, Y') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Completed
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <!-- View Button -->
                                                <button onclick="viewAppointment({{ $service->id }})"
                                                    class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                    title="View Details">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="text-gray-500">
                                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                    </path>
                                                </svg>
                                                <p class="text-lg font-medium">No previous appointments found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Appointment Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Appointment</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form id="editAppointmentForm" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Current Duty Display -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Duty</label>
                        <div id="currentDuty" class="p-3 bg-gray-50 rounded-lg border text-sm text-gray-700"></div>
                    </div>

                    <!-- New Duty Selection -->
                    <div class="mb-4">
                        <label for="appointment_id" class="block text-sm font-medium text-gray-700 mb-1">
                            New Duty Assignment <span class="text-red-500">*</span>
                        </label>
                        <select name="appointment_id" id="appointment_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                            <option value="">Select new duty...</option>
                            @foreach (\App\Models\Appointment::active()->get() as $appointment)
                                <option value="{{ $appointment->id }}">{{ $appointment->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Note -->
                    <div class="mb-6">
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                        <textarea name="note" id="editNote" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            placeholder="Add any additional notes..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200">
                            Update Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Release Confirmation Modal -->
    <div id="releaseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Release from Duty</h3>
                    <button onclick="closeReleaseModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="mb-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-orange-100 rounded-full">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                    </div>
                    <p class="text-center text-gray-700 mb-4">
                        Are you sure you want to release <span id="soldierNameRelease" class="font-semibold"></span> from
                        their current duty?
                    </p>
                    <p class="text-center text-sm text-gray-500">
                        This will move their appointment to previous appointments and they will be available for new
                        assignments.
                    </p>
                </div>

                <!-- Form -->
                <form id="releaseAppointmentForm" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="action" value="release">

                    <!-- Release Note -->
                    <div class="mb-6">
                        <label for="releaseNote" class="block text-sm font-medium text-gray-700 mb-1">Release Note
                            (Optional)</label>
                        <textarea name="release_note" id="releaseNote" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                            placeholder="Reason for release..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeReleaseModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors duration-200">
                            Release from Duty
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript for functionality -->
    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const targetTab = button.getAttribute('data-tab');

                    // Remove active classes
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                        btn.classList.add('border-transparent', 'text-gray-500');
                    });

                    // Add active classes to clicked button
                    button.classList.add('active', 'border-blue-500', 'text-blue-600');
                    button.classList.remove('border-transparent', 'text-gray-500');

                    // Hide all tab contents
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });

                    // Show target tab content
                    document.getElementById(targetTab + '-tab').classList.remove('hidden');
                });
            });

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const activeTab = document.querySelector('.tab-button.active').getAttribute('data-tab');
                const tableBody = document.getElementById(activeTab + 'TableBody');
                const rows = tableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        // Action functions
        function viewAppointment(id) {
            // You can redirect to a view page or show a modal
            // window.location.href = `/appointmanager/${id}`;
        }

        function editAppointment(id, currentDuty, currentNote) {
            // Set form action
            document.getElementById('editAppointmentForm').action = `/appointmanager/${id}`;

            // Set current duty display
            document.getElementById('currentDuty').textContent = currentDuty;

            // Set current note
            document.getElementById('editNote').value = currentNote;

            // Reset appointment selection
            document.getElementById('appointment_id').value = '';

            // Show modal
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function releaseAppointment(id, soldierName) {
            // Set form action
            document.getElementById('releaseAppointmentForm').action = `/appointmanager/${id}/release`;

            // Set soldier name
            document.getElementById('soldierNameRelease').textContent = soldierName;

            // Reset release note
            document.getElementById('releaseNote').value = '';

            // Show modal
            document.getElementById('releaseModal').classList.remove('hidden');
        }

        function closeReleaseModal() {
            document.getElementById('releaseModal').classList.add('hidden');
        }

        function deleteAppointment(id) {
            if (confirm('Are you sure you want to delete this appointment? This action cannot be undone.')) {
                // Create a form and submit it for DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/appointmanager/${id}`;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';

                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const releaseModal = document.getElementById('releaseModal');

            if (event.target === editModal) {
                closeEditModal();
            }
            if (event.target === releaseModal) {
                closeReleaseModal();
            }
        }

        // Handle form submissions with loading states
        document.getElementById('editAppointmentForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
        });

        document.getElementById('releaseAppointmentForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Releasing...';
        });
    </script>
@endsection
