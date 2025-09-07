@extends('mpm.layouts.app')

@section('title', 'Generate Duty Assignments')

@section('content')
    <div class="container mx-auto p-6">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg p-6 formBack max-w-lg mx-auto">
            <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Generate Duty Assignments</h1>

            {{-- Generate for Today --}}
            <form action="{{ route('assignments.generateToday') }}" method="POST" class="mb-6">
                @csrf
                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 transition">
                    Generate Duties for Today
                </button>
            </form>

            {{-- Generate for Custom Date --}}
            <form action="{{ route('assignments.generateForDate') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Select Date</label>
                    <input type="date" name="date" id="date" value="{{ old('date') }}"
                        class="shadow-sm border rounded-lg w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500 @error('date') border-red-500 @enderror"
                        required>
                    @error('date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                    Generate Duties for Selected Date
                </button>
            </form>
        </div>

        {{-- Optional: show recent assignments --}}
        @php
            // Group assignments by soldier and date
            $assignmentsGrouped = $assignments->groupBy(function ($item) {
                return $item->soldier_id . '-' . $item->assigned_date;
            });
        @endphp

        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Soldier</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Rank</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Duty</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Type</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Start Time</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">End Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assignmentsGrouped as $group)
                    @foreach ($group as $assignment)
                        @php
                            // Check for overlapping duties
                            $overlap =
                                $group
                                    ->filter(function ($other) use ($assignment) {
                                        if ($other->id === $assignment->id) {
                                            return false;
                                        }
                                        return strtotime($assignment->start_time) < strtotime($other->end_time) &&
                                            strtotime($assignment->end_time) > strtotime($other->start_time);
                                    })
                                    ->count() > 0;
                        @endphp

                        <tr class="border-b hover:bg-gray-50 {{ $overlap ? 'bg-red-100' : '' }}">
                            <td class="px-4 py-2 text-sm">{{ $assignment->assigned_date }}</td>
                            <td class="px-4 py-2 text-sm">{{ $assignment->soldier->full_name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-sm">{{ $assignment->soldier->rank->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-sm">{{ $assignment->duty->duty_name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-sm capitalize">
                                @php
                                    $dutyType = $assignment->duty->dutyRanks
                                        ->where('rank_id', $assignment->soldier->rank->id)
                                        ->pluck('duty_type')
                                        ->first();
                                @endphp
                                {{ ucfirst($dutyType ?? 'N/A') }}
                            </td>
                            <td class="px-4 py-2 text-sm">
                                {{ \Carbon\Carbon::parse($assignment->start_time)->format('H:i') ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-sm">
                                {{ \Carbon\Carbon::parse($assignment->end_time)->format('H:i') ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>


    </div>
@endsection
