@extends('mpm.layouts.app')

@section('title', 'Edit Duty Assignment')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />
        <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full max-w-lg mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Edit Duty Assignment of Rank</h1>

            <form method="POST" action="{{ route('duty.updateAssignment', $assignment->id) }}">
                @csrf
                @method('PUT')

                {{-- Duty Type --}}
                <div class="mb-4">
                    <label for="duty_type" class="block text-gray-700 text-sm font-bold mb-2">Duty Type <span
                            class="text-red-600">*</span></label>
                    <select name="duty_type" id="duty_type"
                        class="shadow-sm border @error('duty_type') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500"
                        required>
                        <option value="fixed" {{ $assignment->duty_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                        <option value="roster" {{ $assignment->duty_type == 'roster' ? 'selected' : '' }}>Rotating</option>
                    </select>
                    @error('duty_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Rank Select --}}
                <div class="mb-4">
                    <label for="rank_id" class="block text-gray-700 text-sm font-bold mb-2">Rank <span
                            class="text-red-600">*</span></label>
                    <select name="rank_id" id="rank_id"
                        class="shadow-sm border @error('rank_id') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500"
                        required>
                        <option value="">Select Rank</option>
                        @foreach ($ranks as $rank)
                            <option value="{{ $rank->id }}" {{ $assignment->rank_id == $rank->id ? 'selected' : '' }}>
                                {{ $rank->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('rank_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Duty Select --}}
                <div class="mb-4">
                    <label for="duty_id" class="block text-gray-700 text-sm font-bold mb-2">Duty <span
                            class="text-red-600">*</span></label>
                    <select name="duty_id" id="duty_id"
                        class="shadow-sm border @error('duty_id') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500"
                        required>
                        <option value="">Select Duty</option>
                        @foreach ($duties as $dutyItem)
                            <option value="{{ $dutyItem->id }}"
                                {{ $assignment->duty_id == $dutyItem->id ? 'selected' : '' }}>
                                {{ $dutyItem->duty_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('duty_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>





                {{-- Soldier Dropdown (only for fixed) --}}
                <div class="mb-4" id="soldier-wrapper"
                    style="{{ $assignment->duty_type == 'fixed' ? 'display:block;' : 'display:none;' }}">
                    <label for="soldier_id" class="block text-gray-700 text-sm font-bold mb-2">
                        Soldier <span class="text-red-600">*</span>
                    </label>
                    <select name="fixed_soldier_id" id="soldier_id"
                        class="shadow-sm border @error('fixed_soldier_id') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500"
                        {{ $assignment->duty_type == 'fixed' ? 'required' : '' }}>
                        <option value="">Select Soldier</option>
                        {{-- Options will be loaded dynamically via JS --}}
                    </select>
                    @error('fixed_soldier_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Manpower --}}
                <div class="mb-4">
                    <label for="manpower" class="block text-gray-700 text-sm font-bold mb-2">Manpower <span
                            class="text-red-600">*</span></label>
                    <input type="number" id="manpower" name="manpower"
                        value="{{ old('manpower', $assignment->manpower) }}" min="1"
                        class="shadow-sm appearance-none border @error('manpower') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Enter number of personnel" required>
                    @error('manpower')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remarks --}}
                <div class="mb-6">
                    <label for="remarks" class="block text-gray-700 text-sm font-bold mb-2">Remarks</label>
                    <textarea name="remarks" id="remarks" rows="3"
                        class="shadow-sm border @error('remarks') border-red-500 @enderror rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('remarks', $assignment->remarks) }}</textarea>
                    @error('remarks')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('duty.assign') }}"
                        class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                        Back
                    </a>
                    <button type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors">
                        Update Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const dutyType = document.getElementById("duty_type");
                const soldierWrapper = document.getElementById("soldier-wrapper");
                const soldierSelect = document.getElementById("soldier_id");
                const rankSelect = document.getElementById("rank_id");
                const manpowerInput = document.getElementById("manpower");

                // Preselected soldier ID for Edit form (0 or null if Create form)
                const preselectedSoldierId = parseInt(
                    '{{ old('fixed_soldier_id', $assignment->fixed_soldier_id ?? 0) }}');

                // Show/hide soldier dropdown and toggle manpower
                function toggleSoldierDropdown() {
                    if (dutyType.value === "fixed") {
                        soldierWrapper.style.display = "block";
                        soldierSelect.setAttribute("required", "required");
                        manpowerInput.value = 1;
                        manpowerInput.setAttribute("readonly", true);
                        loadSoldiers(rankSelect.value);
                    } else {
                        soldierWrapper.style.display = "none";
                        soldierSelect.removeAttribute("required");
                        soldierSelect.innerHTML = '<option value="">Select Soldier</option>';
                        manpowerInput.removeAttribute("readonly");
                        manpowerInput.value = '';
                    }
                }

                // Fetch soldiers by rank and preselect
                function loadSoldiers(rankId) {
                    if (!rankId) {
                        soldierSelect.innerHTML = '<option value="">Select Soldier</option>';
                        return;
                    }

                    fetch(`/soldiers/by-rank/${rankId}`)
                        .then(res => res.json())
                        .then(data => {
                            soldierSelect.innerHTML = '<option value="">Select Soldier</option>';
                            data.forEach(soldier => {
                                const option = document.createElement('option');
                                option.value = soldier.id;
                                option.textContent = soldier.full_name;
                                if (parseInt(soldier.id) === preselectedSoldierId) {
                                    option.selected = true;
                                }
                                soldierSelect.appendChild(option);
                            });
                        })
                        .catch(err => console.error('Failed to load soldiers:', err));
                }

                // Initial load
                toggleSoldierDropdown();

                // Event listeners
                dutyType.addEventListener("change", toggleSoldierDropdown);
                rankSelect.addEventListener("change", function() {
                    if (dutyType.value === "fixed") {
                        loadSoldiers(rankSelect.value);
                    }
                });
            });
        </script>
    @endpush
@endpush
