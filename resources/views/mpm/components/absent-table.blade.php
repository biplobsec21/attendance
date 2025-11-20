<div class="overflow-x-auto rounded-xl border border-gray-200 shadow-lg">
    <table class="min-w-full bg-white divide-y divide-gray-200 rounded-xl">
        <thead class="bg-black text-white text-sm font-semibold uppercase rounded-t-xl">
            <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Profile Info</th>
                <th class="px-4 py-3 text-left">Apply Date / Type</th>
                <th class="px-4 py-3 text-left">Days</th>
                <th class="px-4 py-3 text-left max-w-[200px]">Reason</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100 text-gray-700 text-sm">
            @forelse ($absentDatas as $index => $data)
                <tr
                    class="hover:bg-orange-50 transition-colors duration-200 {{ $data->absent_current_status == 'rejected' ? 'bg-red-50' : ($data->absent_current_status == 'approved' ? 'bg-green-50' : 'bg-yellow-50') }}">
                    <!-- Serial -->
                    <td class="px-4 py-3 font-medium text-gray-800">
                        {{ ($absentDatas->currentPage() - 1) * $absentDatas->perPage() + $index + 1 }}
                    </td>

                    <!-- Profile Info -->
                    <td class="px-4 py-3">
                        <p class="font-semibold">{{ $data->soldier->full_name }}</p>
                        <p class="text-gray-500">{{ $data->soldier->rank->name ?? 'N/A' }}</p>
                        <p class="text-gray-400 text-xs"># {{ $data->soldier->army_no }}</p>
                    </td>

                    <!-- Apply Date / Type -->
                    <td class="px-4 py-3">
                        <p class="font-semibold">📅 {{ $data->created_at->format('d M Y') }}</p>
                        <p class="text-gray-600">Type: {{ $data->absentType->name ?? 'N/A' }}</p>
                    </td>

                    <!-- Days -->
                    <td class="px-4 py-3">
                        @php
                            $start = \Carbon\Carbon::parse($data->start_date);
                            $totalDays = 1; // Default to 1 day if no end date
                            $dateRangeText = $start->format('d/m/Y');

                            if ($data->end_date) {
                                $end = \Carbon\Carbon::parse($data->end_date);
                                $totalDays = $start->diffInDays($end) + 1;
                                $dateRangeText = $start->format('d/m/Y') . ' → ' . $end->format('d/m/Y');
                            } else {
                                $dateRangeText = $start->format('d/m/Y') . ' (Single Day)';
                            }
                        @endphp
                        <span class="px-3 py-1 rounded-full bg-orange-100 text-orange-700 font-semibold text-xs">
                            {{ $totalDays }} {{ $totalDays == 1 ? 'Day' : 'Days' }}
                        </span>
                        <p class="text-gray-400 text-xs mt-1">
                            {{ $dateRangeText }}
                        </p>
                    </td>

                    <!-- Reason -->
                    <td class="px-4 py-3 max-w-[200px] truncate" title="{{ $data->reason }}">
                        {{ $data->reason ?? 'N/A' }}
                    </td>

                    <!-- Status + Change Link -->
                    <td class="px-4 py-3 text-center">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            ];

                            $statusIcons = [
                                'pending' =>
                                    '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>',
                                'approved' =>
                                    '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
                                'rejected' =>
                                    '<svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
                            ];

                            $status = strtolower($data->absent_current_status ?? 'pending');
                        @endphp

                        <button data-id="{{ $data->id }}" data-status="{{ $status }}"
                            data-reject_reason="{{ $data->reject_reason ?? '' }}"
                            class="openStatusModal text-xs hover:underline mt-1 flex items-center justify-center gap-1"
                            title="{{ $data->reject_reason ?? '' }}">

                            <span
                                class="px-3 py-1 rounded-full {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-600' }} font-semibold text-xs flex items-center gap-1">
                                {!! $statusIcons[$status] !!}
                                {{ ucfirst($data->absent_current_status ?? 'Pending') }}
                            </span>
                        </button>
                    </td>

                    <!-- Actions -->
                    <td class="px-4 py-3 flex gap-2 justify-center">
                        <button
                            class="editAbsentBtn px-3 py-1 bg-blue-500 text-white rounded-lg text-xs font-semibold hover:bg-blue-600 transition-colors"
                            data-id="{{ $data->id }}" data-soldier="{{ $data->soldier_id }}"
                            data-absenttype="{{ $data->absent_type_id }}"
                            data-start="{{ $data->start_date->format('Y-m-d') }}"
                            data-end="{{ $data->end_date ? $data->end_date->format('Y-m-d') : '' }}"
                            data-reason="{{ $data->reason }}" data-hardcopy="{{ $data->hard_copy }}">
                            Edit
                        </button>
                        <button type="button"
                            class="deleteBtn px-3 py-1 bg-red-500 text-white rounded-lg text-xs font-semibold hover:bg-red-600 transition-colors"
                            data-id="{{ $data->id }}">
                            Delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                        No absent applications found matching your filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- @if ($absentDatas->hasPages())
    <div class="flex justify-center items-center mt-6 space-x-2 flex-wrap">
        {{ $absentDatas->links() }}
    </div>
@endif --}}
