@extends('mpm.layouts.app')

@section('title', 'Filter Employees')

{{-- Pushing DataTables CSS to the main layout's style stack --}}
@push('styles')
    <style>
        /* Optional: Add some basic styling for the page */
        body {
            font-family: sans-serif;
            padding: 2em;
        }

        /* Style for the column search inputs in the header */
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }
    </style>
@endpush
@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full  mx-auto">

            <table class="myTable min-w-full border border-gray-200 rounded-lg">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 text-left">Filter Name</th>
                        <th class="p-3 text-left">Description</th>
                        <th class="p-3 text-left">Filter Items</th>
                        <th class="p-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($filters as $filter)
                        <tr class="border-t bg-white">
                            <td class="p-3 font-semibold">{{ $filter->name }}</td>
                            <td class="p-3">{{ $filter->description }}</td>
                            <td class="p-3">
                                @if ($filter->items->isNotEmpty())
                                    <table class="w-full border border-gray-200 rounded">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="p-2 text-left">Label</th>
                                                <th class="p-2 text-left">Column</th>
                                                <th class="p-2 text-left">Operator</th>
                                                <th class="p-2 text-left">Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($filter->items as $item)
                                                <tr class="border-t">
                                                    <td class="p-2">{{ $item->label }}</td>
                                                    <td class="p-2">{{ $item->column_name }}</td>
                                                    <td class="p-2">{{ $item->operator }}</td>
                                                    <td class="p-2">
                                                        @if (!empty($item->options))
                                                            {{ is_array($item->options) ? implode(', ', $item->options) : $item->options }}
                                                        @else
                                                            <span class="text-gray-400">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <span class="text-gray-400">No filter items</span>
                                @endif
                            </td>
                            <td class="p-3 space-x-2">
                                <a href="{{ route('filters.edit', $filter->id) }}" class="text-blue-500">Edit</a>
                                <form action="{{ route('filters.destroy', $filter->id) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-500"
                                        onclick="return confirm('Delete this filter?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $filters->links() }}
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    {{-- <script>
        $(document).ready(function() {
            // Setup - Clone the header row and add it to the table head
            $('#myTable thead tr')
                .clone(true)
                .addClass('filters')
                .appendTo('#myTable thead');

            $('#myTable').DataTable({
                orderCellsTop: true, // Use the top row for sorting, second for filtering
                fixedHeader: true,
                initComplete: function() {
                    var api = this.api();

                    // For each column, add a search input
                    api.columns().eq(0).each(function(colIdx) {
                        var cell = $('.filters th').eq(
                            $(api.column(colIdx).header()).index()
                        );
                        var title = $(cell).text();
                        $(cell).html('<input type="text" placeholder="Search ' + title +
                            '" />');

                        // Add an event listener for the input field
                        $(
                                'input',
                                $('.filters th').eq($(api.column(colIdx).header()).index())
                            )
                            .off('keyup change')
                            .on('keyup change', function(e) {
                                // Get the search value
                                $(this).attr('title', $(this).val());
                                var regexr = '({search})';

                                // Perform the search
                                api
                                    .column(colIdx)
                                    .search(
                                        this.value != '' ?
                                        regexr.replace('{search}', '(((' + this.value +
                                            ')))') :
                                        '',
                                        this.value != '',
                                        this.value == ''
                                    )
                                    .draw();
                            });
                    });
                }
            });
        });
    </script> --}}
@endpush
