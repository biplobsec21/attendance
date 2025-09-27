@extends('mpm.layouts.app')

@section('title', 'Settings Page')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Settings</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Profile Settings Card --}}
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition">
                <div class="px-6 py-4 rounded-t-2xl bg-gradient-to-r from-orange-400 to-orange-500 flex items-center gap-2">
                    <i class="fas fa-user-circle text-white text-lg"></i>
                    <h3 class="text-white font-semibold text-lg">Profile Settings</h3>
                </div>
                <ul class="px-6 py-4 space-y-2 text-sm grid grid-cols-1 md:grid-cols-2 gap-2 max-h-96 overflow-y-auto">
                    <li>
                        <a href="{{ route('ranks.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 rounded-lg transition">
                            <i class="fas fa-tasks text-orange-500"></i> Rank
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('companies.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 rounded-lg transition">
                            <i class="fas fa-building text-orange-500"></i> Company
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('courses.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 rounded-lg transition">
                            <i class="fas fa-book text-orange-500"></i> Courses
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cadres.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 rounded-lg transition">
                            <i class="fas fa-user-tie text-orange-500"></i> Cadre
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('skill.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 rounded-lg transition">
                            <i class="fas fa-lightbulb text-orange-500"></i> Skill
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('skillcategory.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 rounded-lg transition">
                            <i class="fas fa-tags text-orange-500"></i> Skill Category
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('education.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 rounded-lg transition">
                            <i class="fas fa-graduation-cap text-orange-500"></i> Education
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('atts.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 rounded-lg transition">
                            <i class="fas fa-check-circle text-orange-500"></i> ATT
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('eres.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 rounded-lg transition">
                            <i class="fas fa-check-circle text-orange-500"></i> ERE
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('filters.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 rounded-lg transition">
                            <i class="fas fa-filter text-orange-500"></i> Filters
                        </a>
                    </li>
                </ul>
            </div>


            {{-- Duty Settings Card --}}
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition">
                <div class="px-6 py-4 rounded-t-2xl bg-gradient-to-r from-blue-400 to-blue-500">
                    <h3 class="text-white font-semibold text-lg flex items-center gap-2">
                        <i class="fas fa-user-shield"></i> Duty Settings
                    </h3>
                </div>
                <ul class="px-6 py-4 space-y-2 text-sm">
                    <li><a href="{{ route('duty.index') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition">
                            <i class="fas fa-tasks text-blue-500"></i> Duty
                        </a></li>
                    {{-- <li><a href="{{ route('duty.assigntorank') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition">
                            <i class="fas fa-layer-group text-blue-500"></i> Assign to Rank
                        </a></li> --}}
                    <li><a href="{{ route('appointments.index') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition">
                            <i class="fas fa-layer-group text-blue-500"></i> Appointments
                        </a></li>
                </ul>
            </div>

            {{-- Role & Permissions Card --}}
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition">
                <div class="px-6 py-4 rounded-t-2xl bg-gradient-to-r from-green-400 to-green-500">
                    <h3 class="text-white font-semibold text-lg flex items-center gap-2">
                        <i class="fas fa-user-lock"></i> Role & Permissions
                    </h3>
                </div>
                <ul class="px-6 py-4 space-y-2 text-sm">
                    <li><a href="{{ route('roles.index') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-green-50 hover:text-green-600 transition">
                            <i class="fas fa-user-tag text-green-500"></i> Roles
                        </a></li>
                    <li><a href="{{ route('permissions.index') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-green-50 hover:text-green-600 transition">
                            <i class="fas fa-key text-green-500"></i> Permissions
                        </a></li>
                </ul>
            </div>

            {{-- User Management Card --}}
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition">
                <div class="px-6 py-4 rounded-t-2xl bg-gradient-to-r from-purple-400 to-purple-500">
                    <h3 class="text-white font-semibold text-lg flex items-center gap-2">
                        <i class="fas fa-users-cog"></i> User Management
                    </h3>
                </div>
                <ul class="px-6 py-4 space-y-2 text-sm">
                    <li>
                        <a href="{{ route('users.index') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition">
                            <i class="fas fa-user text-purple-500"></i> User List
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.create') }}"
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-purple-50 hover:text-purple-600 transition">
                            <i class="fas fa-user text-purple-500"></i> User Create
                        </a>
                    </li>

                </ul>
            </div>

        </div>
    </div>
@endsection
