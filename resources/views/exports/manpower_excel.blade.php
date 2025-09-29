<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Manpower Distribution Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .header-row td {
            border: none;
            font-size: 14px;
            font-weight: bold;
            padding: 10px;
        }

        .note-row td {
            background-color: #fffbeb;
            border: 1px solid #ddd;
            font-size: 11px;
            text-align: center;
            padding: 10px;
        }

        .section-title-row td {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            font-weight: bold;
            font-size: 14px;
            padding: 8px;
            text-align: center;
        }

        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- Header Table -->
    <table>
        <tr class="header-row">
            <td colspan="100%" style="text-align: center;">
                Manpower Distribution Report<br>
                {{ $formattedDate }}
            </td>
        </tr>
    </table>

    <!-- Note Table -->


    <!-- Initialize all variables with defaults to prevent undefined errors -->
    @php
        use App\Helpers\ManpowerViewHelper;

        $receivedManpower = $receivedManpower ?? collect();
        $receivedOfficerTotals = $receivedOfficerTotals ?? [];
        $leaveManpower = $leaveManpower ?? collect();
        $leaveOfficerTotals = $leaveOfficerTotals ?? [];
        $withoutLeaveManpower = $withoutLeaveManpower ?? collect();
        $withoutLeaveOfficerTotals = $withoutLeaveOfficerTotals ?? [];
    @endphp

    <!-- Planned Manpower Distribution Table -->
    <table>
        <tr class="section-title-row">
            <td colspan="100%"><strong> Planned Manpower Distribution </strong> </td>
        </tr>
        <thead>
            <tr>
                <th>Company</th>
                <th>Officers</th>
                @foreach ($otherRanks as $rank)
                    <th>{{ $rank->name }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                <tr>
                    <td>{{ $company->name }}</td>
                    <td>{{ $officerTotals[$company->id] ?? 0 }}</td>
                    @foreach ($otherRanks as $rank)
                        <td>{{ $manpower[$company->id][$rank->id]->manpower_number ?? 0 }}</td>
                    @endforeach
                    <td>
                        {{ ($officerTotals[$company->id] ?? 0) +
                            $otherRanks->sum(function ($rank) use ($company, $manpower) {
                                return $manpower[$company->id][$rank->id]->manpower_number ?? 0;
                            }) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Total</td>
                <td>{{ array_sum($officerTotals) }}</td>
                @foreach ($otherRanks as $rank)
                    <td>
                        {{ $companies->sum(function ($company) use ($rank, $manpower) {
                            return $manpower[$company->id][$rank->id]->manpower_number ?? 0;
                        }) }}
                    </td>
                @endforeach
                <td>
                    {{ array_sum($officerTotals) +
                        $otherRanks->sum(function ($rank) use ($companies, $manpower) {
                            return $companies->sum(function ($company) use ($rank, $manpower) {
                                return $manpower[$company->id][$rank->id]->manpower_number ?? 0;
                            });
                        }) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Received Manpower Distribution Table -->
    <table>
        <tr class="section-title-row">
            <td colspan="100%">Received Manpower Distribution</td>
        </tr>
        <thead>
            <tr>
                <th>Company</th>
                <th>Officers</th>
                @foreach ($otherRanks as $rank)
                    <th>{{ $rank->name }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                @php
                    $companyId = $company->id;
                    $companyReceivedData = $receivedManpower[$companyId] ?? null;
                @endphp
                <tr>
                    <td>{{ $company->name }}</td>
                    <td>{{ $receivedOfficerTotals[$companyId] ?? 0 }}</td>
                    @foreach ($otherRanks as $rank)
                        @php
                            $rankId = $rank->id;
                            $count = 0;
                            if ($companyReceivedData && isset($companyReceivedData[$rankId])) {
                                $count = $companyReceivedData[$rankId]->count ?? 0;
                            }
                        @endphp
                        <td>{{ $count }}</td>
                    @endforeach
                    <td>
                        {{ ManpowerViewHelper::calculateReceivedCompanyTotal($companyId, $receivedOfficerTotals, $otherRanks, $receivedManpower) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Total</td>
                <td>{{ array_sum($receivedOfficerTotals) }}</td>
                @foreach ($otherRanks as $rank)
                    <td>
                        {{ ManpowerViewHelper::calculateReceivedRankTotal($rank->id, $companies, $receivedManpower) }}
                    </td>
                @endforeach
                <td>
                    {{ ManpowerViewHelper::calculateReceivedGrandTotal($receivedOfficerTotals, $otherRanks, $companies, $receivedManpower) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Manpower with Leave Table -->
    <table>
        <tr class="section-title-row">
            <td colspan="100%">Manpower with Leave</td>
        </tr>
        <thead>
            <tr>
                <th>Company</th>
                <th>Officers</th>
                @foreach ($otherRanks as $rank)
                    <th>{{ $rank->name }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                @php
                    $companyId = $company->id;
                    $companyLeaveData = $leaveManpower[$companyId] ?? null;
                @endphp
                <tr>
                    <td>{{ $company->name }}</td>
                    <td>{{ $leaveOfficerTotals[$companyId] ?? 0 }}</td>
                    @foreach ($otherRanks as $rank)
                        @php
                            $rankId = $rank->id;
                            $count = 0;
                            if ($companyLeaveData && isset($companyLeaveData[$rankId])) {
                                $count = $companyLeaveData[$rankId]->count ?? 0;
                            }
                        @endphp
                        <td>{{ $count }}</td>
                    @endforeach
                    <td>
                        {{ ManpowerViewHelper::calculateLeaveCompanyTotal($companyId, $leaveOfficerTotals, $otherRanks, $leaveManpower) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Total</td>
                <td>{{ array_sum($leaveOfficerTotals) }}</td>
                @foreach ($otherRanks as $rank)
                    <td>
                        {{ ManpowerViewHelper::calculateLeaveRankTotal($rank->id, $companies, $leaveManpower) }}
                    </td>
                @endforeach
                <td>
                    {{ ManpowerViewHelper::calculateLeaveGrandTotal($leaveOfficerTotals, $otherRanks, $companies, $leaveManpower) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Manpower without Leave Table -->
    <table>
        <tr class="section-title-row">
            <td colspan="100%">Manpower without Leave</td>
        </tr>
        <thead>
            <tr>
                <th>Company</th>
                <th>Officers</th>
                @foreach ($otherRanks as $rank)
                    <th>{{ $rank->name }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                @php
                    $companyId = $company->id;
                    $companyWithoutLeaveData = $withoutLeaveManpower[$companyId] ?? null;
                @endphp
                <tr>
                    <td>{{ $company->name }}</td>
                    <td>{{ $withoutLeaveOfficerTotals[$companyId] ?? 0 }}</td>
                    @foreach ($otherRanks as $rank)
                        @php
                            $rankId = $rank->id;
                            $count = 0;
                            if ($companyWithoutLeaveData && isset($companyWithoutLeaveData[$rankId])) {
                                $count = $companyWithoutLeaveData[$rankId]->count ?? 0;
                            }
                        @endphp
                        <td>{{ $count }}</td>
                    @endforeach
                    <td>
                        {{ ManpowerViewHelper::calculateWithoutLeaveCompanyTotal($companyId, $withoutLeaveOfficerTotals, $otherRanks, $withoutLeaveManpower) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Total</td>
                <td>{{ array_sum($withoutLeaveOfficerTotals) }}</td>
                @foreach ($otherRanks as $rank)
                    <td>
                        {{ ManpowerViewHelper::calculateWithoutLeaveRankTotal($rank->id, $companies, $withoutLeaveManpower) }}
                    </td>
                @endforeach
                <td>
                    {{ ManpowerViewHelper::calculateWithoutLeaveGrandTotal($withoutLeaveOfficerTotals, $otherRanks, $companies, $withoutLeaveManpower) }}
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
