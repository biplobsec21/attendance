@extends('mpm.layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-5xl mx-auto bg-white shadow-md rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Permission Management</h1>

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('permissions.update', $role ?? $roles->first()) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block mb-2 font-medium text-gray-700">Select Role</label>
                    <select name="role_id" class="w-full border-gray-300 rounded-lg px-3 py-2"
                        onchange="this.form.action='{{ url('admin/permissions/role') }}/'+this.value">
                        @foreach ($roles as $r)
                            <option value="{{ $r->id }}"
                                {{ isset($role) && $role->id == $r->id ? 'selected' : '' }}>
                                {{ ucfirst($r->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Permissions checkboxes -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($permissions as $module => $perms)
                        <div class="p-4 border rounded-lg bg-gray-50">
                            <h3 class="font-semibold text-gray-700 mb-2">{{ ucfirst($module) }}</h3>
                            <div class="space-y-1">
                                @foreach ($perms as $permission)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            {{ isset($role) && $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                            class="form-checkbox h-4 w-4 text-blue-500">
                                        <span class="text-gray-700">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                        Save Permissions
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection
