@extends('mpm.layouts.app')

@section('title', 'Absent Record Details')

@section('content')

    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />
        <!-- Alert Messages -->
        @include('mpm.components.alerts')

        <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Absent Record Details</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('absents.edit', $absent) }}"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition-colors no-underline">
                        Edit Record
                    </a>
                    <a href="{{ route('absents.index') }}"
                        class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Record ID</label>
                        <p class="text-gray-900 text-lg">{{ $absent->id }}</p>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                        <p class="text-gray-900 text-lg font-medium">{{ $absent->name }}</p>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Absent Date</label>
                        <p
                            class="text-gray-900 text-lg {{ $absent->is_past ? 'text-red-600' : 'text-green-600' }} font-medium">
                            {{ $absent->formatted_absent_date }}
                            @if ($absent->is_past)
                                <span class="text-sm text-gray-500">(Past)</span>
                            @else
                                <span class="text-sm text-gray-500">(Upcoming)</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                        <span
                            class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $absent->status_badge_class }}">
                            {{ $absent->status_text }}
                        </span>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Created At</label>
                        <p class="text-gray-900">{{ $absent->created_at->format('F d, Y h:i A') }}</p>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Last Updated</label>
                        <p class="text-gray-900">{{ $absent->updated_at->format('F d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <div class="bg-white/50 p-4 rounded-lg">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Reason</label>
                    <p class="text-gray-900 whitespace-pre-wrap">{{ $absent->reason }}</p>
                </div>
            </div>

            <div class="mt-6 p-4 bg-white/50 rounded-lg">
                <label class="block text-gray-700 text-sm font-bold mb-2">Quick Actions</label>
                <div class="flex space-x-2">
                    <form method="POST" action="{{ route('absents.toggle-status', $absent) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="px-3 py-1 text-sm font-medium rounded
                        {{ $absent->status ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} transition-colors">
                            {{ $absent->status ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('absents.destroy', $absent) }}" class="inline"
                        onsubmit="return confirm('Are you sure you want to delete this absent record? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-3 py-1 text-sm font-medium rounded bg-red-100 text-red-800 hover:bg-red-200 transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            </div>

            @if ($absent->created_at != $absent->updated_at)
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-700">
                        <strong>Note:</strong> This absent record was last modified on
                        {{ $absent->updated_at->format('F d, Y \a\t h:i A') }}.
                    </p>
                </div>
            @endif
        </div>
    </div>

@endsection
