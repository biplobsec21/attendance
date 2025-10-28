@extends('mpm.layouts.app')

@section('title', 'Absent Records List')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />
        <!-- Alert Messages -->
        @include('mpm.components.alerts')

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 pb-2">Absent Records List</h1>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div class="w-full flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <!-- Search Left -->
                    <form method="GET" action="{{ route('absents.index') }}"
                        class="w-full md:max-w-2xl flex flex-col md:flex-row gap-2">
                        <div class="flex flex-col md:flex-row gap-2 w-full">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="flex-1 px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                                placeholder="Search by name...">
                            <select name="status"
                                class="border rounded-lg px-2 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="flex flex-col md:flex-row gap-2 w-full">
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                                placeholder="Start Date">
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                                placeholder="End Date">
                            <button type="submit"
                                class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg">Search</button>
                            @if (request()->hasAny(['search', 'status', 'start_date', 'end_date']))
                                <a href="{{ route('absents.index') }}"
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Clear</a>
                            @endif
                        </div>
                    </form>
                    <!-- Add New Right -->
                    <div class="w-full md:w-auto md:ml-4">
                        <a href="{{ route('absents.create') }}"
                            class="w-full md:w-auto bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors text-center no-underline block md:inline-block">Add
                            New Absent Record</a>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-transparent rounded-lg">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('absents.index', array_merge(request()->query(), ['sort_by' => 'id', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                    class="no-underline text-gray-500">
                                    #ID
                                    @if (request('sort_by') === 'id')
                                        <span class="ml-1">{{ request('sort_direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('absents.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                    class="no-underline text-gray-500">
                                    Name
                                    @if (request('sort_by') === 'name')
                                        <span class="ml-1">{{ request('sort_direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('absents.index', array_merge(request()->query(), ['sort_by' => 'absent_date', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                    class="no-underline text-gray-500">
                                    Absent Date
                                    @if (request('sort_by') === 'absent_date')
                                        <span class="ml-1">{{ request('sort_direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reason
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('absents.index', array_merge(request()->query(), ['sort_by' => 'status', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                    class="no-underline text-gray-500">
                                    Status
                                    @if (request('sort_by') === 'status')
                                        <span class="ml-1">{{ request('sort_direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @forelse($absents as $absent)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $absent->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $absent->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="{{ $absent->is_past ? 'text-red-600' : 'text-green-600' }} font-medium">
                                        {{ $absent->formatted_absent_date }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs truncate" title="{{ $absent->reason }}">
                                        {{ Str::limit($absent->reason, 50) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="POST" action="{{ route('absents.toggle-status', $absent) }}"
                                        class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $absent->status_badge_class }} cursor-pointer border-0">
                                            {{ $absent->status_text }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('absents.show', $absent) }}"
                                        class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <a href="{{ route('absents.edit', $absent) }}"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form method="POST" action="{{ route('absents.destroy', $absent) }}" class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this absent record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 border-0 bg-transparent cursor-pointer">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    @if (request()->hasAny(['search', 'status', 'start_date', 'end_date']))
                                        No absent records found matching your criteria.
                                    @else
                                        No absent records available. <a href="{{ route('absents.create') }}"
                                            class="text-orange-600 hover:text-orange-800">Create one now</a>.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($absents->hasPages())
                <div class="flex justify-center items-center mt-4">
                    {{ $absents->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
