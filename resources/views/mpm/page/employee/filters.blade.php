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

            <table id="myTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Course</th>
                        <th>Other Qual</th>
                        <th>Sports</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- This data is static. In a full Laravel app, you would use a @foreach loop --}}
                    <tr>
                        <td>Tiger Nixon</td>
                        <td>System Architect</td>
                        <td>Edinburgh</td>
                        <td>61</td>
                        <td>Computer, Singer, Presenter</td>
                        <td>Football, Cricket</td>
                    </tr>
                    <tr>
                        <td>Garrett Winters</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        <td>63</td>
                        <td>Computer, Presenter</td>
                        <td>Football, Cricket</td>
                    </tr>
                    <tr>
                        <td>Ashton Cox</td>
                        <td>Junior Technical Author</td>
                        <td>San Francisco</td>
                        <td>66</td>
                        <td>Computer, Singer, Presenter</td>
                        <td>Football, Cricket</td>
                    </tr>
                    <tr>
                        <td>Cedric Kelly</td>
                        <td>Senior Javascript Developer</td>
                        <td>Edinburgh</td>
                        <td>22</td>
                        <td>Singer, Presenter</td>
                        <td>Football, Game</td>
                    </tr>
                    <tr>
                        <td>Airi Satou</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        <td>33</td>
                        <td>Computer, Singer</td>
                        <td>Football, Cricket</td>
                    </tr>
                    <tr>
                        <td>Brielle Williamson</td>
                        <td>Integration Specialist</td>
                        <td>New York</td>
                        <td>61</td>
                        <td>Computer, Presenter</td>
                        <td>Football, Hadodo</td>
                    </tr>
                    {{-- ... other static rows from your original file ... --}}
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')


    <script>
        $(document).ready(function () {
            // Setup - Clone the header row and add it to the table head
            $('#myTable thead tr')
                .clone(true)
                .addClass('filters')
                .appendTo('#myTable thead');

            $('#myTable').DataTable({
                orderCellsTop: true, // Use the top row for sorting, second for filtering
                fixedHeader: true,
                initComplete: function () {
                    var api = this.api();

                    // For each column, add a search input
                    api.columns().eq(0).each(function (colIdx) {
                        var cell = $('.filters th').eq(
                            $(api.column(colIdx).header()).index()
                        );
                        var title = $(cell).text();
                        $(cell).html('<input type="text" placeholder="Search ' + title + '" />');

                        // Add an event listener for the input field
                        $(
                            'input',
                            $('.filters th').eq($(api.column(colIdx).header()).index())
                        )
                            .off('keyup change')
                            .on('keyup change', function (e) {
                                // Get the search value
                                $(this).attr('title', $(this).val());
                                var regexr = '({search})';

                                // Perform the search
                                api
                                    .column(colIdx)
                                    .search(
                                        this.value != ''
                                            ? regexr.replace('{search}', '(((' + this.value + ')))')
                                            : '',
                                        this.value != '',
                                        this.value == ''
                                    )
                                    .draw();
                            });
                    });
                }
            });
        });
    </script>

@endpush