@extends('mpm.layouts.app')

@section('title', 'Create Absent Record')

@section('content')

<div class="container mx-auto p-4">
    <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full max-w-lg mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Create Absent Record</h1>
        <form id="create-absent-form">
            <div class="mb-4">
                <label for="absent-name" class="block text-gray-700 text-sm font-bold mb-2">Absent Name <span class="text-red-600">*</span></label>
                <input type="text" id="absent-name" class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Enter name" required>
            </div>

            <div class="mb-4">
                <label for="remark" class="block text-gray-700 text-sm font-bold mb-2">Remark</label>
                <textarea id="remark" class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Enter any remarks..." rows="3"></textarea>
            </div>

            <div class="mb-6">
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status <span class="text-red-600">*</span></label>
                <select id="status" class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                    <option value="Active" selected>Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="{{ url('absent/index') }}" class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                    Back to List
                </a>
                <button class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors" type="submit">
                    Save Record
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('create-absent-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const absentName = document.getElementById('absent-name').value;
        const remark = document.getElementById('remark').value;
        const status = document.getElementById('status').value;
        console.log('Form Submitted', { absentName, remark, status });
        // Add your form submission logic here
    });
</script>
@endpush
