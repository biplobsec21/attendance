<nav
    class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 text-white p-0 fixed top-0 w-full z-50 shadow-2xl backdrop-blur-md border-b border-white/10">
    <div class="container mx-auto px-6">
        <div class="flex items-center h-16">
            <!-- Logo/Brand - Left aligned -->
            <div class="flex items-center space-x-3 mr-auto">
                <div
                    class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
                <a href="#"
                    class="text-xl font-bold bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent hover:from-blue-100 hover:to-indigo-100 transition-all duration-300">
                    Military Records
                </a>
            </div>

            <!-- Navigation Links and Actions - Right aligned -->
            <div class="flex items-center space-x-1">
                <!-- Navigation Links -->
                <div class="hidden lg:flex items-center space-x-1">
                    <a href="{{ route('dashboard.index') }}"
                        class="group relative px-4 py-2 rounded-xl font-medium transition-all duration-300 {{ Request::segment(1) === 'dashboard' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-500/25' : 'hover:bg-white/10 hover:text-blue-200' }}">
                        <span class="relative z-10 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                            </svg>
                            <span>Dashboard</span>
                        </span>
                        @if (Request::segment(1) !== 'dashboard')
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-indigo-500/0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        @endif
                    </a>

                    <a href="{{ route('report.index') }}"
                        class="group relative px-4 py-2 rounded-xl font-medium transition-all duration-300 {{ Request::segment(1) === 'report' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-500/25' : 'hover:bg-white/10 hover:text-blue-200' }}">
                        <span class="relative z-10 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span>Report</span>
                        </span>
                        @if (Request::segment(1) !== 'report')
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-indigo-500/0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        @endif
                    </a>

                    <a href="{{ route('soldier.index') }}"
                        class="group relative px-4 py-2 rounded-xl font-medium transition-all duration-300 {{ Request::segment(1) === 'army' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-500/25' : 'hover:bg-white/10 hover:text-blue-200' }}">
                        <span class="relative z-10 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Profile</span>
                        </span>
                        @if (Request::segment(1) !== 'army')
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-indigo-500/0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        @endif
                    </a>

                    <a href="{{ route('leave.index') }}"
                        class="group relative px-4 py-2 rounded-xl font-medium transition-all duration-300 {{ Request::segment(1) === 'leave' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-500/25' : 'hover:bg-white/10 hover:text-blue-200' }}">
                        <span class="relative z-10 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3z">
                                </path>
                            </svg>
                            <span>Leave</span>
                        </span>
                        @if (Request::segment(1) !== 'leave')
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-indigo-500/0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        @endif
                    </a>

                    <a href="{{ route('appointmanager.index') }}"
                        class="group relative px-4 py-2 rounded-xl font-medium transition-all duration-300 {{ Request::segment(1) === 'appointmanager' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-500/25' : 'hover:bg-white/10 hover:text-blue-200' }}">
                        <span class="relative z-10 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <span>Appointment</span>
                        </span>
                        @if (Request::segment(1) !== 'appointmanager')
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-indigo-500/0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        @endif
                    </a>

                    <a href="{{ route('coursecadremanager.index') }}"
                        class="group relative px-4 py-2 rounded-xl font-medium transition-all duration-300 {{ Request::segment(1) === 'coursecadremanager' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-500/25' : 'hover:bg-white/10 hover:text-blue-200' }}">
                        <span class="relative z-10 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                            <span>Training</span>
                        </span>
                        @if (Request::segment(1) !== 'coursecadremanager')
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-indigo-500/0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        @endif
                    </a>

                    <a href="{{ route('settings') }}"
                        class="group relative px-4 py-2 rounded-xl font-medium transition-all duration-300 {{ Request::segment(1) === 'settings' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-500/25' : 'hover:bg-white/10 hover:text-blue-200' }}">
                        <span class="relative z-10 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Settings</span>
                        </span>
                        @if (Request::segment(1) !== 'settings')
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-indigo-500/0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        @endif
                    </a>

                    <a href="{{ route('assignments.generateForm') }}"
                        class="group relative px-4 py-2 rounded-xl font-medium transition-all duration-300 {{ Request::segment(1) === 'assignments' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-500/25' : 'hover:bg-white/10 hover:text-blue-200' }}">
                        <span class="relative z-10 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                            <span>Assignments</span>
                        </span>
                        @if (Request::segment(1) !== 'assignments')
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-indigo-500/0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        @endif
                    </a>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center space-x-4 ml-4">
                    <!-- User Profile/Avatar (Optional) -->
                    {{-- <div class="hidden md:flex items-center space-x-2 px-3 py-1 bg-white/10 rounded-full">
                        <div
                            class="w-8 h-8 bg-gradient-to-r from-blue-400 to-indigo-400 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-blue-100">Admin</span>
                    </div> --}}

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="group flex items-center space-x-2 px-4 py-2 bg-gradient-to-r  hover:from-red-600 hover:to-red-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-red-500/25">
                            <svg class="w-4 h-4 group-hover:rotate-12 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                        </button>
                    </form>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button"
                        class="lg:hidden p-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu"
            class="lg:hidden hidden bg-slate-800/95 backdrop-blur-md rounded-2xl mt-2 p-4 border border-white/10 shadow-2xl">
            <div class="space-y-2">
                <a href="{{ route('dashboard.index') }}"
                    class="block px-4 py-3 rounded-xl {{ Request::segment(1) === 'dashboard' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        <span>Dashboard</span>
                    </div>
                </a>
                <a href="{{ route('report.index') }}"
                    class="block px-4 py-3 rounded-xl {{ Request::segment(1) === 'report' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span>Report</span>
                    </div>
                </a>
                <a href="{{ route('soldier.index') }}"
                    class="block px-4 py-3 rounded-xl {{ Request::segment(1) === 'army' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Profile</span>
                    </div>
                </a>
                <a href="{{ route('leave.index') }}"
                    class="block px-4 py-3 rounded-xl {{ Request::segment(1) === 'leave' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3z">
                            </path>
                        </svg>
                        <span>Leave</span>
                    </div>
                </a>
                <a href="{{ route('appointmanager.index') }}"
                    class="block px-4 py-3 rounded-xl {{ Request::segment(1) === 'appointmanager' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span>Appointment</span>
                    </div>
                </a>
                <a href="{{ route('coursecadremanager.index') }}"
                    class="block px-4 py-3 rounded-xl {{ Request::segment(1) === 'coursecadremanager' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                        <span>Training</span>
                    </div>
                </a>
                <a href="{{ route('settings') }}"
                    class="block px-4 py-3 rounded-xl {{ Request::segment(1) === 'settings' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Settings</span>
                    </div>
                </a>
                <a href="{{ route('assignments.generateForm') }}"
                    class="block px-4 py-3 rounded-xl {{ Request::segment(1) === 'assignments' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        <span>Assignments</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</nav>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    mobileMenu.classList.toggle('hidden');

                    // Animate hamburger to X
                    const icon = mobileMenuButton.querySelector('svg');
                    if (mobileMenu.classList.contains('hidden')) {
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
                    } else {
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
                    }
                });

                // Close mobile menu when clicking outside
                window.addEventListener('click', (e) => {
                    if (!mobileMenuButton.contains(e.target) && !mobileMenu.contains(e.target)) {
                        mobileMenu.classList.add('hidden');
                        const icon = mobileMenuButton.querySelector('svg');
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
                    }
                });

                // Close mobile menu when window is resized to desktop
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 1024) {
                        mobileMenu.classList.add('hidden');
                        const icon = mobileMenuButton.querySelector('svg');
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
                    }
                });
            }

            // Add scroll effect to navigation
            let lastScrollY = window.scrollY;
            window.addEventListener('scroll', () => {
                const nav = document.querySelector('nav');
                if (window.scrollY > lastScrollY && window.scrollY > 100) {
                    // Scrolling down
                    nav.style.transform = 'translateY(-100%)';
                } else {
                    // Scrolling up
                    nav.style.transform = 'translateY(0)';
                }
                lastScrollY = window.scrollY;
            });
        });
    </script>
@endpush
