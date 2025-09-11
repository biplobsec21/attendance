@extends('mpm.layouts.app')

@section('title', 'Profile List')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 formBack">

            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">Profile List</h1>

                <!-- Add New Button -->
                <a href="{{ route('soldier.personalForm') }}"
                    class="inline-flex items-center px-5 py-2.5 rounded-lg bg-gradient-to-r from-orange-500 to-pink-500 text-white font-semibold shadow-md hover:shadow-lg hover:from-orange-600 hover:to-pink-600 transition-all duration-300 ease-in-out transform hover:-translate-y-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New
                </a>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table id="profiles-table" class="min-w-full bg-white divide-y divide-gray-200 rounded-lg">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-sm font-medium">
                        <tr>
                            <th class="px-4 py-3"></th>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Rank</th>
                            <th class="px-4 py-3">Unit</th>
                            <th class="px-4 py-3">Current</th>
                            <th class="px-4 py-3">Previous</th>
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100 text-gray-700 text-sm">
                        <!-- Data will be populated by DataTables -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="pagination-controls" class="flex justify-center items-center mt-6 space-x-2 flex-wrap"></div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Row hover effect */
        #profiles-table tbody tr:hover {
            background-color: #f9fafb;
            transform: translateY(-1px);
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        /* Expanded details card */
        .details-card {
            background-color: #f3f4f6;
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 0.5rem 0;
            font-size: 0.875rem;
            color: #374151;
        }

        .details-card p {
            margin-bottom: 0.25rem;
        }

        /* Action buttons */
        .action-btn {
            @apply px-3 py-1 rounded shadow-sm text-white font-semibold text-xs;
        }

        .action-view {
            background-color: #3b82f6;
        }

        .action-view:hover {
            background-color: #2563eb;
        }

        .action-edit {
            background-color: #f59e0b;
        }

        .action-edit:hover {
            background-color: #d97706;
        }

        /* DataTables search input */
        .dataTables_filter {
            margin-bottom: 1.5rem;
        }

        .dataTables_filter input {
            width: 450px !important;
            padding: 0.75rem 1.5rem !important;
            border-radius: 9999px !important;
            border: 1px solid #d1d5db !important;
            outline: none !important;
            font-size: 1rem !important;
            color: #374151 !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%239CA3AF' viewBox='0 0 20 20'%3E%3Cpath fill-rule='evenodd' d='M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM10 16a6 6 0 100-12 6 6 0 000 12z' clip-rule='evenodd'/%3E%3C/svg%3E") no-repeat 12px center;
            padding-left: 2.5rem !important;
        }

        .dataTables_filter input::placeholder {
            color: #9CA3AF;
            font-style: italic;
        }

        .dataTables_filter input:focus {
            border-color: #f97316 !important;
            box-shadow: 0 0 0 2px #f97316 !important;
        }

        /* Modern pagination styling */
        .dataTables_paginate .paginate_button {
            background-color: #f3f4f6;
            border: none;
            color: #374151 !important;
            padding: 0.5rem 0.75rem;
            margin: 0 2px;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .dataTables_paginate .paginate_button.current {
            background-color: #f97316 !important;
            color: white !important;
        }

        .dataTables_paginate .paginate_button:hover {
            background-color: #f97316 !important;
            color: white !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(function() {
            function format(row) {
                return `
        <div class="details-card">
            <p><strong>Educations:</strong> ${row.educations || 'N/A'}</p>
            <p><strong>Courses:</strong> ${row.courses || 'N/A'}</p>
            <p><strong>Cadres:</strong> ${row.cadres || 'N/A'}</p>
            <p><strong>Co-Curricular:</strong> ${row.cocurricular || 'N/A'}</p>
            <p><strong>ATT:</strong> ${row.att || 'N/A'}</p>
            <p><strong>ERE:</strong> ${row.ere || 'N/A'}</p>
            <p><strong>Medical:</strong> ${row.medical || 'N/A'}</p>
            <p><strong>Sickness:</strong> ${row.sickness || 'N/A'}</p>
            <p><strong>Good Behavior:</strong> ${row.good_behavior || 'N/A'}</p>
            <p><strong>Bad Behavior:</strong> ${row.bad_behavior || 'N/A'}</p>
        </div>
        `;
            }

            let table = $('#profiles-table').DataTable({
                ajax: '{{ route('soldier.index') }}',
                columns: [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'rank'
                    },
                    {
                        data: 'unit'
                    },
                    {
                        data: 'current'
                    },
                    {
                        data: 'previous'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                pageLength: 10,
                lengthChange: false,
            });

            // Toggle child row details
            $('#profiles-table tbody').on('click', 'td.dt-control', function() {
                let tr = $(this).closest('tr');
                let row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });
        });
    </script>
@endpush
