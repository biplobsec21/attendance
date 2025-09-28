@extends('mpm.layouts.app')

@section('title', 'Profile Details')

@section('content')
    <main class="container mx-auto p-4 sm:p-8 mt-16">

        <div class="flex flex-col lg:flex-row gap-8">

            <!-- Sidebar Profile -->
            <aside class="w-full lg:w-1/3 xl:w-1/4">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24 hover:shadow-xl transition-shadow">
                    <div class="flex flex-col items-center">
                        <img src="{{ asset($profile->image) }}" alt="Profile Image"
                            class="w-32 h-32 rounded-full border-4 border-orange-100 object-cover shadow-md">

                        <h1 class="text-2xl font-bold text-gray-900 mt-4">{{ $profile->full_name ?? 'N/A' }}</h1>
                        <p class="text-orange-600 font-medium">{{ $profile->rank->name ?? 'N/A' }}</p>
                        <p class="text-sm font-mono text-gray-400 mt-1">#{{ $profile->army_no ?? 'N/A' }}</p>
                    </div>

                    <hr class="my-6">

                    <div>
                        <h3 class="font-bold text-gray-600 text-xs uppercase tracking-wider">Key Information</h3>
                        <ul class="mt-4 space-y-3 text-gray-700">
                            <li class="flex items-center"><i class="fas fa-building fa-fw w-6 text-gray-400"></i>
                                <span>{{ $profile->company->name ?? 'N/A' }}</span>
                            </li>
                            <li class="flex items-center"><i class="fas fa-mobile-alt fa-fw w-6 text-gray-400"></i>
                                <span>{{ $profile->mobile ?? 'N/A' }}</span>
                            </li>
                            <li class="flex items-center"><i class="fas fa-tint fa-fw w-6 text-gray-400"></i>
                                <span class="px-2 py-1 rounded-md bg-red-50 text-red-600 text-sm">
                                    {{ $profile->blood_group ?? 'N/A' }}
                                </span>
                            </li>
                            <li class="flex items-center"><i class="fas fa-calendar-check fa-fw w-6 text-gray-400"></i>
                                <span>{{ $profile->joining_date ?? 'N/A' }}</span>
                            </li>
                            <li class="flex items-center"><i class="fas fa-stopwatch fa-fw w-6 text-gray-400"></i>
                                <span class="px-2 py-1 rounded-md bg-blue-50 text-blue-600 text-sm">
                                    {{ $profile->service_duration ?? 'N/A' }}
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <a href="{{ $profile ? route('soldier.personalForm', $profile->id) : '#' }}"
                            class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <button
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fas fa-print mr-2"></i>Print
                        </button>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="w-full lg:w-2/3 xl:w-3/4 space-y-8">

                <!-- Personal Details -->
                <x-profile-section title="Personal Details" icon="fas fa-user-circle">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-gray-700">
                        <div><span class="font-medium text-gray-500">Gender:</span> {{ $profile->gender ?? 'N/A' }}</div>
                        <div><span class="font-medium text-gray-500">Marital Status:</span>
                            {{ $profile->marital_status ?? 'N/A' }}</div>
                        <div><span class="font-medium text-gray-500">Boys:</span> {{ $profile->num_boys ?? 'N/A' }}</div>
                        <div><span class="font-medium text-gray-500">Girls:</span> {{ $profile->num_girls ?? 'N/A' }}</div>
                        <div class="sm:col-span-2"><span class="font-medium text-gray-500">Address:</span>
                            {{ $profile->permanent_address ?? 'N/A' }}</div>
                    </div>
                </x-profile-section>

                <!-- Service History -->
                <x-profile-section title="Service History" icon="fas fa-shield-alt">
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">Current Appointment</h3>
                            <div class="bg-blue-50 p-3 rounded-lg text-blue-700">
                                {{ $current->appointments_name ?? 'N/A' }}
                            </div>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">Previous Appointments</h3>
                            <ul class="relative border-l-2 border-gray-200 pl-4 space-y-3">
                                @forelse($previous as $val)
                                    <li class="relative">
                                        <span class="absolute -left-2 top-1.5 w-3 h-3 bg-orange-400 rounded-full"></span>
                                        <p class="text-gray-800 font-medium">{{ $val->appointments_name }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($val->appointments_from_date)->format('d M Y') }}
                                            -
                                            {{ $val->appointments_to_date ? \Carbon\Carbon::parse($val->appointments_to_date)->format('d M Y') : 'Present' }}
                                        </p>
                                    </li>
                                @empty
                                    <li class="text-gray-500">N/A</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </x-profile-section>

                <!-- Qualifications -->
                <x-profile-section title="Qualifications & Activities" icon="fas fa-graduation-cap">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @include('partials.profile-list', [
                            'title' => 'Education',
                            'items' => $educationsData,
                        ])
                        @include('partials.profile-list', ['title' => 'Courses', 'items' => $coursesData])
                        @include('partials.profile-list', [
                            'title' => 'Co-Curricular',
                            'items' => $cocurricular,
                        ])
                        @include('partials.profile-list', ['title' => 'ERE', 'items' => $ereData])
                    </div>
                </x-profile-section>

                <!-- Medical & Disciplinary -->
                <x-profile-section title="Medical & Disciplinary" icon="fas fa-first-aid">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">Medical Status</h3>
                            <ul class="list-disc list-inside text-gray-600 space-y-1">
                                @forelse($soldierMedicalData as $smd)
                                    <li><strong>Category:</strong> {{ $smd['category'] }} ({{ $smd['start_date'] }} -
                                        {{ $smd['end_date'] }}) <span
                                            class="italic text-gray-500">({{ $smd['remarks'] }})</span></li>
                                @empty <li>N/A</li>
                                @endforelse

                                @forelse($soldierSicknessData as $ssk)
                                    <li><strong>Sickness:</strong> {{ $ssk['category'] }} ({{ $ssk['start_date'] }} -
                                        {{ $ssk['end_date'] }}) <span
                                            class="italic text-gray-500">({{ $ssk['remarks'] }})</span></li>
                                @empty <li>N/A</li>
                                @endforelse
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-2">Disciplinary Records</h3>
                            <ul class="list-disc list-inside text-gray-600 space-y-1">
                                @forelse($goodBehevior as $gb)
                                    <li><strong>Good Behavior:</strong> {{ $gb['name'] }} <span
                                            class="italic text-gray-500">({{ $gb['remarks'] }})</span></li>
                                @empty <li>N/A</li>
                                @endforelse

                                @forelse($badBehavior as $bb)
                                    <li><strong>Punishment:</strong> {{ $bb['name'] }} ({{ $bb['start_date'] }}) <span
                                            class="italic text-gray-500">({{ $bb['remarks'] }})</span></li>
                                @empty <li>None</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </x-profile-section>

            </div>
        </div>
    </main>
@endsection
