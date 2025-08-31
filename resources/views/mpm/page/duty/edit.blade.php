@extends('mpm.layouts.app')

@section('title', 'Edit Duty Record')

@section('content')

<div class="container mx-auto p-4">
    <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full max-w-lg mx-auto">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Edit Duty Record</h1>

        {{-- The form points to the 'duty.update' route and includes the duty's ID --}}
        <form method="POST" action="{{ route('duty.update', $duty->id) }}">
            @csrf
            {{-- Laravel requires the PUT method for updates --}}
            @method('PUT')

            {{-- Duty Name Input --}}
            <div class="mb-4">
                <label for="duty-name" class="block text-gray-700 text-sm font-bold mb-2">Duty Name <span class="text-red-600">*</span></label>
                {{-- The 'value' is pre-filled with the existing duty's name --}}
                <input type="text" id="duty-name" name="duty_name" value="{{ old('duty_name', $duty->duty_name) }}" class="shadow-sm appearance-none border @error('duty_name') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Enter duty name" required>
                @error('duty_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Duty Time Inputs (Start and End) --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Duty Time <span class="text-red-600">*</span></label>
                <div class="flex space-x-4">
                    <div class="w-1/2">
                        <label for="start-time" class="block text-gray-500 text-xs font-bold mb-1">Start Time</label>
                        <input type="time" id="start-time" name="start_time" value="{{ old('start_time', $duty->start_time) }}" class="shadow-sm appearance-none border @error('start_time') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                        @error('start_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="w-1/2">
                        <label for="end-time" class="block text-gray-500 text-xs font-bold mb-1">End Time</label>
                        <input type="time" id="end-time" name="end_time" value="{{ old('end_time', $duty->end_time) }}" class="shadow-sm appearance-none border @error('end_time') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                        @error('end_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Manpower Input --}}
            <div class="mb-4">
                <label for="manpower" class="block text-gray-700 text-sm font-bold mb-2">Manpower <span class="text-red-600">*</span></label>
                <input type="number" id="manpower" name="manpower" value="{{ old('manpower', $duty->manpower) }}" min="1" class="shadow-sm appearance-none border @error('manpower') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Enter number of personnel" required>
                @error('manpower')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remark Input --}}
            <div class="mb-4">
                <label for="remark" class="block text-gray-700 text-sm font-bold mb-2">Remark</label>
                <textarea id="remark" name="remark" class="shadow-sm appearance-none border @error('remark') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Enter any remarks..." rows="3">{{ old('remark', $duty->remark) }}</textarea>
                @error('remark')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Select --}}
            <div class="mb-6">
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status <span class="text-red-600">*</span></label>
                <select id="status" name="status" class="shadow-sm border @error('status') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                    {{-- Logic to select the current status of the duty record --}}
                    <option value="Active" @if(old('status', $duty->status) == 'Active') selected @endif>Active</option>
                    <option value="Inactive" @if(old('status', $duty->status) == 'Inactive') selected @endif>Inactive</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('duty.index') }}" class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                    Back to List
                </a>
                <button class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors" type="submit">
                    Update Record
                </button>
            </div>
        </form>
    </div>
</div>

@endsection