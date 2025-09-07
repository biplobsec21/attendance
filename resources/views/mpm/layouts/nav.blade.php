<nav class="bg-gray-800 text-white p-4 fixed top-0 w-full z-20 h-16 shadow-md">
    {{-- <div class="container mx-auto flex justify-between items-center">
        <div class="flex justify-between items-center h-16">
            <div class="flex-shrink-0">
                <a href="#" class="text-2xl font-bold text-black">My App</a>
            </div>
            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-4">
                    <a href="{{ url('profile/index') }}"
                        class="text-black hover:bg-white/90 hover:text-black/90 px-3 py-2 rounded-md text-sm font-medium">Profile</a>

                    <!-- Settings Dropdown -->
                    <div class="relative">
                        <button id="settings-menu-button" class="text-black hover:bg-white/90 hover:text-black/90 px-3 py-2 rounded-md text-sm font-medium focus:outline-none">
                            <span>Settings</span>
                            <svg class="h-5 w-5 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="settings-menu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="settings-menu-button">
                                <a href="{{ route('ranks.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Rank</a>
                                <a href="{{ url('company/index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Company</a>
                                <a href="{{ url('course/index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Course</a>
                                <a href="{{ url('otherQual/index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Other Qual</a>
                                <a href="{{ url('sports/index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sports</a>
                                <a href="{{ url('absent/index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Absent</a>
                                <a href="{{ url('duty/index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Duty</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ url('assignDuty/index') }}"
                        class="text-black hover:bg-white/90 hover:text-black/90 px-3 py-2 rounded-md text-sm font-medium">Duty</a>
                    <a href="{{ url('leave/index') }}"
                        class="text-black hover:bg-white/90 hover:text-black/90 px-3 py-2 rounded-md text-sm font-medium">Leave</a>

                    <!-- Approval Dropdown -->
                    <div class="relative">
                        <button id="approval-menu-button" class="text-black hover:bg-white/90 hover:text-black/90 px-3 py-2 rounded-md text-sm font-medium focus:outline-none">
                            <span>Approval</span>
                            <svg class="h-5 w-5 inline-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="approval-menu" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="approval-menu-button">
                                <a href="{{ url('approval/duty') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Duty</a>
                                <a href="{{ url('approval/leave') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Leave</a>
                            </div>
                        </div>
                    </div>
                    <a href="{{ url('filter') }}"
                        class="text-black hover:bg-white/90 hover:text-black/90 px-3 py-2 rounded-md text-sm font-medium">Filter A</a>
                    <a href="{{ url('filters') }}"
                        class="text-black hover:bg-white/90 hover:text-black/90 px-3 py-2 rounded-md text-sm font-medium">Filter B</a>
                </div>
            </div>
        </div>
    </div> --}}


    <div class="container mx-auto flex justify-between items-center">
        <a href="#" class="text-xl font-bold">Army Records</a>
        <ul class="flex gap-6 items-center">
            <li><a href="#" class="hover:text-orange-400 transition-colors">Dashboard</a></li>
            <li><a href="#" class="hover:text-orange-400 transition-colors">Reports</a></li>
            <a href="{{ route('soldier.index') }}"
                class="px-4 py-2 rounded-lg {{ Request::segment(1) === 'army' ? 'bg-orange-500 text-white' : 'hover:text-orange-400 transition-colors' }}">
                Profile
            </a>
            <a href="{{ route('leave.index') }}"
                class="px-4 py-2 rounded-lg {{ Request::segment(1) === 'leave' ? 'bg-orange-500 text-white' : 'hover:text-orange-400 transition-colors' }}">
                Leave
            </a>

            <!-- Settings Dropdown -->
            <li class="relative">
                <button id="settingsBtn" type="button"
                    class="hover:text-orange-400 transition-colors flex items-center focus:outline-none">
                    Settings
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <ul id="settingsMenu" class="absolute left-0 mt-2 w-40 bg-white border rounded-lg shadow-lg hidden">
                    <li>
                        <a href="{{ route('duty.index') }}"
                            class="block px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 transition">
                            Duty
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('ranks.index') }}"
                            class="block px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 transition">
                            Rank
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('companies.index') }}"
                            class="block px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 transition">
                            Company
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('courses.index') }}"
                            class="block px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 transition">
                            Courses
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cadres.index') }}"
                            class="block px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 transition">
                            Cadre
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('skill.index') }}"
                            class="block px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 transition">
                            Skill
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('skillcategory.index') }}"
                            class="block px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 transition">
                            Skill Category
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('education.index') }}"
                            class="block px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 transition">
                            Education
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('atts.index') }}"
                            class="block px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 transition">
                            ATT
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('eres.index') }}"
                            class="block px-4 py-2 text-black hover:bg-orange-100 hover:text-orange-600 transition">
                            ERE
                        </a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="#"
                    class="hover:bg-orange-700 text-white font-bold py-2 px-4 rounded transition-colors">Logout</a>
            </li>
        </ul>
    </div>

</nav>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const settingsBtn = document.getElementById('settingsBtn');
            const settingsMenu = document.getElementById('settingsMenu');

            if (settingsBtn && settingsMenu) {
                settingsBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    settingsMenu.classList.toggle('hidden');
                });

                // Close dropdown if clicking outside
                window.addEventListener('click', (e) => {
                    if (!settingsBtn.contains(e.target) && !settingsMenu.contains(e.target)) {
                        settingsMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
@endpush
