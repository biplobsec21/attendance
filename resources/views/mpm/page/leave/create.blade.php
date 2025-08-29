@extends('mpm.layouts.app')

@section('title', 'Create Leave Request')

@section('content')

<div class="container mx-auto p-4">
    <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Create Leave Request</h1>
        <form id="create-leave-form">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="person-no" class="block text-gray-700 text-sm font-bold mb-2">No <span class="text-red-600">*</span></label>
                    <input type="text" id="person-no" class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Enter No to fetch details" required>
                </div>

                <div class="mb-4">
                    <label for="rank" class="block text-gray-700 text-sm font-bold mb-2">Rank</label>
                    <input type="text" id="rank" class="shadow-sm bg-gray-200 appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none" readonly>
                </div>

                <div class="mb-4">
                    <label for="person-name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" id="person-name" class="shadow-sm bg-gray-200 appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none" readonly>
                </div>

                <div class="mb-4">
                    <label for="company" class="block text-gray-700 text-sm font-bold mb-2">Company</label>
                    <input type="text" id="company" class="shadow-sm bg-gray-200 appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none" readonly>
                </div>

                <div class="mb-4 md:col-span-2">
                    <label for="leave-type" class="block text-gray-700 text-sm font-bold mb-2">Leave Type <span class="text-red-600">*</span></label>
                    <select id="leave-type" class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                        <option value="" disabled selected>Select a leave type</option>
                        <option value="Annual Leave">Annual Leave</option>
                        <option value="Sick Leave">Sick Leave</option>
                        <option value="Compassionate Leave">Compassionate Leave</option>
                        <option value="Unpaid Leave">Unpaid Leave</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="from-date" class="block text-gray-700 text-sm font-bold mb-2">From Date <span class="text-red-600">*</span></label>
                    <input type="date" id="from-date" class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>

                <div class="mb-4">
                    <label for="to-date" class="block text-gray-700 text-sm font-bold mb-2">To Date <span class="text-red-600">*</span></label>
                    <input type="date" id="to-date" class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                </div>

                <div class="mb-6 md:col-span-2">
                    <label for="remark" class="block text-gray-700 text-sm font-bold mb-2">Remark</label>
                    <textarea id="remark" class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Enter any remarks..." rows="3"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="{{ url('leave/index') }}" class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                    Back to List
                </a>
                <button class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors" type="submit">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Sample Profile Data ---
        const profileData = [
            { no: "12345", rank: "Captain", name: "John Doe", company: "Alpha" },
            { no: "54321", rank: "Major", name: "Jane Smith", company: "Bravo" },
            { no: "67890", rank: "Sergeant", name: "Peter Jones", company: "Charlie" }
        ];

        const personNoInput = document.getElementById('person-no');
        const rankInput = document.getElementById('rank');
        const personNameInput = document.getElementById('person-name');
        const companyInput = document.getElementById('company');
        const fromDateInput = document.getElementById('from-date');

        // --- Set default date to today ---
        const today = new Date().toISOString().split('T')[0];
        fromDateInput.value = today;

        // --- Auto-fill fields on 'No' input ---
        personNoInput.addEventListener('blur', function() {
            const no = this.value;
            const profile = profileData.find(p => p.no === no);

            if (profile) {
                rankInput.value = profile.rank;
                personNameInput.value = profile.name;
                companyInput.value = profile.company;
            } else {
                rankInput.value = '';
                personNameInput.value = '';
                companyInput.value = '';
            }
        });

        // --- Form Submission ---
        document.getElementById('create-leave-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const personNo = personNoInput.value;
            const personName = personNameInput.value;
            const leaveType = document.getElementById('leave-type').value;
            const fromDate = fromDateInput.value;
            const toDate = document.getElementById('to-date').value;
            const remark = document.getElementById('remark').value;

            console.log('Form Submitted', {
                personNo,
                personName,
                leaveType,
                fromDate,
                toDate,
                remark
            });
        });
    });
</script>
@endpush
