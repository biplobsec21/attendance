@extends('mpm.layouts.app')

@section('title', 'Medical Category Details')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />
        <!-- Alert Messages -->
        @include('mpm.components.alerts')

        <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Medical Category Details</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('medical-categories.edit', $medicalCategory) }}"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg transition-colors no-underline">
                        Edit Medical Category
                    </a>
                    <a href="{{ route('medical-categories.index') }}"
                        class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Medical Category ID</label>
                        <p class="text-gray-900 text-lg">{{ $medicalCategory->id }}</p>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Medical Category Name</label>
                        <p class="text-gray-900 text-lg font-medium">{{ $medicalCategory->name }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Created At</label>
                        <p class="text-gray-900">{{ $medicalCategory->created_at->format('F d, Y h:i A') }}</p>
                    </div>

                    <div class="bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Last Updated</label>
                        <p class="text-gray-900">{{ $medicalCategory->updated_at->format('F d, Y h:i A') }}</p>
                    </div>

                    <div class="hidden bg-white/50 p-4 rounded-lg">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Soldiers Count</label>
                        <p class="text-gray-900 text-lg">
                            {{ $medicalCategory->soldiers_count ?? $medicalCategory->soldiers()->count() }}</p>
                    </div>
                </div>
            </div>

            @if ($medicalCategory->created_at != $medicalCategory->updated_at)
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-700">
                        <strong>Note:</strong> This medical category was last modified on
                        {{ $medicalCategory->updated_at->format('F d, Y \a\t h:i A') }}.
                    </p>
                </div>
            @endif

            @if ($medicalCategory->soldiers()->exists())
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-yellow-700">
                        <strong>Note:</strong> This medical category is currently assigned to
                        {{ $medicalCategory->soldiers()->count() }} soldier(s). It cannot be deleted until all assignments
                        are removed.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection
