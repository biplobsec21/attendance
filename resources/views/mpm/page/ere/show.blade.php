@extends('mpm.layouts.app')

@section('title', 'ERE Details')

@section('content')

    <div class="container mx-auto p-4">
        <!-- Alert Messages -->
        @include('mpm.components.alerts')

        <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">ERE Details</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('eres.edit', $ere) }}"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition-colors no-underline">
                        Edit ERE
                    </a>
                    <a href="{{ route('eres.index') }}"
                        class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">ERE ID</label>
                        <p class="text-gray-900 text-lg">{{ $ere->id }}</p>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">ere Name</label>
                        <p class="text-gray-900 text-lg font-medium">{{ $ere->name }}</p>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                        <span
                            class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $ere->status_badge_class }}">
                            {{ $ere->status_text }}
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Created At</label>
                        <p class="text-gray-900">{{ $ere->created_at->format('F d, Y h:i A') }}</p>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Last Updated</label>
                        <p class="text-gray-900">{{ $ere->updated_at->format('F d, Y h:i A') }}</p>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Quick Actions</label>
                        <div class="flex space-x-2">
                            <form method="POST" action="{{ route('eres.toggle-status', $ere) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="px-3 py-1 text-sm font-medium rounded
                                {{ $ere->status ? 'bg-red-100 text-red-800 hover:bg-red-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} transition-colors">
                                    {{ $ere->status ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('eres.destroy', $ere) }}" class="inline"
                                onsubmit="return confirm('Are you sure you want to delete this ere? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 text-sm font-medium rounded bg-red-100 text-red-800 hover:bg-red-200 transition-colors">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if ($ere->created_at != $ere->updated_at)
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-700">
                        <strong>Note:</strong> This ERE was last modified on
                        {{ $ere->updated_at->format('F d, Y \a\t h:i A') }}.
                    </p>
                </div>
            @endif
        </div>
    </div>

@endsection
