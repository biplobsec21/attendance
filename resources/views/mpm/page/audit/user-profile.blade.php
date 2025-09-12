<div class="flex items-center space-x-4 mb-6">
    <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-2xl font-bold text-gray-600">
        {{ strtoupper(substr($user->name, 0, 1)) }}
    </div>
    <div>
        <h1 class="text-xl font-bold text-gray-800">{{ $user->name }}</h1>
        <p class="text-gray-500">{{ $user->email }}</p>
        <p class="text-gray-400 text-sm">User ID: {{ $user->id }}</p>
    </div>
</div>

<!-- User Info -->
<div class="space-y-4">
    <div class="flex justify-between border-b pb-2">
        <span class="font-semibold text-gray-700">Name:</span>
        <span class="text-gray-600">{{ $user->name }}</span>
    </div>
    <div class="flex justify-between border-b pb-2">
        <span class="font-semibold text-gray-700">Email:</span>
        <span class="text-gray-600">{{ $user->email }}</span>
    </div>
    <div class="flex justify-between border-b pb-2">
        <span class="font-semibold text-gray-700">Role(s):</span>
        <span class="text-gray-600">
            @if ($user->roles->count())
                {{ $user->roles->pluck('name')->join(', ') }}
            @else
                N/A
            @endif
        </span>
    </div>
    <div class="flex justify-between border-b pb-2">
        <span class="font-semibold text-gray-700">Created At:</span>
        <span class="text-gray-600">{{ $user->created_at->format('d M Y, h:i A') }}</span>
    </div>
    <div class="flex justify-between pb-2">
        <span class="font-semibold text-gray-700">Last Updated:</span>
        <span class="text-gray-600">{{ $user->updated_at->format('d M Y, h:i A') }}</span>
    </div>
</div>
