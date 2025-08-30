@props(['name', 'label' => '', 'rows' => 4, 'placeholder' => '', 'value' => ''])

<div class="flex flex-col {{ $attributes->get('class') }}">
    @if ($label)
        <label for="{{ $name }}" class="text-gray-600 font-medium mb-1">{{ $label }}</label>
    @endif
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full border rounded-md p-3 focus:ring-2 focus:ring-orange-500 focus:outline-none']) }}>{{ old($name, $value) }}</textarea>
</div>
