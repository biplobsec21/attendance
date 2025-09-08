@extends('mpm.layouts.app')

@section('title', 'Edit Permission')

@section('content')
    <div class="container py-6">
        <h1 class="text-2xl font-bold mb-6">Edit Permission</h1>

        <form action="{{ route('permissions.update', $permission->id) }}" method="POST" onsubmit="disableButton(this)">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block font-medium">Permission Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $permission->name) }}"
                    required>
                @error('name')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" id="submitBtn" class="btn btn-success">Update</button>
            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script>
        function disableButton(form) {
            form.querySelector('#submitBtn').disabled = true;
            return true;
        }
    </script>
@endsection
