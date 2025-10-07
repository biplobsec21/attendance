<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Duty Assignment Report</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            text-align: center;
            margin: 20px 0 10px;
            text-decoration: underline;
        }

        .duty-header {
            font-weight: bold;
            margin: 15px 0 5px;
        }

        .table-header {
            font-weight: bold;
            margin: 10px 0 5px;
        }

        .table-row {
            display: flex;
        }

        .table-cell {
            flex: 1;
        }

        .divider {
            border-top: 1px solid #000;
            margin: 10px 0;
        }

        .summary-row {
            display: flex;
            margin: 5px 0;
        }

        .summary-label {
            flex: 0 0 200px;
        }

        .summary-value {
            flex: 1;
        }

        .fulfilled {
            color: green;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">====================================================================</div>
        <div class="title"> MILITARY DUTY ASSIGNMENT REPORT</div>
        <div class="title">====================================================================</div>
        <div style="margin-top: 15px;">
            📅 Report Date: {{ $date->format('d M Y') }}<br>
            🕓 Generated: {{ $date->format('d M Y H:i') }}
        </div>
    </div>

    <div class="divider"></div>
    <div class="section-title">SUMMARY STATISTICS</div>
    <div class="divider"></div>

    <div class="summary-row">
        <div class="summary-label">Total Duties:</div>
        <div class="summary-value">{{ $summary['total_duties'] }}</div>
    </div>
    <div class="summary-row">
        <div class="summary-label">Total Assignments:</div>
        <div class="summary-value">{{ $summary['total_assignments'] }}</div>
    </div>
    <div class="summary-row">
        <div class="summary-label">Unique Soldiers:</div>
        <div class="summary-value">{{ $summary['unique_soldiers'] }}</div>
    </div>
    <div class="summary-row">
        <div class="summary-label">Unfulfilled Duties:</div>
        <div class="summary-value">{{ $summary['unfulfilled_duties'] }}</div>
    </div>
    <div class="summary-row">
        <div class="summary-label">Average Duties per Soldier:</div>
        <div class="summary-value">{{ $summary['average_duties_per_soldier'] }}</div>
    </div>
    <div class="divider"></div>

    <div class="section-title">ROSTER DUTIES</div>

    @foreach ($roster_duties as $duty)
        <div class="divider"></div>
        <div class="duty-header">
            Duty: {{ $duty['duty_name'] }} (ID: {{ $duty['duty_id'] }})<br>
            Time: {{ $duty['start_time']->format('H:i') }} – {{ $duty['end_time']->format('H:i') }} | Duration:
            {{ $duty['duration_days'] }} day<br>
            Required Manpower: {{ $duty['required_manpower'] }} | Assigned: {{ $duty['assigned_count'] }} |
            Fulfillment:
            @if ($duty['fulfillment_rate'] >= 100)
                <span class="fulfilled">✅ {{ $duty['fulfillment_rate'] }}%</span>
            @else
                ❌ {{ $duty['fulfillment_rate'] }}%
            @endif
        </div>
        <div class="divider"></div>

        <div class="table-header">👷 Rank Requirements:</div>
        <div class="divider"></div>
        <div class="table-row">
            <div class="table-cell" style="flex: 2;">Rank Name</div>
            <div class="table-cell" style="flex: 1;">Manpower</div>
            <div class="table-cell" style="flex: 1;">Group ID</div>
        </div>
        <div class="divider"></div>

        @foreach ($duty['rank_requirements'] as $requirement)
            <div class="table-row">
                <div class="table-cell" style="flex: 2;">{{ $requirement['rank_name'] }}</div>
                <div class="table-cell" style="flex: 1;">{{ $requirement['manpower'] }}</div>
                <div class="table-cell" style="flex: 1;">{{ $requirement['group_id'] ?? 'N/A' }}</div>
            </div>
        @endforeach
        <div class="divider"></div>

        <div class="table-header">🪖 Assigned Soldiers:</div>
        <div class="divider"></div>
        <div class="table-row">
            <div class="table-cell" style="flex: 1;">ID</div>
            <div class="table-cell" style="flex: 2;">Army No</div>
            <div class="table-cell" style="flex: 4;">Full Name</div>
            <div class="table-cell" style="flex: 2;">Rank</div>
            <div class="table-cell" style="flex: 2;">Company</div>
        </div>
        <div class="divider"></div>

        @if (count($duty['assigned_soldiers']) > 0)
            @foreach ($duty['assigned_soldiers'] as $soldier)
                <div class="table-row">
                    <div class="table-cell" style="flex: 1;">{{ $soldier['soldier_id'] }}</div>
                    <div class="table-cell" style="flex: 2;">{{ $soldier['army_no'] }}</div>
                    <div class="table-cell" style="flex: 4;">{{ $soldier['full_name'] }}</div>
                    <div class="table-cell" style="flex: 2;">{{ $soldier['rank'] }}</div>
                    <div class="table-cell" style="flex: 2;">{{ $soldier['company'] }}</div>
                </div>
            @endforeach
        @else
            <div class="table-row">
                <div class="table-cell" style="flex: 10;">No soldiers assigned</div>
            </div>
        @endif
        <div class="divider"></div>
    @endforeach

    <div class="section-title">FIXED DUTIES</div>

    @if (isset($fixed_duties) && count($fixed_duties) > 0)
        @foreach ($fixed_duties as $duty)
            <div class="divider"></div>
            <div class="duty-header">
                Duty: {{ $duty['duty_name'] }} (ID: {{ $duty['duty_id'] }})<br>
                Time: {{ $duty['start_time']->format('H:i') }} – {{ $duty['end_time']->format('H:i') }} | Type: Fixed
                Assignment
            </div>
            <div class="divider"></div>

            <div class="table-header">🪖 Assigned Soldier:</div>
            <div class="divider"></div>
            <div class="table-row">
                <div class="table-cell" style="flex: 1;">ID</div>
                <div class="table-cell" style="flex: 2;">Army No</div>
                <div class="table-cell" style="flex: 4;">Full Name</div>
                <div class="table-cell" style="flex: 2;">Rank</div>
                <div class="table-cell" style="flex: 2;">Company</div>
            </div>
            <div class="divider"></div>

            <div class="table-row">
                <div class="table-cell" style="flex: 1;">{{ $duty['soldier_id'] }}</div>
                <div class="table-cell" style="flex: 2;">{{ $duty['army_no'] }}</div>
                <div class="table-cell" style="flex: 4;">{{ $duty['full_name'] }}</div>
                <div class="table-cell" style="flex: 2;">{{ $duty['rank'] }}</div>
                <div class="table-cell" style="flex: 2;">{{ $duty['company'] }}</div>
            </div>
            <div class="divider"></div>
        @endforeach
    @else
        <div class="divider"></div>
        <div class="center">No fixed duties assigned</div>
        <div class="divider"></div>
    @endif

    <div class="divider" style="margin-top: 30px;"></div>
    <div class="center">
        Report Generated Automatically on {{ $date->format('d M Y H:i:s') }}<br>
        Military Personnel Management System
    </div>
    <div class="divider"></div>
</body>

</html>
