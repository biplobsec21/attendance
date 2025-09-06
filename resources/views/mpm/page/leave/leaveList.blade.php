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
            <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-lg">
                <table class="min-w-full bg-white divide-y divide-gray-200 rounded-xl">
                    <thead class="bg-black text-white text-sm font-semibold uppercase rounded-t-xl">

                        <tr>
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Profile Info</th>
                            <th class="px-4 py-3 text-left">Apply Date / Type</th>
                            <th class="px-4 py-3 text-left">Days</th>
                            <th class="px-4 py-3 text-left max-w-[200px]">Reason</th>
                            <th class="px-4 py-3 text-center">Application Copy</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100 text-gray-700 text-sm">
                        @foreach ($leaveDatas as $index => $data)
                            <tr
                                class="hover:bg-orange-50 transition-colors duration-200
                        {{ $data->application_current_status == 'rejected' ? 'bg-red-50' : ($data->application_current_status == 'approved' ? 'bg-green-50' : 'bg-yellow-50') }}">


                                <!-- Serial -->
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Profile Info -->
                                <td class="px-4 py-3">
                                    <p class="font-semibold">{{ $data->soldier->full_name }}</p>
                                    <p class="text-gray-500">{{ $data->soldier->rank->name ?? 'N/A' }}</p>
                                    <p class="text-gray-400 text-xs"># {{ $data->soldier->army_no }}</p>
                                </td>

                                <!-- Apply Date / Type -->
                                <td class="px-4 py-3">
                                    <p class="font-semibold">📅 {{ $data->created_at->format('d M Y') }}</p>
                                    <p class="text-gray-600">Type: {{ $data->leaveType->name ?? 'N/A' }}</p>
                                </td>

                                <!-- Days -->
                                <td class="px-4 py-3">
                                    @php
                                        $start = \Carbon\Carbon::parse($data->start_date);
                                        $end = \Carbon\Carbon::parse($data->end_date);
                                        $totalDays = $start->diffInDays($end) + 1;
                                    @endphp
                                    <span
                                        class="px-3 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold text-xs">
                                        {{ $totalDays }} Days
                                    </span>
                                    <p class="text-gray-400 text-xs mt-1">
                                        ({{ $start->format('d/m/Y') }} → {{ $end->format('d/m/Y') }})
                                    </p>
                                </td>

                                <!-- Reason -->
                                <td class="px-4 py-3 max-w-[200px] truncate" title="{{ $data->reason }}">
                                    {{ $data->reason ?? 'N/A' }}
                                </td>

                                <!-- Application Copy -->
                                <td class="px-4 py-3 text-center">
                                    @if ($data->hard_copy)
                                        <img src="{{ asset('storage/' . $data->hard_copy) }}" alt="Application Image"
                                            data-img="{{ asset('storage/' . $data->hard_copy) }}"
                                            class="openImageModal w-20 h-20 rounded-xl object-cover shadow-md mx-auto cursor-pointer hover:opacity-80">
                                    @else
                                        <span class="text-gray-400 italic">N/A</span>
                                    @endif
                                </td>

                                <!-- Status + Change Link -->
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'approved' => 'bg-green-100 text-green-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                        ];

                                        $statusIcons = [
                                            'pending' =>
                                                '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>',
                                            'approved' =>
                                                '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
                                            'rejected' =>
                                                '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
                                        ];

                                        $status = strtolower($data->application_current_status ?? 'pending');
                                    @endphp

                                    <button data-id="{{ $data->id }}" data-status="{{ $status }}"
                                        data-reject_reason="{{ $data->reject_reason ?? '' }}"
                                        class="openStatusModal text-xs hover:underline mt-1 flex items-center justify-center gap-1"
                                        title="{{ $data->reject_reason ?? '' }}">

                                        <span
                                            class="px-3 py-1 rounded-full {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-600' }} font-semibold text-xs flex items-center gap-1">
                                            {!! $statusIcons[$status] !!}
                                            {{ ucfirst($data->application_current_status ?? 'Pending') }}
                                        </span>
                                    </button>
                                </td>



                                <!-- Actions -->
                                <td class="px-4 py-3 flex gap-2 justify-center">
                                    <button
                                        class="editLeaveBtn px-3 py-1 bg-blue-500 text-white rounded-lg text-xs font-semibold hover:bg-blue-600 transition-colors"
                                        data-id="{{ $data->id }}" data-soldier="{{ $data->soldier_id }}"
                                        data-leavetype="{{ $data->leave_type_id }}" data-start="{{ $data->start_date }}"
                                        data-end="{{ $data->end_date }}" data-reason="{{ $data->reason }}"
                                        data-hardcopy="{{ $data->hard_copy }}">
                                        Edit
                                    </button>
                                    <button type="button"
                                        class="deleteBtn px-3 py-1 bg-red-500 text-white rounded-lg text-xs font-semibold hover:bg-red-600 transition-colors"
                                        data-id="{{ $data->id }}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
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
                <input type="hidden" name="leave_id" id="leave_id">
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
                    <label for="application_file" class="block text-sm font-medium text-gray-700">Application Copy</label>
                    <input type="file" name="application_file" id="application_file"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                        accept="image/*">

                    <!-- Preview -->
                    <div id="filePreviewWrapper" class="mt-3 hidden">
                        <p class="text-xs text-gray-500 mb-1">Preview:</p>
                        <div class="relative inline-block">
                            <img id="filePreview" src=""
                                class="w-32 h-32 rounded-lg border border-gray-200 object-cover shadow-md">
                            <button type="button" id="removeFileBtn"
                                class="absolute top-0 right-0 bg-red-500 text-white text-xs px-2 py-1 rounded-full shadow hover:bg-red-600">
                                ✕
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Reason -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea name="reason" rows="3"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"></textarea>
                </div>
                <input type="hidden" name="remove_hard_copy" id="removeHardCopy" value="0">

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
    <!-- Change Status Modal -->
    <div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h2 class="text-lg font-bold text-gray-800">Change Status</h2>
                <button id="closeStatusModal" class="text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            </div>

            <form id="statusForm" method="POST" action="{{ route('leave.changeStatusSubmit') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="leave_id" id="statusLeaveId">

                <div>
                    <label class="block text-sm font-medium text-gray-700">New Status</label>
                    <select name="application_current_status" id="statusSelect"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-400 focus:outline-none"
                        required>
                        <option value="pending">Pending</option>
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="status_reason" class="block text-sm font-medium text-gray-700">
                        Reason <span id="reasonRequiredMark" class="text-red-500 hidden">*</span>
                    </label>
                    <textarea name="status_reason" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-400 focus:border-orange-400 @error('status_reason') border-red-500 @enderror"></textarea>

                    @error('status_reason')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" id="closeStatusModal2"
                        class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-orange-500 text-white font-bold rounded-lg hover:bg-orange-600 transition-colors">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Image Preview Modal -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
        <div class="relative bg-white rounded-xl shadow-lg max-w-3xl p-4">
            <button id="closeImageModal"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-2xl">&times;</button>
            <img id="modalImage" src="" alt="Preview"
                class="max-h-[80vh] max-w-full object-contain rounded-lg shadow">
        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirm Delete</h2>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this leave application? This action cannot be
                undone.</p>

            <div class="flex justify-end gap-3">
                <button id="cancelDelete"
                    class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors">
                        Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('tbody tr').forEach(row => {
            const statusBtn = row.querySelector('.openStatusModal');
            if (statusBtn) {
                const status = statusBtn.dataset.status;
                const reason = statusBtn.dataset.reject_reason;
                if (status === 'rejected' && reason.trim() !== '') {
                    row.classList.add('bg-red-50'); // highlight row
                    statusBtn.classList.add('bg-red-200', 'text-red-800'); // highlight button
                    statusBtn.title = reason;
                }
            }
        });

        // =================== LEAVE MODAL ===================
        const openBtn = document.getElementById('openLeaveModal');
        const closeBtn = document.getElementById('closeLeaveModal');
        const closeBtn2 = document.getElementById('closeLeaveModal2');
        const modal = document.getElementById('leaveModal');

        openBtn.addEventListener('click', () => modal.classList.remove('hidden'));
        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
        closeBtn2.addEventListener('click', () => modal.classList.add('hidden'));

        // =================== AUTO CALCULATE DAYS ===================
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

        // =================== FORM VALIDATION ===================
        leaveForm.addEventListener('submit', function(e) {
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

            submitBtn.disabled = true;
            submitBtn.innerText = 'Submitting...';
        });

        // =================== STATUS MODAL ===================
        const statusModal = document.getElementById('statusModal');
        const closeStatusModal = document.getElementById('closeStatusModal');
        const closeStatusModal2 = document.getElementById('closeStatusModal2');
        const statusLeaveId = document.getElementById('statusLeaveId');
        const statusSelect = document.getElementById('statusSelect');
        const statusForm = document.getElementById('statusForm');
        const statusReason = statusForm.querySelector('textarea[name="status_reason"]');
        const reasonMark = document.getElementById('reasonRequiredMark');

        document.querySelectorAll('.openStatusModal').forEach(btn => {
            const status = btn.dataset.status;
            const reason = btn.dataset.reject_reason;
            if (status === 'rejected' && reason.trim() !== '') {
                btn.classList.add('bg-red-200', 'text-red-800');
                btn.title = reason;
            }

            btn.addEventListener('click', () => {
                statusLeaveId.value = btn.dataset.id;
                statusSelect.value = status;
                statusReason.value = reason;
                statusReason.classList.remove('border-red-500');
                reasonMark.classList.add('hidden');
                statusModal.classList.remove('hidden');
                statusSelect.dispatchEvent(new Event('change'));
            });
        });

        [closeStatusModal, closeStatusModal2].forEach(btn => {
            btn.addEventListener('click', () => statusModal.classList.add('hidden'));
        });

        statusSelect.addEventListener('change', () => {
            if (statusSelect.value === 'rejected') {
                reasonMark.classList.remove('hidden');
                statusReason.classList.add('border-red-500');
                statusReason.setAttribute('required', 'required');
            } else {
                reasonMark.classList.add('hidden');
                statusReason.classList.remove('border-red-500');
                statusReason.removeAttribute('required');
            }
        });

        statusReason.addEventListener('input', () => {
            if (statusSelect.value === 'rejected') {
                if (statusReason.value.trim() === '') {
                    statusReason.classList.add('border-red-500');
                } else {
                    statusReason.classList.remove('border-red-500');
                }
            }
        });

        statusForm.addEventListener('submit', function(e) {
            if (statusSelect.value === 'rejected' && statusReason.value.trim() === '') {
                e.preventDefault();
                alert('Please provide a reason for rejection.');
                statusReason.focus();
            }
        });

        // =================== IMAGE MODAL ===================
        const imageModal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const closeImageModal = document.getElementById('closeImageModal');

        document.querySelectorAll('.openImageModal').forEach(img => {
            img.addEventListener('click', () => {
                modalImage.src = img.dataset.img;
                imageModal.classList.remove('hidden');
            });
        });
        closeImageModal.addEventListener('click', () => imageModal.classList.add('hidden'));

        // =================== EDIT ===================
        document.querySelectorAll('.editLeaveBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('leave_id').value = btn.dataset.id;
                document.querySelector('[name="soldier_id"]').value = btn.dataset.soldier;
                document.querySelector('[name="leave_type_id"]').value = btn.dataset.leavetype;
                document.getElementById('fromDate').value = btn.dataset.start;
                document.getElementById('endDate').value = btn.dataset.end;
                document.querySelector('[name="reason"]').value = btn.dataset.reason ?? '';
                calculateDays();
                if (btn.dataset.hardcopy) {
                    document.getElementById('filePreview').src = `/storage/${btn.dataset.hardcopy}`;
                    document.getElementById('filePreviewWrapper').classList.remove('hidden');
                } else {
                    document.getElementById('filePreview').src = "";
                    document.getElementById('filePreviewWrapper').classList.add('hidden');
                }
                document.querySelector('#leaveModal h2').innerText = "Edit Leave Application";
                submitBtn.innerText = "Update";
                leaveForm.action = "{{ route('leave.update', ':id') }}".replace(':id', btn.dataset.id);
                leaveForm.method = "POST";
                if (!document.getElementById('_method')) {
                    const methodInput = document.createElement('input');
                    methodInput.type = "hidden";
                    methodInput.name = "_method";
                    methodInput.value = "PUT";
                    methodInput.id = "_method";
                    leaveForm.appendChild(methodInput);
                } else {
                    document.getElementById('_method').value = "PUT";
                }
                modal.classList.remove('hidden');
            });
        });

        // =================== FILE PREVIEW ===================
        document.getElementById('application_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('filePreview').src = event.target.result;
                    document.getElementById('filePreviewWrapper').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
        document.getElementById('removeFileBtn').addEventListener('click', function() {
            document.getElementById('application_file').value = "";
            document.getElementById('filePreview').src = "";
            document.getElementById('filePreviewWrapper').classList.add('hidden');
            document.getElementById('removeHardCopy').value = "1";
        });

        openBtn.addEventListener('click', () => {
            leaveForm.reset();
            document.getElementById('leave_id').value = '';
            document.querySelector('#leaveModal h2').innerText = "New Leave Application";
            submitBtn.innerText = "Submit";
            leaveForm.action = "{{ route('leave.leaveApplicationSubmit') }}";
            document.getElementById('filePreview').src = "";
            document.getElementById('filePreviewWrapper').classList.add('hidden');
            if (document.getElementById('_method')) {
                document.getElementById('_method').remove();
            }
        });

        // =================== DELETE MODAL ===================
        const deleteBtns = document.querySelectorAll('.deleteBtn');
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const cancelDelete = document.getElementById('cancelDelete');

        deleteBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const leaveId = btn.dataset.id;
                deleteForm.action = `/leave/${leaveId}`; // set action dynamically
                deleteModal.classList.remove('hidden');
            });
        });

        cancelDelete.addEventListener('click', () => {
            deleteModal.classList.add('hidden');
        });

        deleteModal.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                deleteModal.classList.add('hidden');
            }
        });
    </script>
@endpush
