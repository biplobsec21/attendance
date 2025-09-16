@extends('mpm.layouts.app')

@section('title', 'Duty Assignments')

@section('content')
    <div class="container mx-auto p-4">
        <x-breadcrumb :breadcrumbs="generateBreadcrumbs()" />

        {{-- Errors / Alerts --}}
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <h1 class="text-3xl font-extrabold text-gray-800">Duty Assignments to Rank</h1>
            <a href="{{ route('duty.createAssignment') }}"
                class="inline-flex items-center bg-gradient-to-r from-orange-400 to-orange-600 hover:from-orange-500 hover:to-orange-700 text-white font-semibold py-2 px-5 rounded-lg shadow-lg transition-all transform hover:scale-105">
                + Create Assignment
            </a>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-lg border border-gray-200 bg-white">
            @include('mpm.components.alerts')
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-600 uppercase tracking-wider">Duty</th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-600 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-center text-sm font-bold text-gray-600 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-center text-sm font-bold text-gray-600 uppercase tracking-wider">Manpower
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-bold text-gray-600 uppercase tracking-wider">Remarks
                        </th>
                        <th class="px-6 py-3 text-center text-sm font-bold text-gray-600 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assignments as $assignment)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-3 text-sm font-medium text-gray-700">{{ $assignment->duty->duty_name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $assignment->rank->name }}</td>
                            <td class="px-6 py-3 text-sm">
                                @if ($assignment->duty_type === 'fixed')
                                    <span
                                        class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-semibold">
                                        <span class="font-bold">fixed: </span>
                                        <!-- Man icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M5.121 17.804A4 4 0 018 16h8a4 4 0 012.879 1.804M12 11a4 4 0 100-8 4 4 0 000 8z" />
                                        </svg>
                                        <span>{{ $assignment->soldier?->full_name ?? '-' }}</span>
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-semibold">
                                        {{ $assignment->duty_type }}
                                    </span>
                                @endif
                            </td>

                            <!-- Time Column -->
                            <td class="px-6 py-3 text-center text-sm text-gray-700">
                                <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-800 px-2 py-1 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($assignment->duty->start_time)->format('H:i') }}
                                        - {{ \Carbon\Carbon::parse($assignment->duty->end_time)->format('H:i') }}</span>
                                </span>
                            </td>

                            <td class="px-6 py-3 text-center text-sm text-gray-700 font-medium">{{ $assignment->manpower }}
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $assignment->remarks ?? '-' }}</td>
                            <td class="px-6 py-3 text-center flex justify-center gap-2">
                                <a href="{{ route('duty.editAssignment', $assignment->id) }}"
                                    class="text-blue-500 hover:text-blue-700 font-semibold transition-colors transform hover:scale-110">
                                    Edit
                                </a>
                                <form action="{{ route('duty.deleteAssignment', $assignment->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 font-semibold transition-colors transform hover:scale-110">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400 italic">
                                No assignments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
