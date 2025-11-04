@extends('mpm.layouts.app')

@section('title', 'Permanent Sickness Details')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />
        <!-- Alert Messages -->
        @include('mpm.components.alerts')

        <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Permanent Sickness Details</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('permanent-sickness.edit', $permanentSickness) }}"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition-colors no-underline">
                        Edit Permanent Sickness
                    </a>
                    <a href="{{ route('permanent-sickness.index') }}"
                        class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-4">
                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Permanent Sickness ID</label>
                        <p class="text-gray-900 text-lg">{{ $permanentSickness->id }}</p>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Permanent Sickness Name</label>
                        <p class="text-gray-900 text-lg font-medium">{{ $permanentSickness->name }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Created At</label>
                        <p class="text-gray-900">{{ $permanentSickness->created_at->format('F d, Y h:i A') }}</p>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Last Updated</label>
                        <p class="text-gray-900">{{ $permanentSickness->updated_at->format('F d, Y h:i A') }}</p>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Soldiers Count</label>
                        <p class="text-gray-900 text-lg">{{ $permanentSickness->soldiers_count }}</p>
                    </div>
                </div>
            </div>

            <!-- Soldiers Assignment Section -->
            @if ($permanentSickness->soldiers->count() > 0)
                <div class="mt-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Assigned Soldiers
                        ({{ $permanentSickness->soldiers->count() }})</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white/50 rounded-lg">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Soldier ID
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Start Date
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">End Date
                                    </th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($permanentSickness->soldiers as $soldier)
                                    <tr>
                                        <td class="px-4 py-2">{{ $soldier->id }}</td>
                                        <td class="px-4 py-2 font-medium">{{ $soldier->name }}</td>
                                        <td class="px-4 py-2">
                                            {{ $soldier->pivot->start_date ? $soldier->pivot->start_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $soldier->pivot->end_date ? $soldier->pivot->end_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2">{{ $soldier->pivot->remarks ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if ($permanentSickness->created_at != $permanentSickness->updated_at)
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-700">
                        <strong>Note:</strong> This permanent sickness was last modified on
                        {{ $permanentSickness->updated_at->format('F d, Y \a\t h:i A') }}.
                    </p>
                </div>
            @endif

            @if ($permanentSickness->soldiers_count > 0)
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-yellow-700">
                        <strong>Note:</strong> This permanent sickness is currently assigned to
                        {{ $permanentSickness->soldiers_count }} soldier(s). It cannot be deleted until all assignments are
                        removed.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
