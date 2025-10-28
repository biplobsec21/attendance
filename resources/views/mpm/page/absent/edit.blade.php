@extends('mpm.layouts.app')

@section('title', 'Edit Absent Record')

@section('content')

    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />
        <!-- Alert Messages -->
        @include('mpm.components.alerts')

        <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full max-w-lg mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Edit Absent Record</h1>

            <form method="POST" action="{{ route('absents.update', $absent) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                        Name <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $absent->name) }}"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 @error('name') border-red-500 @enderror"
                        placeholder="Enter name" required>
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="absent_date" class="block text-gray-700 text-sm font-bold mb-2">
                        Absent Date <span class="text-red-600">*</span>
                    </label>
                    <input type="date" id="absent_date" name="absent_date"
                        value="{{ old('absent_date', $absent->absent_date->format('Y-m-d')) }}"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 @error('absent_date') border-red-500 @enderror"
                        required>
                    @error('absent_date')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="reason" class="block text-gray-700 text-sm font-bold mb-2">
                        Reason <span class="text-red-600">*</span>
                    </label>
                    <textarea id="reason" name="reason" rows="4"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 @error('reason') border-red-500 @enderror"
                        placeholder="Enter reason for absence" required>{{ old('reason', $absent->reason) }}</textarea>
                    @error('reason')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">
                        Status <span class="text-red-600">*</span>
                    </label>
                    <select id="status" name="status"
                        class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 @error('status') border-red-500 @enderror"
                        required>
                        <option value="1" {{ old('status', $absent->status ? '1' : '0') == '1' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="0" {{ old('status', $absent->status ? '1' : '0') == '0' ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('absents.index') }}"
                        class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                        Back to List
                    </a>
                    <button
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors"
                        type="submit">
                        Update Absent Record
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
