<nav class="text-gray-500 text-sm mb-4" aria-label="Breadcrumb">
    <ol class="list-reset flex flex-wrap gap-1">
        @foreach ($breadcrumbs as $breadcrumb)
            @if ($loop->last)
                <li class="text-gray-700 font-semibold">{{ $breadcrumb['label'] }}</li>
            @else
                <li>
                    <a href="{{ $breadcrumb['url'] }}" class="hover:text-orange-500">{{ $breadcrumb['label'] }}</a>
                    <span>/</span>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
