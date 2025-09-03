<div class="grid grid-cols-12 gap-8 pb-8 border-b">
    <div class="col-span-12 md:col-span-4">
        <label class="font-bold text-gray-700">{{ $title }}</label>
        @if ($description)
            <p class="text-sm text-gray-500 mt-1">{{ $description }}</p>
        @endif
    </div>
    <div class="col-span-12 md:col-span-8">
        {{ $slot }}
    </div>
</div>
