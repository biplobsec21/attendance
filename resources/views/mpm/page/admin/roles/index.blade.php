@extends('mpm.layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-5xl mx-auto bg-white shadow-md rounded-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Roles Management</h1>
                <a href="{{ route('roles.create') }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                    Create Role
                </a>
            </div>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 divide-y divide-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">#</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Role Name</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Permissions</th>
                            <th class="px-4 py-2 text-center text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($roles as $i => $role)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-600">{{ $roles->firstItem() + $i }}</td>
                                <td class="px-4 py-2 text-sm text-gray-800">{{ ucfirst($role->name) }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">
                                    {{ $role->permissions->pluck('name')->map(fn($p) => ucfirst($p))->join(', ') }}
                                </td>
                                <td class="px-4 py-2 text-center space-x-2">
                                    <a href="{{ route('roles.edit', $role) }}"
                                        class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-lg transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline-block"
                                        onsubmit="return confirmDelete(this)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg transition">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(form) {
            if (confirm('Are you sure you want to delete this role?')) {
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerText = 'Deleting...';
                return true;
            }
            return false;
        }
    </script>
@endpush
