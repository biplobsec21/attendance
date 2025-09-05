@extends('mpm.layouts.app')

@section('title', 'Leave List')

@section('content')
    @include('mpm.components.leave-nav')
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container mx-auto p-4">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 formBack">

            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">Leave Application List</h1>

                <!-- Button to open modal -->
                <button id="openLeaveModal"
                    class="flex items-center gap-2 px-4 py-2 border border-orange-400 text-black font-bold rounded-lg
                           hover:bg-orange-50 hover:border-orange-500
                           transition-colors duration-200">
                    + Add new Leave application
                </button>
            </div>

            <!-- Leave Table -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table id="profiles-table" class="min-w-full bg-white divide-y divide-gray-200 rounded-lg">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-sm font-medium">
                        <tr>
                            <th class="px-4 py-3">SI</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Apply Date</th>
                            <th class="px-4 py-3">Start Date</th>
                            <th class="px-4 py-3">End Date</th>
                            <th class="px-4 py-3">Reason</th>
                            <th class="px-4 py-3">Days</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100 text-gray-700 text-sm">
                        <!-- Data will be populated by DataTables -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="pagination-controls" class="flex justify-center items-center mt-6 space-x-2 flex-wrap"></div>
        </div>
    </div>

    <!-- Leave Modal -->
    <div id="leaveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h2 class="text-xl font-bold text-gray-800">New Leave Application</h2>
                <button id="closeLeaveModal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>

            <!-- Modal Form -->
            <form id="leaveForm" action="{{ route('leave.leaveApplicationSubmit') }}" method="POST"
                enctype="multipart/form-data" class="space-y-4">
                @csrf
                <!-- Profile Dropdown -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Profile Name *</label>
                    <select name="soldier_id"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"
                        required>
                        <option value="">-- Select Profile --</option>
                        @foreach ($profiles as $profile)
                            <option value="{{ $profile->id }}">{{ $profile->full_name }}
                                {{ $profile->army_no ? $profile->army_no : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Leave Type Dropdown -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Leave Type *</label>
                    <select name="leave_type_id"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"
                        required>
                        <option value="">-- Select Leave Type --</option>
                        @foreach ($leaveType as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">From Date *</label>
                        <input type="date" id="fromDate" name="start_date"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date *</label>
                        <input type="date" id="endDate" name="end_date"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"
                            required>
                    </div>
                </div>

                <!-- Total Days -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Days</label>
                    <input type="number" id="totalDays" name="total_days"
                        class="w-full border rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed" readonly>
                </div>

                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Application Hard Copy</label>
                    <input type="file" name="application_file"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none">
                </div>

                <!-- Reason -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea name="reason" rows="3"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"></textarea>
                </div>

                <!-- Modal Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" id="closeLeaveModal2"
                        class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-orange-500 text-white font-bold rounded-lg hover:bg-orange-600 transition-colors">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Modal open/close handling
        const openBtn = document.getElementById('openLeaveModal');
        const closeBtn = document.getElementById('closeLeaveModal');
        const closeBtn2 = document.getElementById('closeLeaveModal2');
        const modal = document.getElementById('leaveModal');

        openBtn.addEventListener('click', () => modal.classList.remove('hidden'));
        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
        closeBtn2.addEventListener('click', () => modal.classList.add('hidden'));

        // Auto-calculate total days
        const fromDateEl = document.getElementById('fromDate');
        const endDateEl = document.getElementById('endDate');
        const totalDaysEl = document.getElementById('totalDays');
        const leaveForm = document.getElementById('leaveForm');
        const submitBtn = leaveForm.querySelector('button[type="submit"]');

        function calculateDays() {
            const fromDate = new Date(fromDateEl.value);
            const endDate = new Date(endDateEl.value);

            if (!isNaN(fromDate) && !isNaN(endDate) && endDate >= fromDate) {
                const diffTime = endDate - fromDate;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // include both dates
                totalDaysEl.value = diffDays;
            } else {
                totalDaysEl.value = '';
            }
        }

        fromDateEl.addEventListener('change', calculateDays);
        endDateEl.addEventListener('change', calculateDays);

        // Form validation & prevent double submit
        leaveForm.addEventListener('submit', function(e) {
            // Basic JS validation
            if (!leaveForm.soldier_id.value) {
                e.preventDefault();
                alert('Please select a Profile.');
                leaveForm.soldier_id.focus();
                return false;
            }
            if (!leaveForm.leave_type_id.value) {
                e.preventDefault();
                alert('Please select a Leave Type.');
                leaveForm.leave_type_id.focus();
                return false;
            }
            if (!fromDateEl.value) {
                e.preventDefault();
                alert('Please select a From Date.');
                fromDateEl.focus();
                return false;
            }
            if (!endDateEl.value) {
                e.preventDefault();
                alert('Please select an End Date.');
                endDateEl.focus();
                return false;
            }
            if (!totalDaysEl.value || totalDaysEl.value <= 0) {
                e.preventDefault();
                alert('Total days must be greater than 0.');
                return false;
            }

            // Disable submit button to prevent double submit
            submitBtn.disabled = true;
            submitBtn.innerText = 'Submitting...';
        });
    </script>
@endpush
