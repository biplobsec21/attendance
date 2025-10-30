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

                    <a href="{{ route('absent.index') }}"
                        class="group relative px-4 py-2 rounded-xl font-medium transition-all duration-300 {{ Request::segment(1) === 'absent' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-500/25' : 'hover:bg-white/10 hover:text-blue-200' }}">
                        <span class="relative z-10 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                            <span>Absent</span>
                        </span>
                        @if (Request::segment(1) !== 'absent')
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
                            <span>Appt</span>
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

                    <a href="{{ route('duty-assignments.index') }}"
                        class="group relative px-4 py-2 rounded-xl font-medium transition-all duration-300 {{ Request::segment(1) === 'duty-assignments' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-lg shadow-blue-500/25' : 'hover:bg-white/10 hover:text-blue-200' }}">
                        <span class="relative z-10 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <span>Duty Assignment</span>
                        </span>
                        @if (Request::segment(1) !== 'duty-assignments')
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-indigo-500/0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                        @endif
                    </a>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center space-x-4 ml-4">
                    <!-- Notifications Icon -->
                    <div class="relative">
                        <a href="{{ route('notifications.index') }}"
                            class="group relative flex items-center justify-center w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5 text-blue-200 group-hover:text-white transition-colors duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>

                            <!-- Unread Count Badge -->
                            @php
                                $unreadCount = auth()->user()->unreadNotifications->count();
                            @endphp
                            @if ($unreadCount > 0)
                                <span
                                    class="absolute -top-1 -right-1 flex items-center justify-center min-w-[20px] h-5 px-1 text-xs font-bold text-white bg-gradient-to-r from-red-500 to-red-600 rounded-full border-2 border-slate-800 shadow-lg animate-pulse">
                                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Notification Dropdown (Optional) -->
                        <div
                            class="notification-dropdown absolute hidden right-0 top-12 w-80 bg-slate-800/95 backdrop-blur-md rounded-xl shadow-2xl border border-white/10 z-50">
                            <div class="p-4 border-b border-white/10">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-white">Notifications</h3>
                                    @if ($unreadCount > 0)
                                        <button onclick="markAllAsRead()"
                                            class="text-sm text-blue-400 hover:text-blue-300 transition-colors duration-200">
                                            Mark all as read
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @forelse(auth()->user()->notifications->take(5) as $notification)
                                    <div
                                        class="p-4 border-b border-white/5 hover:bg-white/5 transition-colors duration-200
                                                {{ is_null($notification->read_at) ? 'bg-blue-500/10 border-l-2 border-l-blue-400' : '' }}">
                                        <div class="flex items-start space-x-3">
                                            <div
                                                class="flex-shrink-0 w-8 h-8 bg-blue-500/20 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-white font-medium truncate">
                                                    {{ $notification->data['message'] ?? 'New notification' }}
                                                </p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            @if (is_null($notification->read_at))
                                                <div
                                                    class="flex-shrink-0 w-2 h-2 bg-red-500 rounded-full animate-pulse">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center">
                                        <svg class="w-12 h-12 text-gray-500 mx-auto mb-3" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                            </path>
                                        </svg>
                                        <p class="text-gray-400 text-sm">No notifications</p>
                                    </div>
                                @endforelse
                            </div>
                            <div class="p-3 border-t border-white/10">
                                <a href="{{ route('notifications.index') }}"
                                    class="block w-full text-center px-4 py-2 text-sm text-blue-400 hover:text-blue-300 transition-colors duration-200">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="group flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-red-500/25">
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
                <!-- Mobile Notifications Link -->
                <a href="{{ route('notifications.index') }}"
                    class="block px-4 py-3 rounded-xl {{ Request::segment(1) === 'notifications' ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white' }} transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                </path>
                            </svg>
                            <span>Notifications</span>
                        </div>
                        @if ($unreadCount > 0)
                            <span
                                class="flex items-center justify-center min-w-[20px] h-5 px-1 text-xs font-bold text-white bg-red-500 rounded-full">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @endif
                    </div>
                </a>

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
                <!-- ... rest of your mobile menu items ... -->
            </div>
        </div>
    </div>
</nav>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const notificationIcon = document.querySelector('.relative > a');
            const notificationDropdown = document.querySelector('.notification-dropdown');

            // Mobile menu functionality
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    mobileMenu.classList.toggle('hidden');

                    const icon = mobileMenuButton.querySelector('svg');
                    if (mobileMenu.classList.contains('hidden')) {
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
                    } else {
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
                    }
                });

                window.addEventListener('click', (e) => {
                    if (!mobileMenuButton.contains(e.target) && !mobileMenu.contains(e.target)) {
                        mobileMenu.classList.add('hidden');
                        const icon = mobileMenuButton.querySelector('svg');
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
                    }
                });

                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 1024) {
                        mobileMenu.classList.add('hidden');
                        const icon = mobileMenuButton.querySelector('svg');
                        icon.innerHTML =
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
                    }
                });
            }

            // Notification dropdown functionality
            if (notificationIcon && notificationDropdown) {
                notificationIcon.addEventListener('click', (e) => {
                    if (window.innerWidth >= 1024) {
                        e.preventDefault();
                        notificationDropdown.classList.toggle('hidden');
                    }
                });

                // Close dropdown when clicking outside
                window.addEventListener('click', (e) => {
                    if (!notificationIcon.contains(e.target) && !notificationDropdown.contains(e.target)) {
                        notificationDropdown.classList.add('hidden');
                    }
                });
            }

            // Mark all as read function
            window.markAllAsRead = function() {
                fetch('{{ route('notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove all unread indicators
                            document.querySelectorAll('.bg-blue-500/10').forEach(el => {
                                el.classList.remove('bg-blue-500/10', 'border-l-2',
                                    'border-l-blue-400');
                            });
                            document.querySelectorAll('.bg-red-500').forEach(el => {
                                el.remove();
                            });

                            // Update unread count badge
                            const badge = document.querySelector('.min-w-\\[20px\\]');
                            if (badge) badge.remove();

                            // Close dropdown
                            if (notificationDropdown) {
                                notificationDropdown.classList.add('hidden');
                            }

                            // Reload to update the count everywhere
                            setTimeout(() => location.reload(), 500);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            };

            // Add scroll effect to navigation
            let lastScrollY = window.scrollY;
            window.addEventListener('scroll', () => {
                const nav = document.querySelector('nav');
                if (window.scrollY > lastScrollY && window.scrollY > 100) {
                    nav.style.transform = 'translateY(-100%)';
                } else {
                    nav.style.transform = 'translateY(0)';
                }
                lastScrollY = window.scrollY;
            });
        });
    </script>
@endpush
