<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Duty Assignment Report - {{ $date }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .section-header {
            background-color: #e0e0e0;
            font-weight: bold;
            padding: 8px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Duty Assignment Report</h1>
        <p>Date: {{ $date }} | Generated: {{ $generatedAt }}</p>
    </div>

    <!-- Roster Duties Section -->
    <div class="section-header">Roster Duties</div>
    <table>
        <thead>
            <tr>
                <th>Duty Name</th>
                <th>Time Slot</th>
                <th>Duration</th>
                <th>Required</th>
                <th>Assigned</th>
                <th>Soldier Name</th>
                <th>Army No</th>
                <th>Rank</th>
                <th>Company</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($details['roster_duties'] as $duty)
                @php
                    $startTime = \Carbon\Carbon::parse($duty['start_time'])->format('H:i');
                    $endTime = \Carbon\Carbon::parse($duty['end_time'])->format('H:i');
                    $assignedSoldiers = $duty['assigned_soldiers'];

                    // Handle both array and object formats for assigned_soldiers
                    if (is_array($assignedSoldiers)) {
                        $soldiers = array_values($assignedSoldiers);
                    } else {
                        $soldiers = json_decode(json_encode($assignedSoldiers), true);
                        $soldiers = is_array($soldiers) ? array_values($soldiers) : [];
                    }
                @endphp

                @if (count($soldiers) > 0)
                    @foreach ($soldiers as $soldier)
                        <tr>
                            <td>{{ $duty['duty_name'] }}</td>
                            <td>{{ $startTime }} - {{ $endTime }}</td>
                            <td>{{ $duty['duration_days'] }} day{{ $duty['duration_days'] > 1 ? 's' : '' }}</td>
                            <td>{{ $duty['required_manpower'] }}</td>
                            <td>{{ $duty['assigned_count'] }}</td>
                            <td>{{ $soldier['full_name'] ?? 'N/A' }}</td>
                            <td>{{ $soldier['army_no'] ?? 'N/A' }}</td>
                            <td>{{ $soldier['rank'] ?? 'N/A' }}</td>
                            <td>{{ $soldier['company'] ?? 'N/A' }}</td>
                            <td>{{ $soldier['remarks'] ?? '' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td>{{ $duty['duty_name'] }}</td>
                        <td>{{ $startTime }} - {{ $endTime }}</td>
                        <td>{{ $duty['duration_days'] }} day{{ $duty['duration_days'] > 1 ? 's' : '' }}</td>
                        <td>{{ $duty['required_manpower'] }}</td>
                        <td>{{ $duty['assigned_count'] }}</td>
                        <td colspan="5" style="text-align: center; color: #dc2626;">No soldiers assigned</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Fixed Duties Section -->
    @if (isset($details['fixed_duties']) && count($details['fixed_duties']) > 0)
        <div class="section-header">Fixed Duties</div>
        <table>
            <thead>
                <tr>
                    <th>Duty Name</th>
                    <th>Time Slot</th>
                    <th>Duration</th>
                    <th>Soldier Name</th>
                    <th>Army No</th>
                    <th>Rank</th>
                    <th>Company</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details['fixed_duties'] as $duty)
                    @php
                        $startTime = \Carbon\Carbon::parse($duty['start_time'])->format('H:i');
                        $endTime = \Carbon\Carbon::parse($duty['end_time'])->format('H:i');
                    @endphp
                    <tr>
                        <td>{{ $duty['duty_name'] }}</td>
                        <td>{{ $startTime }} - {{ $endTime }}</td>
                        <td>{{ $duty['duration_days'] }} day{{ $duty['duration_days'] > 1 ? 's' : '' }}</td>
                        <td>{{ $duty['full_name'] }}</td>
                        <td>{{ $duty['army_no'] }}</td>
                        <td>{{ $duty['rank'] }}</td>
                        <td>{{ $duty['company'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Unfulfilled Duties Section -->
    @if (isset($details['unfulfilled_duties']) && count($details['unfulfilled_duties']) > 0)
        <div class="section-header">Unfulfilled Duties</div>
        <table>
            <thead>
                <tr>
                    <th>Duty Name</th>
                    <th>Time Slot</th>
                    <th>Duration</th>
                    <th>Required</th>
                    <th>Assigned</th>
                    <th>Shortage</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details['unfulfilled_duties'] as $duty)
                    @php
                        $startTime = \Carbon\Carbon::parse($duty['start_time'])->format('H:i');
                        $endTime = \Carbon\Carbon::parse($duty['end_time'])->format('H:i');
                    @endphp
                    <tr>
                        <td>{{ $duty['duty_name'] }}</td>
                        <td>{{ $startTime }} - {{ $endTime }}</td>
                        <td>{{ $duty['duration_days'] }} day{{ $duty['duration_days'] > 1 ? 's' : '' }}</td>
                        <td>{{ $duty['required_manpower'] }}</td>
                        <td>{{ $duty['assigned_count'] }}</td>
                        <td>{{ $duty['shortage'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Summary Section -->
    @if (isset($details['summary']))
        <div class="section-header">Summary Statistics</div>
        <table style="width: 60%;">
            <tbody>
                <tr>
                    <td><strong>Total Duties:</strong></td>
                    <td>{{ $details['summary']['total_duties'] }}</td>
                </tr>
                <tr>
                    <td><strong>Total Assignments:</strong></td>
                    <td>{{ $details['summary']['total_assignments'] }}</td>
                </tr>
                <tr>
                    <td><strong>Unique Soldiers:</strong></td>
                    <td>{{ $details['summary']['unique_soldiers'] }}</td>
                </tr>
                <tr>
                    <td><strong>Unfulfilled Duties:</strong></td>
                    <td>{{ $details['summary']['unfulfilled_duties'] }}</td>
                </tr>
                <tr>
                    <td><strong>Average Duties/Soldier:</strong></td>
                    <td>{{ $details['summary']['average_duties_per_soldier'] }}</td>
                </tr>
            </tbody>
        </table>
    @endif

</body>

</html>
