@props(['index', 'data' => [], 'options' => [], 'section'])

<div class="grid grid-cols-1 sm:grid-cols-6 gap-2 items-center border p-3 rounded-md relative">
    <button type="button"
        class="remove-row absolute top-2 right-2 text-white bg-red-500 hover:bg-red-600 rounded-full w-6 h-6 flex items-center justify-center text-sm">×</button>

    @if ($section === 'medical' || $section === 'sickness')
        <x-form.select name="{{ $section }}[{{ $index }}][category]" class="sm:col-span-2">
            <option value="">Select {{ ucfirst($section) }} category</option>
            @foreach ($options as $opt)
                <option value="{{ $opt->id }}" {{ ($data['category'] ?? '') == $opt->id ? 'selected' : '' }}>
                    {{ $opt->name }}
                </option>
            @endforeach
        </x-form.select>
        <div
            class="details-container sm:col-span-{{ $section === 'medical' ? 4 : 4 }} grid grid-cols-{{ $section === 'medical' ? 3 : 2 }} gap-2">
            @if ($section === 'medical')
                <x-form.input type="date" name="{{ $section }}[{{ $index }}][start_date]"
                    value="{{ $data['start_date'] ?? '' }}" placeholder="Start Date" />
                <x-form.input type="date" name="{{ $section }}[{{ $index }}][end_date]"
                    value="{{ $data['end_date'] ?? '' }}" placeholder="End Date" />
                <x-form.input type="text" name="{{ $section }}[{{ $index }}][remarks]"
                    value="{{ $data['remarks'] ?? '' }}" placeholder="Remarks" />
            @else
                <x-form.input type="date" name="{{ $section }}[{{ $index }}][start_date]"
                    value="{{ $data['start_date'] ?? '' }}" placeholder="Start Date" />
                <x-form.input type="text" name="{{ $section }}[{{ $index }}][remarks]"
                    value="{{ $data['remarks'] ?? '' }}" placeholder="Remarks" />
            @endif
        </div>
    @elseif($section === 'punishments')
        <x-form.input name="punishments[{{ $index }}][name]" value="{{ $data['name'] ?? '' }}"
            placeholder="Punishment Name" class="sm:col-span-2" />
        <x-form.input name="punishments[{{ $index }}][detail]" value="{{ $data['detail'] ?? '' }}"
            placeholder="Detail" />
    @endif
</div>
