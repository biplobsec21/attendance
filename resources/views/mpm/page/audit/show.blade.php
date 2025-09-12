<div>
    <div class="space-y-4">
        <div class="flex justify-between">
            <span class="font-semibold text-gray-700">Table Name:</span>
            <span class="text-gray-600">{{ $log->subject_type ? class_basename($log->subject_type) : '' }}</span>
        </div>

        <div class="flex justify-between">
            <span class="font-semibold text-gray-700">Action:</span>
            <span class="text-gray-600">{{ strtoupper($log->event) }}</span>
        </div>

        <div class="flex justify-between">
            <span class="font-semibold text-gray-700">Description:</span>
            <span class="text-gray-600">{{ $log->description }}</span>
        </div>

        <div class="flex justify-between">
            <span class="font-semibold text-gray-700">User:</span>
            <span class="text-gray-600">
                @if ($log->causer)
                    {{ $log->causer->name }} (ID: {{ $log->causer->id }})
                @else
                    System
                @endif
            </span>
        </div>

        <div class="flex justify-between">
            <span class="font-semibold text-gray-700">Date & Time:</span>
            <span class="text-gray-600">{{ $log->created_at->format('d M Y, h:i A') }}</span>
        </div>

        @if ($log->properties->isNotEmpty())
            <div>
                <span class="font-semibold text-gray-700 mb-2 block">Changes:</span>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Field
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Old
                                    Value</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">New
                                    Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($log->properties['attributes'] ?? [] as $key => $new)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $key }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">
                                        {{ $log->properties['old'][$key] ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700">
                                        {{ $key === 'password' ? '********' : $new }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <p class="text-gray-500 mt-2">No changes recorded.</p>
        @endif
    </div>
</div>
