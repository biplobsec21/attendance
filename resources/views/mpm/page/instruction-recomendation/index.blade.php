@extends('mpm.layouts.app')

@section('title', 'Instruction Recommendations List')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />
        @include('mpm.components.alerts')

        <div class="bg-white/30 shadow-lg rounded-lg p-4 sm:p-6 formBack">
            <h1 class="text-2xl font-bold text-gray-800 mb-4 pb-2">Instruction Recommendations List</h1>

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <form method="GET" action="{{ route('instruction-recomendations.index') }}"
                    class="w-full md:max-w-3xl flex flex-col md:flex-row gap-2">
                    <div class="flex flex-col md:flex-row gap-2 flex-1">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="flex-1 px-3 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="Search recommendations...">


                        <select name="status"
                            class="border rounded-lg px-2 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg">Search</button>
                        @if (request()->hasAny(['search', 'status', 'instruction_type', 'priority_level']))
                            <a href="{{ route('instruction-recomendations.index') }}"
                                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">Clear</a>
                        @endif
                    </div>
                </form>
                <div class="w-full md:w-auto md:ml-4">
                    <a href="{{ route('instruction-recomendations.create') }}"
                        class="w-full md:w-auto bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors text-center no-underline block md:inline-block">
                        Add New Recommendation
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-transparent rounded-lg">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title
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
                        @forelse($instructionRecomendations as $recommendation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $recommendation->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $recommendation->title }}</td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="POST"
                                        action="{{ route('instruction-recomendations.toggle-status', $recommendation) }}"
                                        class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $recommendation->status_badge_class }} cursor-pointer border-0">
                                            {{ $recommendation->status_text }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $recommendation->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('instruction-recomendations.show', $recommendation) }}"
                                        class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <a href="{{ route('instruction-recomendations.edit', $recommendation) }}"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form method="POST"
                                        action="{{ route('instruction-recomendations.destroy', $recommendation) }}"
                                        class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this recommendation?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 border-0 bg-transparent cursor-pointer">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    @if (request()->filled('search'))
                                        No recommendations found matching your criteria.
                                    @else
                                        No recommendations available. <a
                                            href="{{ route('instruction-recomendations.create') }}"
                                            class="text-orange-600 hover:text-orange-800">Create one now</a>.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($instructionRecomendations->hasPages())
                <div class="flex justify-center items-center mt-4">
                    {{ $instructionRecomendations->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
