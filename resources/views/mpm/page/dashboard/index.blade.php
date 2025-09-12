@extends('mpm.layouts.app')

@section('title', 'Dashboard Reports')

@section('content')
    <div class="container mx-auto p-4 md:p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800">Dashboard Reports</h1>
            <div class="flex items-center text-sm text-slate-500 mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Last updated: {{ now()->format('d M Y, h:i A') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8">
            <section class="bg-white p-6 rounded-2xl shadow-md border border-slate-200/80">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-slate-700">Database Backup</h2>
                        </div>
                        <p class="text-slate-500 mt-2 text-sm">
                            Download a complete SQL backup. Filename:
                            <span class="font-mono text-slate-600">{{ now()->format('Y-m-d-H-i') }}.sql</span>
                        </p>
                    </div>
                    <a href="{{ route('database.download') }}"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm
                              transform hover:scale-105 transition-transform duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Download
                    </a>
                </div>
            </section>

            <section class="bg-white p-6 rounded-2xl shadow-md border border-slate-200/80">
                <header class="mb-6">
                    <h2 class="text-xl font-semibold text-slate-700">Audit Trail</h2>
                    <p class="text-slate-500 mt-1 text-sm">
                        A comprehensive record of all system updates and deletions for monitoring purposes.
                    </p>
                </header>

                <div class="overflow-x-auto ring-1 ring-slate-200 p-6 rounded-lg">
                    <table id="logs-table" class="min-w-full divide-y divide-slate-200 display">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Table Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Show</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">

                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>


    <div id="userModal" class="fixed inset-0 flex hidden items-center justify-center bg-black bg-opacity-50 z-50 p-4 mt-10">
        <div
            class="bg-white rounded-xl shadow-lg w-full max-w-md md:max-w-lg lg:max-w-xl
                transform scale-0 transition-transform duration-500
                flex flex-col max-h-[90vh]">

            <div class="p-6 border-b shrink-0">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800" id="userModalTitle">User Details</h2>
                    <button class="close-modal text-gray-500 hover:text-gray-700 text-lg">✕</button>
                </div>
            </div>

            <div id="userModalContent" class="p-6 overflow-y-auto">

            </div>

            <div class="p-4 bg-gray-50 text-right rounded-b-xl border-t shrink-0">
                <button class="close-modal px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Close</button>
            </div>

        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function manageModal(modalSelector) {
            var modal = $(modalSelector);
            var modalContent = modal.find('div.bg-white');

            function open() {
                modal.removeClass('hidden');
                modalContent.removeClass('scale-0 scale-100 scale-110').addClass('scale-110');
                setTimeout(function() {
                    modalContent.removeClass('scale-110').addClass('scale-100');
                }, 200);
            }

            function close() {
                modalContent.removeClass('scale-100 scale-110').addClass('scale-0');
                setTimeout(function() {
                    modal.addClass('hidden');
                }, 200);
            }
            modal.find('.close-modal').off('click').on('click', close);
            return {
                open: open,
                close: close
            };
        }

        const userModal = manageModal('#userModal');

        function showUserProfile(user_id) {
            $.ajax({
                url: "{{ route('users.show', ':id') }}".replace(':id', user_id),
                method: 'GET',
                beforeSend: function() {
                    $('#ajaxLoaderOverlay').fadeIn(200);
                },
                success: function(response) {
                    $('#userModalTitle').html('User Profile');
                    $('#userModalContent').html('');
                    $('#userModalContent').html(response);
                    userModal.open();

                },
                error: function(xhr, status, error) {
                    console.error(error);
                },
                complete: function() {
                    $('#ajaxLoaderOverlay').fadeOut(200);
                }
            })
        }

        function showDetails(user_id) {
            $.ajax({
                url: "{{ route('audit-trail.view', ':id') }}".replace(':id', user_id),
                method: 'GET',
                beforeSend: function() {
                    $('#ajaxLoaderOverlay').fadeIn(200);
                },
                success: function(response) {
                    $('#userModalTitle').html('Audit Trail');
                    $('#userModalContent').html('');
                    $('#userModalContent').html(response);
                    userModal.open();

                },
                error: function(xhr, status, error) {
                    console.error(error);
                },
                complete: function() {
                    $('#ajaxLoaderOverlay').fadeOut(200);
                }
            })
        }
        $(document).ready(function() {
            $('#logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('audit-trail.index') }}",
                columns: [{
                        data: 'table_name',
                        name: 'subject_type'
                    },
                    {
                        data: 'action_badge',
                        name: 'description',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'user',
                        name: 'causer_id'
                    },
                    {
                        data: 'date_time',
                        name: 'created_at'
                    },
                    {
                        data: 'show',
                        name: 'show',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush
