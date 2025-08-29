@extends('mpm.layouts.app')

@section('title', 'Edit Cadre')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white/30 shadow-lg rounded-lg p-6 formBack w-full max-w-lg mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Edit Cadre</h1>

            <form method="POST" action="{{ route('cadres.update', $cadre) }}"
                onsubmit="if(this.dataset.submitted){return false;}this.dataset.submitted=true;this.querySelector('[data-submit]')?.setAttribute('disabled','disabled');this.querySelector('[data-submit]')?.classList.add('opacity-50','cursor-not-allowed');">
                @csrf
                @method('PUT')

                {{-- Cadre Name --}}
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                        Cadre Name <span class="text-red-600">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $cadre->name) }}"
                        class="shadow-sm appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 @error('name') border-red-500 @enderror"
                        placeholder="Enter cadre name" required>
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>


                {{-- Status --}}
                <div class="mb-6">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">
                        Status <span class="text-red-600">*</span>
                    </label>
                    <select id="status" name="status"
                        class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-orange-500 @error('status') border-red-500 @enderror"
                        required>

                        <option value="1" {{ old('status', (string) (int) $cadre->status) === '1' ? 'selected' : '' }}>
                            Active</option>
                        <option value="0" {{ old('status', (string) (int) $cadre->status) === '0' ? 'selected' : '' }}>
                            Inactive</option>

                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('cadres.index') }}"
                        class="bg-transparent text-black font-semibold py-2 px-4 border border-black rounded-lg hover:bg-black hover:text-white transition-colors no-underline">
                        Back to List
                    </a>
                    <button type="submit" data-submit
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors">
                        Update Cadre
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
