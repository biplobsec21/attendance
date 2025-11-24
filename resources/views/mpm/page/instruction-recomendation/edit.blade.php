@extends('mpm.layouts.app')

@section('title', 'Edit Instruction Recommendation')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />
        <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full max-w-lg mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Edit Instruction Recommendation</h1>

            <form method="POST" action="{{ route('instruction-recomendations.update', $instructionRecomendation) }}"
                onsubmit="if(this.dataset.submitted){return false;}this.dataset.submitted=true;this.querySelector('[data-submit]')?.setAttribute('disabled','disabled');this.querySelector('[data-submit]')?.classList.add('opacity-50','cursor-not-allowed');">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 text-sm font-bold mb-2">
                        Title <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="title" name="title"
                        value="{{ old('title', $instructionRecomendation->title) }}"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 @error('title') border-red-500 @enderror"
                        placeholder="Enter recommendation title" required>
                    @error('title')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 @error('description') border-red-500 @enderror"
                        placeholder="Enter recommendation description">{{ old('description', $instructionRecomendation->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Instruction Type --}}
                {{-- Priority Level --}}

                {{-- Status --}}
                <div class="mb-6">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">
                        Status <span class="text-red-600">*</span>
                    </label>
                    <select id="status" name="status"
                        class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 @error('status') border-red-500 @enderror"
                        required>
                        <option value="1"
                            {{ old('status', (string) (int) $instructionRecomendation->status) === '1' ? 'selected' : '' }}>
                            Active</option>
                        <option value="0"
                            {{ old('status', (string) (int) $instructionRecomendation->status) === '0' ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('instruction-recomendations.index') }}"
                        class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                        Back to List
                    </a>
                    <button type="submit" data-submit
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors">
                        Update Recommendation
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
