@extends('mpm.layouts.app')

@section('title', 'Audit Trail')

@section('content')
    @include('mpm.components.leave-nav')
    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <main class="container mx-auto p-4 sm:p-6 lg:p-8">
        <section class="bg-white p-6 rounded-lg shadow-md mb-8">
            <header class="flex items-center justify-between border-b pb-4 mb-4">
                <h1 class="text-2xl font-bold text-gray-700">Database Backup</h1>
                <a
                    href="{{ route('database.download') }}"class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition duration-300 ease-in-out">
                    Download Database
                </a>
            </header>
            <p class="text-gray-600">Click the button to download a complete backup of the database. Filename:
                <span class="font-mono text-gray-800">{{ now()->format('Y-m-d-H-i') }}.sql
                </span>
            </p>
        </section>

        <section class="bg-white p-6 rounded-lg shadow-md">
            <header class="border-b pb-4 mb-4">
                <h1 class="text-2xl font-bold text-gray-700">Audit Trail</h1>
            </header>
            <p class="text-gray-600 mb-6">
                A comprehensive record of all updates and deletions in the system for auditing and monitoring purposes.
            </p>


            <div class="overflow-x-auto">
                <table id="logs-table" class="min-w-full divide-y divide-gray-200 display">
                    <thead>
                        <tr>
                            <th>Table Name</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>User</th>
                            <th>Date & Time</th>
                            <th>Show</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </section>
    </main>


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
