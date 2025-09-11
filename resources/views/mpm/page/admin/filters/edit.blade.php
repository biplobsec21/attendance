@extends('mpm.layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-xl font-bold mb-4">Edit Filter: {{ $filter->name }}</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-2 rounded mb-3">{{ session('success') }}</div>
        @endif

        {{-- Update filter basic info --}}
        <form action="{{ route('filters.update', $filter->id) }}" method="POST" class="mb-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block">Filter Name</label>
                <input type="text" name="name" value="{{ $filter->name }}" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block">Description</label>
                <textarea name="description" class="w-full border rounded p-2">{{ $filter->description }}</textarea>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
        </form>

        {{-- Manage filter items --}}
        <h2 class="text-lg font-semibold mb-3">Filter Items</h2>

        <table class="min-w-full bg-white shadow rounded mb-4">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-3">Label</th>
                    <th class="p-3">Table</th>
                    <th class="p-3">Column</th>
                    <th class="p-3">Operator</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Options</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($filter->items as $item)
                    <tr class="border-t">
                        <td class="p-3">{{ $item->label }}</td>
                        <td class="p-3">{{ $item->table_name }}</td>
                        <td class="p-3">{{ $item->column_name }}</td>
                        <td class="p-3">{{ $item->operator }}</td>
                        <td class="p-3">{{ $item->value_type }}</td>
                        <td class="p-3">
                            @if ($item->options)
                                {{ implode(', ', $item->options) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Add new filter item --}}
        <form action="{{ route('filters.update', $filter->id) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <input type="hidden" name="add_item" value="1">
            <div>
                <label>Label</label>
                <input type="text" name="label" class="border rounded p-2 w-full" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Table</label>
                    <select name="table_name" class="border rounded p-2 w-full">
                        <option value="soldiers">Soldiers</option>
                        <option value="soldier_courses">Courses</option>
                        <option value="soldier_services">Services</option>
                        <option value="soldier_cadres">Cadres</option>
                        <option value="soldier_educations">Educations</option>
                        <option value="soldier_skills">Skills</option>
                        <option value="soldiers_medical">Medical</option>
                    </select>
                </div>
                <div>
                    <label>Column</label>
                    <input type="text" name="column_name" class="border rounded p-2 w-full" required>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label>Operator</label>
                    <select name="operator" class="border rounded p-2 w-full">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value=">">&gt;</option>
                        <option value="<">&lt;</option>
                        <option value="like">LIKE</option>
                    </select>
                </div>
                <div>
                    <label>Input Type</label>
                    <select name="value_type" class="border rounded p-2 w-full">
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="select">Select</option>
                    </select>
                </div>
                <div>
                    <label>Options (comma separated)</label>
                    <input type="text" name="options" class="border rounded p-2 w-full">
                </div>
            </div>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">+ Add Item</button>
        </form>
    </div>
@endsection
