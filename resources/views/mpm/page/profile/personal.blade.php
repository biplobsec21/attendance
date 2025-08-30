@extends('mpm.layouts.app')

@section('title', 'Create Profile')

@section('content')
    {{-- Profile Steps Navigation --}}

    <x-profile-step-nav :steps="$profileSteps" />


    <div class="container mx-auto p-6">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Create Your Profile</h1>
            <p class="text-gray-500">Fill in your details below. Make sure the information is accurate.</p>
        </div>

        <form id="create-profile-form" class="bg-white shadow-lg rounded-lg p-8 space-y-8">

            {{-- Profile Image --}}
            <div class="grid md:grid-cols-12 gap-6 items-center">
                <div class="md:col-span-4">
                    <h2 class="font-semibold text-lg text-gray-700">Profile Image</h2>
                    <p class="text-gray-500 text-sm">Upload a clear, professional headshot for your profile.</p>
                </div>
                <div class="md:col-span-8 flex items-center gap-4">
                    <img id="image-preview" src="https://via.placeholder.com/150"
                        class="w-40 h-40 rounded-full border-2 border-gray-300 object-cover">
                    <label for="profile-image-input"
                        class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors">
                        Upload Image
                    </label>
                    <input type="file" id="profile-image-input" class="hidden" name="image" accept="image/*">
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
                        <label for="army-no" class="block text-sm font-medium text-gray-600 mb-1">Army No.</label>
                        <input id="army-no" type="text" placeholder="e.g., 123456" name="army_no"
                            class="w-full border rounded-md p-3 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="fullName" class="block text-sm font-medium text-gray-600 mb-1">Full Name</label>
                        <input id="fullName" type="text" placeholder="e.g., John M. Doe" name="full_name"
                            class="w-full border rounded-md p-3 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    </div>
                </div>
            </div>

            {{-- Rank & Company --}}
            <div class="grid md:grid-cols-12 gap-6 border-t pt-6">
                <div class="md:col-span-4">
                    <h2 class="font-semibold text-lg text-gray-700">Rank & Company</h2>
                    <p class="text-gray-500 text-sm">Select your current rank and assigned company.</p>
                </div>
                <div class="md:col-span-8 flex flex-col sm:flex-row gap-4">

                    <select name="rank_id"
                        class="w-full sm:w-1/2 p-3 border rounded-md focus:ring-2 focus:ring-orange-500 focus:outline-none">
                        <option value="">Select Rank</option>

                        @foreach ($groupedRanks as $type => $ranksInGroup)
                            <optgroup label="{{ $type }}">
                                @foreach ($ranksInGroup as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach

                    </select>
                    <select
                        class="w-full sm:w-1/2 p-3 border rounded-md focus:ring-2 focus:ring-orange-500 focus:outline-none"
                        name="company_id">
                        <option value="">Select Company</option>
                        @foreach ($company as $cp)
                            <option value="{{ $cp->id }}">{{ $cp->name }}</option>
                        @endforeach
                    </select>
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
                        <label for="mobile-no" class="block text-sm font-medium text-gray-600 mb-1">Mobile No.</label>
                        <input id="mobile-no" type="tel" placeholder="e.g., 01712345678" name="mobile"
                            class="w-full border rounded-md p-3 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="gender-select" class="block text-sm font-medium text-gray-600 mb-1">Gender</label>
                        <select id="gender-select" mame="gender"
                            class="w-full p-3 border rounded-md focus:ring-2 focus:ring-orange-500 focus:outline-none">
                            <option>Select Gender</option>
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>
                    <div>
                        <label for="blood-group-select" class="block text-sm font-medium text-gray-600 mb-1">Blood
                            Group</label>
                        <select id="blood-group-select" name="blood_group"
                            class="w-full p-3 border rounded-md focus:ring-2 focus:ring-orange-500 focus:outline-none">
                            <option>Select Group</option>
                            <option>A+</option>
                            <option>A-</option>
                            <option>B+</option>
                            <option>B-</option>
                            <option>O+</option>
                            <option>O-</option>
                            <option>AB+</option>
                            <option>AB-</option>
                        </select>
                    </div>
                    <div>
                        <label for="marital-status-select" class="block text-sm font-medium text-gray-600 mb-1">Marital
                            Status</label>
                        <select id="marital-status-select" name="marital_status"
                            class="w-full p-3 border rounded-md focus:ring-2 focus:ring-orange-500 focus:outline-none">
                            <option value="">Select Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Widowed">Widowed</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Children Info --}}
            <div id="children-info-container" class="grid md:grid-cols-12 gap-6 border-t pt-6 hidden">
                <div class="md:col-span-4">
                    <h2 class="font-semibold text-lg text-gray-700">Children Information</h2>
                    <p class="text-gray-500 text-sm">Please enter the number of your children.</p>
                </div>
                <div class="md:col-span-8 grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="num-boys" class="block text-sm font-medium text-gray-600 mb-1">Number of Boys</label>
                        <input id="num-boys" type="number" min="0" name="num_boys"
                            class="w-full p-3 border rounded-md focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="num-girls" class="block text-sm font-medium text-gray-600 mb-1">Number of
                            Girls</label>
                        <input id="num-girls" type="number" min="0" name="num_girls"
                            class="w-full p-3 border rounded-md focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    </div>
                </div>
            </div>

            {{-- Location --}}
            <div class="grid md:grid-cols-12 gap-6 border-t pt-6">
                <div class="md:col-span-4">
                    <h2 class="font-semibold text-lg text-gray-700">Location</h2>
                    <p class="text-gray-500 text-sm">Specify your home village and district.</p>
                </div>
                <div class="md:col-span-8 flex flex-col sm:flex-row gap-4">
                    <input id="village" type="text" placeholder="Village" name="village"
                        class="w-full sm:w-1/2 border rounded-md p-3 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    <select id="district" name="district_id"
                        class="w-full sm:w-1/2 border rounded-md p-3 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                        <option>Select District</option>
                        @foreach ($district as $ds)
                            <option value="{{ $ds->id }}">{{ $ds->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Permanent Address --}}
            <div class="grid md:grid-cols-12 gap-6 border-t pt-6">
                <div class="md:col-span-4">
                    <h2 class="font-semibold text-lg text-gray-700">Permanent Address</h2>
                    <p class="text-gray-500 text-sm">Full permanent address including Post Office and Upazila.</p>
                </div>
                <div class="md:col-span-8">
                    <textarea id="permanent-address" rows="4" placeholder="House 123, Vill- ABC, P.O- XYZ"
                        name="permanent_address"
                        class="w-full border rounded-md p-3 focus:ring-2 focus:ring-orange-500 focus:outline-none"></textarea>
                </div>
            </div>

            <div class="text-right">
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-6 rounded-lg transition-all">
                    Save Profile
                </button>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maritalStatusSelect = document.getElementById('marital-status-select');
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

            document.getElementById('create-profile-form').addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Form submitted! Implement your JS submission logic here.');
            });
        });
    </script>
@endpush
