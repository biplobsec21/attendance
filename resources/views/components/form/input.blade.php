@props(['name', 'label' => '', 'placeholder' => '', 'type' => 'text', 'value' => ''])

@php
    // Convert name from array syntax to dot syntax for error checking
    $errorName = str_replace(['[', ']'], ['.', ''], $name);
@endphp

<div class="flex flex-col {{ $attributes->get('class') }}">
    @if ($label)
        <label for="{{ $name }}" class="text-gray-600 font-medium mb-1">{{ $label }}</label>
    @endif

    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" placeholder="{{ $placeholder }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'w-full border rounded-md p-3 focus:ring-2 focus:ring-orange-500 focus:outline-none' . ($errors->has($errorName) ? ' border-red-500' : '')]) }}>

    {{-- Error message --}}
    @if ($errors->has($errorName))
        <span class="text-red-500 text-sm mt-1">
            {{ $errors->first($errorName) }}
        </span>
    @endif
</div>
