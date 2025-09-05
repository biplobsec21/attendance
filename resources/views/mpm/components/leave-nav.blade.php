@props([
    'steps' => [],
    'profileId' => null,
])

@php
    $currentRoute = \Route::currentRouteName();

@endphp

<div class="container mx-auto px-4">
    {{-- <nav class="overflow-x-auto">
        <ul class="flex items-center gap-2 text-sm text-gray-600 font-medium w-max">


            <li class="flex items-center">
                <button id="openLeaveModal"
                    class="flex items-center gap-2 px-4 py-2 border border-orange-400 text-black font-bold rounded-lg
                       hover:bg-orange-50 hover:border-orange-500
                       transition-colors duration-200">
                    <span class="whitespace-nowrap">+ Add new Leave application</span>
                </button>


            </li>

        </ul>
    </nav> --}}
    @include('mpm.components.alerts')
</div>
