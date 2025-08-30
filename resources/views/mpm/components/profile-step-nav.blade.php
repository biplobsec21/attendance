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

<div class="container mx-auto px-4">
    <nav class="overflow-x-auto">
        <ul class="flex items-center gap-2 text-sm text-gray-600 font-medium w-max">
            @foreach ($steps as $index => $step)
                @php
                    $isActive = $currentIndex === $index;
                    $url = isset($step['routeName'])
                        ? ($step['enabled']
                            ? route(
                                $step['routeName'],
                                array_merge($step['params'] ?? [], $profileId ? ['id' => $profileId] : []),
                            )
                            : '#')
                        : $step['route'] ?? '#';
                @endphp

                <li class="flex items-center">
                    <a href="{{ $url }}"
                        class="flex items-center gap-2 px-3 py-2 rounded-lg transition-colors duration-200
                               {{ $isActive ? 'bg-orange-500 text-white font-semibold shadow-sm' : ($step['enabled'] ? 'hover:bg-gray-100 text-gray-700' : 'opacity-50 cursor-not-allowed') }}">
                        <span
                            class="w-6 h-6 rounded-full flex items-center justify-center text-xs
                                     {{ $isActive ? 'bg-white text-orange-500 font-bold' : 'bg-gray-300 text-gray-700' }}">
                            {!! $step['icon'] ?? $index + 1 !!}
                        </span>
                        <span class="whitespace-nowrap">{{ $step['title'] }}</span>
                    </a>
                </li>

                @if (!$loop->last)
                    <li><span class="mx-2 text-gray-300">&gt;</span></li>
                @endif
            @endforeach
        </ul>
    </nav>

    <div class="relative mt-3 h-1 bg-gray-300 rounded-full overflow-hidden">
        <div id="progress-bar" class="absolute top-0 left-0 h-full bg-orange-500 transition-all duration-500"
            style="width: 0%"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const progressBar = document.getElementById('progress-bar');
        const progressPercent = {{ $progressPercent }};
        setTimeout(() => {
            progressBar.style.width = progressPercent + '%';
        }, 50);
    });
</script>
