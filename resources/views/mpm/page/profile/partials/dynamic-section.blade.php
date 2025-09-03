<div class="grid grid-cols-12 gap-8 pb-8 border-t pt-8">
    <div class="col-span-12 md:col-span-4">
        <label class="font-bold text-gray-700">{{ $title }}</label>
        <p class="text-sm text-gray-500 mt-1">{{ $description }}</p>
    </div>
    <div class="col-span-12 md:col-span-8">
        <div id="{{ $containerId }}" class="space-y-4">
            @foreach ($rows as $row)
                <div class="grid grid-cols-1 sm:grid-cols-10 md:grid-cols-12 gap-2 items-center border p-3 rounded-md">
                    {{-- Name / Select --}}
                    <div class="flex items-center sm:col-span-4 md:col-span-4">
                        <select class="w-full p-2 border rounded-md bg-white name-select"
                            name="{{ $containerId }}[][id]">
                            <option value="">Select</option>
                            @foreach ($options as $option)
                                <option value="{{ $option->id }}"
                                    {{ isset($row['id']) && $row['id'] == $option->id ? 'selected' : '' }}>
                                    {{ $option->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    @if (!empty($statusOptions))
                        <div
                            class="status-container sm:col-span-3 md:col-span-2 {{ isset($row['id']) ? '' : 'hidden' }}">
                            <select class="status-select w-full p-2 border rounded-md bg-white"
                                name="{{ $containerId }}[][status]">
                                <option value="">Select Status</option>
                                @foreach ($statusOptions as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ isset($row['status']) && $row['status'] == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Year or Passed Fields --}}
                    @if (!empty($showYear) && $showYear)
                        <div
                            class="year-container sm:col-span-2 {{ isset($row['status']) && $row['status'] == 'Passed' ? '' : 'hidden' }}">
                            <input type="text" placeholder="Year" class="w-full p-2 border rounded-md"
                                name="{{ $containerId }}[][year]" value="{{ $row['year'] ?? '' }}">
                        </div>
                    @elseif (in_array('start_date', $fields ?? []))
                        <div
                            class="passed-fields-container sm:col-span-5 md:col-span-5 grid grid-cols-3 gap-2 {{ isset($row['status']) && $row['status'] == 'Passed' ? '' : 'hidden' }}">
                            @if (isset($row['start_date']))
                                <input type="date" class="w-full p-2 border rounded-md"
                                    name="{{ $containerId }}[][start_date]" value="{{ $row['start_date'] ?? '' }}">
                            @endif
                            @if (isset($row['end_date']))
                                <input type="date" class="w-full p-2 border rounded-md"
                                    name="{{ $containerId }}[][end_date]" value="{{ $row['end_date'] ?? '' }}">
                            @endif
                            @if (isset($row['remark']))
                                <input type="text" class="w-full p-2 border rounded-md"
                                    name="{{ $containerId }}[][remark]" placeholder="Result"
                                    value="{{ $row['remark'] ?? '' }}">
                            @endif
                        </div>
                    @endif

                    <div class="sm:col-span-1 md:col-span-1 flex justify-end">
                        <button type="button"
                            class="remove-row bg-red-500 hover:bg-red-600 text-white p-2 rounded-md h-full">✕</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
