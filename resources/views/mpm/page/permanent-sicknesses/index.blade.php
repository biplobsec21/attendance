@extends('mpm.layouts.app')

@section('title', 'Permanent Sicknesses List')

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
            <h1 class="text-2xl font-bold text-gray-800 mb-4 pb-2">Permanent Sicknesses List</h1>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div class="w-full flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <!-- Search Left -->
                    <form method="GET" action="{{ route('permanent-sickness.index') }}"
                        class="w-full md:max-w-xl flex items-center gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="flex-1 px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="Search permanent sicknesses...">
                        <button type="submit"
                            class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg">Search</button>
                        @if (request()->has('search'))
                            <a href="{{ route('permanent-sickness.index') }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Clear</a>
                        @endif
                    </form>
                    <!-- Add New Right -->
                    <div class="w-full md:w-auto md:ml-4">
                        <a href="{{ route('permanent-sickness.create') }}"
                            class="w-full md:w-auto bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors text-center no-underline block md:inline-block">
                            Add New Permanent Sickness
                        </a>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-transparent rounded-lg">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('permanent-sickness.index', array_merge(request()->query(), ['sort_by' => 'id', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                    class="no-underline text-gray-500">
                                    #ID
                                    @if (request('sort_by') === 'id')
                                        <span class="ml-1">{{ request('sort_direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('permanent-sickness.index', array_merge(request()->query(), ['sort_by' => 'name', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                    class="no-underline text-gray-500">
                                    Permanent Sickness Name
                                    @if (request('sort_by') === 'name')
                                        <span class="ml-1">{{ request('sort_direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('permanent-sickness.index', array_merge(request()->query(), ['sort_by' => 'created_at', 'sort_direction' => request('sort_direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                    class="no-underline text-gray-500">
                                    Created At
                                    @if (request('sort_by') === 'created_at')
                                        <span class="ml-1">{{ request('sort_direction') === 'asc' ? '↑' : '↓' }}</span>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Soldiers Count
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @forelse($permanentSicknesses as $sickness)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $sickness->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $sickness->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sickness->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sickness->soldiers_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('permanent-sickness.show', $sickness) }}"
                                        class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <a href="{{ route('permanent-sickness.edit', $sickness) }}"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form method="POST" action="{{ route('permanent-sickness.destroy', $sickness) }}"
                                        class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this permanent sickness?')">
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
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    @if (request()->has('search'))
                                        No permanent sicknesses found matching your criteria.
                                    @else
                                        No permanent sicknesses available. <a
                                            href="{{ route('permanent-sickness.create') }}"
                                            class="text-orange-600 hover:text-orange-800">Create one now</a>.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($permanentSicknesses->hasPages())
                <div class="flex justify-center items-center mt-4">
                    {{ $permanentSicknesses->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
