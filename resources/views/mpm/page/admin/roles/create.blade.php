@extends('mpm.layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Create Role</h1>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <ul class="mb-0 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('roles.store') }}" class="space-y-4"
                onsubmit="return disableSubmitButton(this)">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role Name</label>
                    <input type="text" name="name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach ($permissions as $permission)
                            <label
                                class="flex items-center space-x-2 bg-gray-50 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                    class="form-checkbox h-4 w-4 text-blue-500">
                                <span class="text-gray-700">{{ ucfirst($permission->name) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex space-x-3 mt-4">
                    <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center justify-center space-x-2">
                        <svg class="animate-spin h-5 w-5 text-white hidden mr-2" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Save Role</span>
                    </button>
                    <a href="{{ route('roles.index') }}"
                        class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function disableSubmitButton(form) {
            const btn = form.querySelector('button[type="submit"]');
            const spinner = btn.querySelector('svg');
            const text = btn.querySelector('span');

            btn.disabled = true;
            spinner.classList.remove('hidden');
            text.innerText = 'Processing...';
            return true;
        }
    </script>
@endpush
