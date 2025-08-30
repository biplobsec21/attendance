@props(['name', 'label' => '', 'placeholder' => '', 'type' => 'text', 'value' => ''])

<div class="flex flex-col {{ $attributes->get('class') }}">
    @if ($label)
        <label for="{{ $name }}" class="text-gray-600 font-medium mb-1">{{ $label }}</label>
    @endif
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" placeholder="{{ $placeholder }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'w-full border rounded-md p-3 focus:ring-2 focus:ring-orange-500 focus:outline-none']) }}>
</div>
