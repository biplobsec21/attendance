@extends('mpm.layouts.app')

@section('title', 'View Profile')

@section('content')

<div class="container mx-auto p-4">
    <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full">
        <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Profile Details</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column: Profile Image -->
            <div class="md:col-span-1 flex flex-col items-center">
                <img src="{{ asset('asset/image/profile/1.jpg') }}" alt="Profile Image" class="w-40 h-52 rounded-lg object-cover border-2 border-gray-300 mb-4">
                <h2 class="text-xl font-semibold text-gray-800">John Doe</h2>
                <p class="text-gray-600">Captain</p>
            </div>

            <!-- Right Column: Profile Details -->
            <div class="md:col-span-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold">No</label>
                        <p class="text-gray-800">12345</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold">Mobile</label>
                        <p class="text-gray-800">555-0101</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold">Company</label>
                        <p class="text-gray-800">Alpha</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold">Status</label>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Active
                        </span>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold">Course/Cadre</label>
                        <p class="text-gray-800">Course A, Cadre X</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold">Sports</label>
                        <p class="text-gray-800">Football, Basketball</p>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold">Other Qualifications</label>
                        <p class="text-gray-800">Qualification 1</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-8">
            <a href="{{ url('profile/index') }}" class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                Back to List
            </a>
            <a href="#" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors no-underline">
                Edit Profile
            </a>
        </div>
    </div>
</div>

@endsection
