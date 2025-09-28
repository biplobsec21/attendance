@props([
    'steps' => [],
    'profileId' => null,
])

@php
    $currentRoute = \Route::currentRouteName();

    $currentIndex = collect($steps)->search(function ($s) use ($currentRoute) {
        return isset($s['routeName']) && $s['routeName'] === $currentRoute;
    });

    $progressPercent = $steps ? (($currentIndex + 1) / count($steps)) * 100 : 0;
@endphp

<div class="container mx-auto px-4 py-6">

    <!-- Modern Step Navigation -->
    <nav class="relative">
        <!-- Mobile Scroll Hint -->
        <div class="sm:hidden mb-4 flex items-center justify-center">
            <div class="flex items-center text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l4-4 4 4M7 8l4 4 4-4">
                    </path>
                </svg>
                Scroll horizontally to see all steps
            </div>
        </div>

        <!-- Steps Container -->
        <div class="overflow-x-auto pb-4">
            <ul class="flex items-center gap-3 text-sm w-max min-w-full justify-center">
                @foreach ($steps as $index => $step)
                    @php
                        $isActive = $currentIndex === $index;
                        $isCompleted = $currentIndex > $index;
                        $isUpcoming = $currentIndex < $index;
                        $url = isset($step['routeName'])
                            ? ($step['enabled']
                                ? route(
                                    $step['routeName'],
                                    array_merge($step['params'] ?? [], $profileId ? ['id' => $profileId] : []),
                                )
                                : '#')
                            : $step['route'] ?? '#';
                    @endphp

                    <li class="flex items-center group">
                        <!-- Step Card -->
                        <div class="relative">
                            <a href="{{ $url }}"
                                class="flex flex-col sm:flex-row items-center gap-1 p-2 rounded-lg transition-all duration-300 transform group-hover:scale-105
                                       {{ $isActive
                                           ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-xl shadow-blue-500/25'
                                           : ($isCompleted
                                               ? 'bg-gradient-to-br from-green-50 to-emerald-50 text-green-700 border-2 border-green-200 hover:shadow-lg hover:border-green-300'
                                               : ($step['enabled']
                                                   ? 'bg-white text-gray-700 border-2 border-gray-200 hover:border-blue-300 hover:shadow-lg hover:bg-blue-50'
                                                   : 'bg-gray-50 text-gray-400 border-2 border-gray-100 opacity-60 cursor-not-allowed')) }}">

                                <!-- Step Circle -->
                                <div class="relative flex-shrink-0">
                                    <div
                                        class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-lg
                                               {{ $isActive
                                                   ? 'bg-white text-blue-600 shadow-lg'
                                                   : ($isCompleted
                                                       ? 'bg-green-500 text-white'
                                                       : ($step['enabled']
                                                           ? 'bg-gray-100 text-gray-600 group-hover:bg-blue-100 group-hover:text-blue-600'
                                                           : 'bg-gray-200 text-gray-400')) }}">
                                        @if ($isCompleted)
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        @elseif($isActive)
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        @else
                                            {!! $step['icon'] ?? $index + 1 !!}
                                        @endif
                                    </div>

                                    <!-- Active Pulse Animation -->
                                    @if ($isActive)
                                        <div class="absolute inset-0 rounded-2xl bg-white animate-ping opacity-20">
                                        </div>
                                    @endif

                                    <!-- Status Badge -->
                                    @if ($isCompleted)
                                        <div
                                            class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @elseif($isActive)
                                        <div
                                            class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full animate-pulse">
                                        </div>
                                    @endif
                                </div>

                                <!-- Step Content -->
                                <div class="text-center sm:text-left min-w-0">
                                    <div class="font-bold text-sm sm:text-base whitespace-nowrap">{{ $step['title'] }}
                                    </div>
                                    @if ($isActive)
                                        <div class="text-xs opacity-90 mt-1">Current Step</div>
                                    @elseif($isCompleted)
                                        <div class="text-xs mt-1">Completed</div>
                                    @elseif($step['enabled'])
                                        <div class="text-xs mt-1">Click to start</div>
                                    @else
                                        <div class="text-xs mt-1">Coming soon</div>
                                    @endif
                                </div>

                                <!-- Hover Effect Gradient -->
                                <div
                                    class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500/0 via-blue-500/5 to-purple-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                </div>
                            </a>
                        </div>
                    </li>

                    <!-- Connection Line -->
                    @if (!$loop->last)
                        <li class="hidden sm:flex items-center px-2">
                            <div
                                class="w-8 h-0.5 rounded-full
                                       {{ $currentIndex > $index ? 'bg-green-400' : 'bg-gray-300' }}
                                       transition-colors duration-300">
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>

        <!-- Mobile Step Indicators -->
        <div class="sm:hidden flex justify-center mt-4 space-x-2">
            @foreach ($steps as $index => $step)
                <div
                    class="w-2 h-2 rounded-full transition-all duration-300
                           {{ $currentIndex === $index ? 'bg-blue-500 w-6' : ($currentIndex > $index ? 'bg-green-400' : 'bg-gray-300') }}">
                </div>
            @endforeach
        </div>


    </nav>
    <!-- Modern Header -->
    <div class="mb-8">


        <!-- Progress Bar Container -->
        <div class="relative">
            <!-- Background Progress Track -->
            <div class="h-2 bg-gradient-to-r from-gray-200 to-gray-300 rounded-full overflow-hidden shadow-inner">
                <div id="progress-bar"
                    class="h-full bg-gradient-to-r from-blue-500 via-blue-600 to-indigo-600 transition-all duration-1000 ease-out rounded-full relative"
                    style="width: 0%">
                    <!-- Animated Shine Effect -->
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent transform -skew-x-12 animate-pulse">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const progressBar = document.getElementById('progress-bar');
        const progressPercent = {{ $progressPercent }};

        // Animate progress bar
        setTimeout(() => {
            progressBar.style.width = progressPercent + '%';
        }, 100);

        // Add smooth scrolling for mobile navigation
        const nav = document.querySelector('nav .overflow-x-auto');
        if (nav) {
            const activeStep = nav.querySelector('.bg-gradient-to-br.from-blue-500');
            if (activeStep) {
                activeStep.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center'
                });
            }
        }

        // Add hover sound effect (optional)
        const stepLinks = document.querySelectorAll('nav a');
        stepLinks.forEach(link => {
            link.addEventListener('mouseenter', () => {
                // You can add a subtle hover sound here if desired
                // new Audio('/sounds/hover.mp3').play().catch(() => {});
            });
        });
    });
</script>

<style>
    /* Custom scrollbar for better mobile experience */
    .overflow-x-auto {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }

    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f7fafc;
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: linear-gradient(to right, #3b82f6, #6366f1);
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to right, #2563eb, #4f46e5);
    }

    /* Smooth animations */
    * {
        scroll-behavior: smooth;
    }

    /* Pulse animation for active elements */
    @keyframes gentle-pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.8;
        }
    }

    .animate-gentle-pulse {
        animation: gentle-pulse 2s infinite;
    }
</style>
