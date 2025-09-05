@extends('mpm.layouts.app')

@section('title', 'Profile List')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
            <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
                <h1 class="text-2xl font-bold text-gray-800">Profile List</h1>
                <div class="w-full md:w-auto flex flex-col sm:flex-row items-center gap-2">
                    <div class="w-full sm:w-auto flex items-center space-x-2">
                        <label for="rows-per-page" class="text-sm font-medium text-gray-700">Rows:</label>
                        <select id="rows-per-page"
                            class="w-full border rounded-lg px-2 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                    <input type="text" id="table-search"
                        class="w-full sm:w-auto px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Search profiles...">
                    <a href="{{ url('profile/personal') }}"
                        class="w-full sm:w-auto bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors text-center no-underline">Add
                        New Profile</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-transparent rounded-lg">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#SL
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mobile</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Company</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Course/Cadre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody id="profile-table-body" class="divide-y divide-gray-300">
                        <!-- Table rows will be inserted by JavaScript -->
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div id="pagination-controls" class="flex justify-center items-center mt-4 space-x-2 flex-wrap"></div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
