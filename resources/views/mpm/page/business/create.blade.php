@extends('takbir.layouts.app')

@section('title', 'Create Business')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Create New Business</h1>
            <form id="create-business-form">
                <div class="mb-4">
                    <label for="business-name" class="block text-gray-700 text-sm font-bold mb-2">Business Name <span
                            class="text-red-600">*</span></label>
                    <input type="text" id="business-name"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Enter business name" required minlength="4">
                </div>
                <div class="mb-4">
                    <label for="bin" class="block text-gray-700 text-sm font-bold mb-2">BIN</label>
                    <input type="text" id="bin"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Enter BIN">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Enter email address">
                </div>
                <div class="mb-4">
                    <label for="mobile" class="block text-gray-700 text-sm font-bold mb-2">Mobile</label>
                    <input type="tel" id="mobile"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Enter mobile number">
                </div>
                <div class="mb-4">
                    <label for="website" class="block text-gray-700 text-sm font-bold mb-2">Website</label>
                    <input type="url" id="website"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="https://example.com">
                </div>
                <div class="mb-4">
                    <label for="address" class="block text-gray-700 text-sm font-bold mb-2">Address <span
                            class="text-red-600">*</span></label>
                    <textarea id="address" rows="3"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Enter business address" required minlength="4"></textarea>
                </div>
                <div class="mb-4">
                    <label for="opening-balance" class="block text-gray-700 text-sm font-bold mb-2">Opening Balance <span
                            class="text-red-600">*</span></label>
                    <input type="number" id="opening-balance"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Enter opening balance" required>
                </div>
                <div class="mb-6">
                    <label for="logo" class="block text-gray-700 text-sm font-bold mb-2">Logo</label>
                    <input type="file" id="logo"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="mb-6">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status <span
                            class="text-red-600">*</span></label>
                    <select id="status"
                        class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500"
                        required>
                        <option value="" disabled selected>Select Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div class="flex items-center justify-between">
                    <button
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors"
                        type="submit">
                        Save Business
                    </button>
                    <a href="{{ url('/business') }}"
                        class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors">
                        Back to List
                    </a>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('create-business-form').addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData();
                formData.append('business_name', document.getElementById('business-name').value);
                formData.append('bin', document.getElementById('bin').value);
                formData.append('email', document.getElementById('email').value);
                formData.append('mobile', document.getElementById('mobile').value);
                formData.append('website', document.getElementById('website').value);
                formData.append('address', document.getElementById('address').value);
                formData.append('opening_balance', document.getElementById('opening-balance').value);
                formData.append('status', document.getElementById('status').value);

                const logo = document.getElementById('logo').files[0];
                if (logo) {
                    formData.append('logo', logo);
                }

                console.log('Form Submitted');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }

                // Here you would typically send the data to your server
                // For example, using the Fetch API with FormData:
                /*
                fetch('{{ url("/business") }}', {
                method: 'POST',
                    headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    // 'Content-Type': 'multipart/form-data' is not needed, browser sets it
                },
                body: formData
            })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '{{ url("/business") }}';
                    } else {
                        // Handle errors
                        response.json().then(data => console.error('Form submission failed:', data));
                    }
                })
                .catch(error => console.error('Error:', error));
                */
        });
        });
    </script>
@endpush