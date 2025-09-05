@props(['title', 'items'])

<div>
    <h3 class="font-semibold text-gray-700 mb-2">{{ $title }}</h3>
    <ul class="list-disc list-inside text-gray-600 space-y-1">
        @forelse($items as $item)
            <li>
                @if (is_array($item))
                    {{-- Handle array structure --}}
                    {{ implode(', ', array_filter($item)) }}
                @else
                    {{-- Handle plain string --}}
                    {{ $item }}
                @endif
            </li>
        @empty
            <li class="text-gray-400 italic">N/A</li>
        @endforelse
    </ul>
</div>
