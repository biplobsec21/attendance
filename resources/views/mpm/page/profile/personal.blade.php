@extends('mpm.layouts.app')

@section('title', $profile ? 'Update Profile' : 'Create Profile')

@section('content')
    {{-- Profile Steps Navigation --}}
    <x-profile-step-nav :steps="$profileSteps" :profileId="$profile->id ?? null" />

    <div class="container mx-auto p-6">
        <div class="grid md:grid-cols-12 gap-6 items-center">
            <div class="md:col-span-7 mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2"> {{ $profile ? 'Update' : 'Create' }} Your Profile</h1>
                <p class="text-gray-500">{{ $profile ? 'Update' : 'Fill' }} in your details below. Make sure the information
                    is
                    accurate.</p>
            </div>
            <div class="md:col-span-5">
                @include('mpm.components.alerts')

            </div>
        </div>

        <form id="create-profile-form"
            action="{{ $profile ? route('profile.updatePersonal', $profile->id) : route('profile.savePersonal') }}"
            method="POST" enctype="multipart/form-data" class="bg-white shadow-lg rounded-lg p-8 space-y-8">
            @csrf
            @if ($profile)
                @method('PUT') {{-- Required for updating --}}
            @endif


            {{-- Profile Image --}}
            <div class="grid md:grid-cols-12 gap-6 items-center">
                <div class="md:col-span-4">
                    <h2 class="font-semibold text-lg text-gray-700">Profile Image</h2>
                    <p class="text-gray-500 text-sm">Upload a clear, professional headshot for your profile.</p>
                </div>
                <div class="md:col-span-8 flex items-center gap-4">

                    <img id="image-preview"
                        src="{{ old('image')
                            ? asset('storage/' . old('image'))
                            : ($profile?->image
                                ? asset('storage/' . $profile->image)
                                : 'https://via.placeholder.com/150') }}"
                        class="w-40 h-40 rounded-full border-2 border-gray-300 object-cover">

                    <label for="profile-image-input"
                        class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors">
                        Upload Image
                    </label>
                    <input type="file" id="profile-image-input" class="hidden" name="image" accept="image/*">
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Name & Army No --}}
            <div class="grid md:grid-cols-12 gap-6 border-t pt-6">
                <div class="md:col-span-4">
                    <h2 class="font-semibold text-lg text-gray-700">Name & Army Number</h2>
                    <p class="text-gray-500 text-sm">Enter your full name and official army number.</p>
                </div>
                <div class="md:col-span-8 grid sm:grid-cols-3 gap-4">
                    <div>
                        <x-form.input name="army_no" label="Army No." placeholder="e.g., 123456" :value="old('army_no') ?? $profile?->army_no" />
                        @error('army_no')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <x-form.input name="full_name" label="Full Name" placeholder="e.g., John M. Doe"
                            :value="old('full_name') ?? $profile?->full_name" />
                        @error('full_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Rank & Company --}}
            <div class="grid md:grid-cols-12 gap-6 border-t pt-6">
                <div class="md:col-span-4">
                    <h2 class="font-semibold text-lg text-gray-700">Rank & Company</h2>
                    <p class="text-gray-500 text-sm">Select your current rank and assigned company.</p>
                </div>
                <div class="md:col-span-8 grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-form.select name="rank_id" label="Rank">
                            <option value="">Select Rank</option>
                            @foreach ($groupedRanks as $type => $ranksInGroup)
                                <optgroup label="{{ $type }}">
                                    @foreach ($ranksInGroup as $r)
                                        <option value="{{ $r->id }}"
                                            {{ (old('rank_id') ?? $profile?->rank_id) == $r->id ? 'selected' : '' }}>
                                            {{ $r->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </x-form.select>
                        @error('rank_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-form.select name="company_id" label="Company">
                            <option value="">Select Company</option>
                            @foreach ($company as $cp)
                                <option value="{{ $cp->id }}"
                                    {{ (old('company_id') ?? $profile?->company_id) == $cp->id ? 'selected' : '' }}>
                                    {{ $cp->name }}
                                </option>
                            @endforeach
                        </x-form.select>
                        @error('company_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Contact & Personal Details --}}
            <div class="grid md:grid-cols-12 gap-6 border-t pt-6">
                <div class="md:col-span-4">
                    <h2 class="font-semibold text-lg text-gray-700">Contact & Personal Details</h2>
                    <p class="text-gray-500 text-sm">Provide your mobile, gender, blood group, and marital status.</p>
                </div>
                <div class="md:col-span-8 grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-form.input name="mobile" label="Mobile No." placeholder="e.g., 01712345678" type="tel"
                            :value="old('mobile') ?? $profile?->mobile" />
                        @error('mobile')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-form.select name="gender" label="Gender">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ (old('gender') ?? $profile?->gender) == 'Male' ? 'selected' : '' }}>
                                Male</option>
                            <option value="Female"
                                {{ (old('gender') ?? $profile?->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        </x-form.select>
                        @error('gender')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-form.select name="blood_group" label="Blood Group">
                            <option value="">Select Group</option>
                            @foreach (['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                <option value="{{ $bg }}"
                                    {{ (old('blood_group') ?? $profile?->blood_group) == $bg ? 'selected' : '' }}>
                                    {{ $bg }}
                                </option>
                            @endforeach
                        </x-form.select>
                        @error('blood_group')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <x-form.select name="marital_status" label="Marital Status" id="marital_status">
                            <option value="">Select Status</option>
                            @foreach (['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                                <option value="{{ $status }}"
                                    {{ (old('marital_status') ?? $profile?->marital_status) == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </x-form.select>
                        @error('marital_status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Children Info --}}
            <div id="children-info-container"
                class="grid md:grid-cols-12 gap-6 border-t pt-6 {{ (old('marital_status') ?? $profile?->marital_status) == 'Married' ? '' : 'hidden' }}">
                <div class="md:col-span-4">
                    <h2 class="font-semibold text-lg text-gray-700">Children Information</h2>
                    <p class="text-gray-500 text-sm">Please enter the number of your children.</p>
                </div>
                <div class="md:col-span-8 grid sm:grid-cols-2 gap-4">
                    <x-form.input name="num_boys" label="Number of Boys" type="number" min="0" :value="old('num_boys', $profile?->num_boys ?? 0)" />
                    <x-form.input name="num_girls" label="Number of Girls" type="number" min="0"
                        :value="old('num_girls', $profile?->num_girls ?? 0)" />
                </div>
            </div>

            {{-- Location --}}
            <div class="grid md:grid-cols-12 gap-6 border-t pt-6">
                <div class="md:col-span-4">
                    <h2 class="font-semibold text-lg text-gray-700">Location</h2>
                    <p class="text-gray-500 text-sm">Specify your home village and district.</p>
                </div>
                <div class="md:col-span-8 flex flex-col sm:flex-row gap-4">
                    <div class="sm:w-1/2">
                        <x-form.input name="village" label="Village" placeholder="Village" :value="old('village') ?? $profile?->village" />
                        @error('village')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:w-1/2">
                        <x-form.select name="district_id" label="District">
                            <option value="">Select District</option>
                            @foreach ($district as $ds)
                                <option value="{{ $ds->id }}"
                                    {{ (old('district_id') ?? $profile?->district_id) == $ds->id ? 'selected' : '' }}>
                                    {{ $ds->name }}
                                </option>
                            @endforeach
                        </x-form.select>
                        @error('district_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Permanent Address --}}
            <div class="grid md:grid-cols-12 gap-6 border-t pt-6">
                <div class="md:col-span-4">
                    <h2 class="font-semibold text-lg text-gray-700">Permanent Address </h2>
                    <p class="text-gray-500 text-sm">Full permanent address including Post Office and Upazila.</p>
                </div>
                <div class="md:col-span-8">
                    <x-form.textarea name="permanent_address" label="Permanent Address" rows="4"
                        placeholder="House 123, Vill- ABC, P.O- XYZ" :value="old('permanent_address') ?? $profile?->permanent_address" />
                    @error('permanent_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="text-right">
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition-all">
                    {{ $profile ? 'Update Profile' : 'Save Profile & Continue' }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maritalStatusSelect = document.getElementById('marital_status');
            const childrenInfoContainer = document.getElementById('children-info-container');

            maritalStatusSelect?.addEventListener('change', function() {
                childrenInfoContainer.classList.toggle('hidden', this.value === 'Single' || !this.value);
            });

            const imageInput = document.getElementById('profile-image-input');
            const imagePreview = document.getElementById('image-preview');

            imageInput?.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = e => imagePreview.src = e.target.result;
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endpush
