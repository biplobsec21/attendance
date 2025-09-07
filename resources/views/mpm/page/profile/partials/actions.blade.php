<a href="{{ route('soldier.details', $profile->id) }}" class="btn btn-sm btn-primary">View</a>
<a href="{{ route('soldier.personalForm', $profile->id) }}" class="btn btn-sm btn-warning">Edit</a>
<form action="#" method="POST" class="inline-block">
    @csrf

    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
</form>
