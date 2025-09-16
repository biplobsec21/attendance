@extends('mpm.layouts.app')

@section('title', 'Duty List')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />
        <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
            {{-- Session Success Message --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
                <h1 class="text-2xl font-bold text-gray-800">Duty Lists</h1>
                <a href="{{ route('duty.create') }}"
                    class="w-full sm:w-auto bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition-colors text-center no-underline">
                    Add New Duty
                </a>
            </div>

            <form action="{{ route('duty.index') }}" method="GET" class="mb-4 flex flex-wrap items-end gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search"
                        class="mt-1 block w-full md:w-64 border rounded-lg px-3 py-2"
                        placeholder="Search by name or remark..." value="{{ request('search') }}">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border rounded-lg px-3 py-2">
                        <option value="">All</option>
                        <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Filter</button>
                    <a href="{{ route('duty.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg no-underline">Clear</a>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-transparent rounded-lg">
                    <thead>
                        <tr>
                            @php
                                // Helper function for sortable links
                                $sortLink = fn($sortBy, $label) => '<a href="' .
                                    route(
                                        'duty.index',
                                        array_merge(request()->query(), [
                                            'sort_by' => $sortBy,
                                            'sort_direction' =>
                                                request('sort_by') == $sortBy && request('sort_direction') == 'asc'
                                                    ? 'desc'
                                                    : 'asc',
                                        ]),
                                    ) .
                                    '" class="flex items-center gap-2">' .
                                    $label .
                                    (request('sort_by') == $sortBy
                                        ? (request('sort_direction') == 'asc'
                                            ? '▲'
                                            : '▼')
                                        : '') .
                                    '</a>';
                            @endphp
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#SL
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {!! $sortLink('duty_name', 'Duty Name') !!}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {!! $sortLink('remarks', 'Remarks') !!}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {!! $sortLink('status', 'Status') !!}</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @forelse ($duties as $duty)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ ($duties->currentPage() - 1) * $duties->perPage() + $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $duty->duty_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ date('h:i', strtotime($duty->start_time)) }} -
                                    {{ date('h:i', strtotime($duty->end_time)) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">{{ $duty->remark }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($duty->status == 'Active')
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <a href="{{ route('duty.edit', $duty->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 no-underline">Edit</a>
                                    <form action="{{ route('duty.destroy', $duty->id) }}" method="POST"
                                        class="inline-block ml-4"
                                        onsubmit="return confirm('Are you sure you want to delete this duty?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 border-none bg-transparent cursor-pointer p-0">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No duties found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $duties->links() }}
            </div>
        </div>
    </div>
@endsection
