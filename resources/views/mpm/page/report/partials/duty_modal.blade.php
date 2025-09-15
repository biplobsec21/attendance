<!-- Duty Details Modal Content -->
<div class="p-4">
    <!-- Summary -->
    <div id="dutySummary" class="mb-4 flex justify-between bg-gray-100 p-3 rounded">
        <span>Total Duties: <strong>{{ $stats->sum('total_duties') }}</strong></span>
        <span>Total Soldiers: <strong>{{ $stats->sum('total_soldiers') }}</strong></span>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
        <table class="w-full border">
            <thead class="bg-gray-50 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-2 border">Duty Name</th>
                    <th class="px-4 py-2 border">Total Duties</th>
                    <th class="px-4 py-2 border">Total Soldiers</th>
                    <th class="px-4 py-2 border">Duty Times</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($stats as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border">{{ $item->duty->duty_name ?? 'N/A' }}</td>
                        <td class="px-4 py-2 border">{{ $item->total_duties }}</td>
                        <td class="px-4 py-2 border">{{ $item->total_soldiers }}</td>
                        <td class="px-4 py-2 border">
                            {{ $item->start_time ?? '' }} - {{ $item->end_time ?? '' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">No duties found for this date</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
