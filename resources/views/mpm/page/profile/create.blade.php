@extends('mpm.layouts.app')

@section('title', 'Create Profile')

@section('content')

<div class="container mx-auto p-4">
    <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full ">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Create New Profile</h1>
            <form id="create-profile-form">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Image Input and Viewer -->
                    <div class="mb-4 md:col-span-2 flex flex-col items-center">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Profile Image</label>
                        <img id="image-preview" src="https://via.placeholder.com/150" alt="Image Preview" class="w-32 h-40 rounded-lg object-cover border-2 border-gray-300 mb-4 cursor-pointer" onclick="document.getElementById('profile-image-input').click();">
                        <input type="file" id="profile-image-input" class="hidden" accept="image/*">
                        <label for="profile-image-input" class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-lg transition-colors">
                            Upload Image
                        </label>
                    </div>

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name <span class="text-red-600">*</span></label>
                        <input type="text" id="name" class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Enter name" required>
                    </div>

                    <div class="mb-4">
                        <label for="rank" class="block text-gray-700 text-sm font-bold mb-2">RANK <span class="text-red-600">*</span></label>
                        <select id="rank" class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                            <option value="" disabled selected>Select Rank</option>
                            <option value="Private">Private</option>
                            <option value="Corporal">Corporal</option>
                            <option value="Sergeant">Sergeant</option>
                            <option value="Lieutenant">Lieutenant</option>
                            <option value="Captain">Captain</option>
                            <option value="Major">Major</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="no" class="block text-gray-700 text-sm font-bold mb-2">No <span class="text-red-600">*</span></label>
                        <input type="text" id="no" class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Enter number" required>
                    </div>

                    <div class="mb-4">
                        <label for="mobile" class="block text-gray-700 text-sm font-bold mb-2">Mobile</label>
                        <input type="text" id="mobile" class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Enter mobile number">
                    </div>

                    <div class="mb-4">
                        <label for="company" class="block text-gray-700 text-sm font-bold mb-2">Company <span class="text-red-600">*</span></label>
                        <select id="company" class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                            <option value="" disabled selected>Select Company</option>
                            <option value="Alpha">Alpha</option>
                            <option value="Bravo">Bravo</option>
                            <option value="Charlie">Charlie</option>
                            <option value="Delta">Delta</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="course-cadre" class="block text-gray-700 text-sm font-bold mb-2">Course/Cadre <span class="text-red-600">*</span></label>
                        <select id="course-cadre" multiple class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                            <option value="Course A">Course A</option>
                            <option value="Course B">Course B</option>
                            <option value="Cadre X">Cadre X</option>
                            <option value="Cadre Y">Cadre Y</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="sports" class="block text-gray-700 text-sm font-bold mb-2">Sports</label>
                        <select id="sports" multiple class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="Football">Football</option>
                            <option value="Basketball">Basketball</option>
                            <option value="Cricket">Cricket</option>
                            <option value="Hockey">Hockey</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="other-qual" class="block text-gray-700 text-sm font-bold mb-2">Other Qual</label>
                        <select id="other-qual" multiple class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="Qualification 1">Qualification 1</option>
                            <option value="Qualification 2">Qualification 2</option>
                            <option value="Qualification 3">Qualification 3</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status <span class="text-red-600">*</span></label>
                        <select id="status" class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                            <option value="" disabled selected>Select Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ url('profile/index') }}" class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors">
                        Back to List
                    </a>
                    <button class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors" type="submit">
                        Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('profile-image-input');
        const imagePreview = document.getElementById('image-preview');

        // --- Image Preview Logic ---
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.setAttribute('src', event.target.result);
                }
                reader.readAsDataURL(file);
            }
        });


        // Helper function to get all selected options from a multi-select dropdown
        function getSelectedOptions(select) {
            const options = select.options;
            const selectedValues = [];
            for (let i = 0; i < options.length; i++) {
                if (options[i].selected) {
                    selectedValues.push(options[i].value);
                }
            }
            return selectedValues;
        }

        // --- Form Submission ---
        document.getElementById('create-profile-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const imageFile = imageInput.files[0];
            const rank = document.getElementById('rank').value;
            const no = document.getElementById('no').value;
            const name = document.getElementById('name').value;
            const mobile = document.getElementById('mobile').value;
            const company = document.getElementById('company').value;
            const status = document.getElementById('status').value;
            const courseCadre = getSelectedOptions(document.getElementById('course-cadre'));
            const sports = getSelectedOptions(document.getElementById('sports'));
            const otherQual = getSelectedOptions(document.getElementById('other-qual'));

            console.log({
                imageFile,
                rank,
                no,
                name,
                mobile,
                company,
                status,
                courseCadre,
                sports,
                otherQual
            });

        });
    });
</script>
@endpush
