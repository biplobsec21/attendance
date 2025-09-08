@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create Permission</h1>
        <form method="POST" action="{{ route('permissions.store') }}">
            @csrf
            <div class="mb-3">
                <label>Permission Name</label>
                <input type="text" name="name" class="form-control">
            </div>
            <button class="btn btn-success">Save</button>
        </form>
    </div>
@endsection
