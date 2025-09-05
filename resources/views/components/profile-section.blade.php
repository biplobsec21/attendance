@props(['title', 'icon'])

<div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">
    <h2 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4 flex items-center">
        <i class="{{ $icon }} mr-3 text-orange-500"></i>
        <span>{{ $title }}</span>
    </h2>
    <div>
        {{ $slot }}
    </div>
</div>
