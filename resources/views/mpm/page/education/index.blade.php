@extends('mpm.layouts.app')

@section('title', 'education List')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />
        @include('mpm.components.alerts')

        <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 pb-2">education List</h1>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <form method="GET" action="{{ route('education.index') }}"
                    class="w-full md:max-w-2xl flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="flex-1 px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Search education...">
                    <select name="status"
                        class="border rounded-lg px-2 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <button type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg">Search</button>
                    @if (request()->hasAny(['search', 'status']))
                        <a href="{{ route('education.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Clear</a>
                    @endif
                </form>
                <div class="w-full md:w-auto md:ml-4">
                    <a href="{{ route('education.create') }}"
                        class="w-full md:w-auto bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors text-center no-underline block md:inline-block">
                        Add New education
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-transparent rounded-lg">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300">
                        @forelse($educations as $education)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $education->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $education->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="POST" action="{{ route('education.toggle-status', $education) }}"
                                        class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $education->status_badge_class }} cursor-pointer border-0">
                                            {{ $education->status_text }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $education->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('education.show', $education) }}"
                                        class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <a href="{{ route('education.edit', $education) }}"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form method="POST" action="{{ route('education.destroy', $education) }}"
                                        class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this education?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 border-0 bg-transparent cursor-pointer">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    @if (request()->filled('search'))
                                        No education found matching your criteria.
                                    @else
                                        No education available. <a href="{{ route('education.create') }}"
                                            class="text-orange-600 hover:text-orange-800">Create one now</a>.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($educations->hasPages())
                <div class="flex justify-center items-center mt-4">
                    {{ $educations->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
