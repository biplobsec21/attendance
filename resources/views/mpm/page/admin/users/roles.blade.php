@extends('mpm.layouts.app')

@section('content')
    <div class="container mx-auto">
        <h2 class="text-xl font-bold mb-4">Manage Roles for {{ $user->name }}</h2>

        <form action="{{ route('users.roles.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                @foreach ($roles as $role)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                            {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                        <span>{{ ucfirst($role->name) }}</span>
                    </label>
                @endforeach
            </div>

            <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg">
                Update Roles
            </button>
        </form>
    </div>
@endsection
