<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Manpower Distribution Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        th,
        td {
            border: 2px solid #000000;
            padding: 0;
            text-align: left !important;
            line-height: 1;
        }

        th {
            background-color: #D9D9D9;
            font-weight: bold;
            height: 100px;
        }
    </style>
</head>

<body>
    <!-- Header Table -->
    <table>
        <tr class="header-row">
            <td style="padding:4px;text-align:center" colspan="{{ count($otherRanks) + 3 }}">
                <strong>Manpower Distribution Report {{ $date }}</strong><br>
                <br>
            </td>
        </tr>
    </table>

    <!-- Initialize all variables with defaults to prevent undefined errors -->
    @php
        use App\Helpers\ManpowerViewHelper;

        $receivedManpower = $receivedManpower ?? collect();
        $receivedOfficerTotals = $receivedOfficerTotals ?? [];
        $leaveManpower = $leaveManpower ?? collect();
        $leaveOfficerTotals = $leaveOfficerTotals ?? [];
        $withoutLeaveManpower = $withoutLeaveManpower ?? collect();
        $withoutLeaveOfficerTotals = $withoutLeaveOfficerTotals ?? [];
        $leaveTypeManpower = $leaveTypeManpower ?? collect();
        $leaveTypeTotals = $leaveTypeTotals ?? [];
        $leaveTypeCompanyTotals = $leaveTypeCompanyTotals ?? [];
    @endphp

    <!-- Common Header for Rank-based Tables -->
    <table>
        <thead>
            <tr class="section-title-row">
                <td colspan="{{ count($otherRanks) + 3 }}" style="text-align: center">Auth Manpower</td>
            </tr>
            <tr>
                <th>Coy</th>
                <th class="vertical-header">Officers</th>
                @foreach ($otherRanks as $rank)
                    <th class="vertical-header">{{ $rank->name }}</th>
                @endforeach
                <th class="vertical-header">Total</th>
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
            <td colspan="{{ count($otherRanks) + 3 }}" style="text-align: center">Held Manpower</td>
        </tr>
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



    <!-- Manpower without Leave Table -->
    <table>
        <tr class="section-title-row">
            <td colspan="{{ count($otherRanks) + 3 }}" style="text-align: center">Present</td>
        </tr>
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
    <!-- Manpower with Leave Table -->
    <table>
        <tr class="section-title-row">
            <td colspan="{{ count($otherRanks) + 3 }}" style="text-align: center">Absent</td>
        </tr>
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
    <!-- Leave Types Distribution Table -->
    <table>
        <tr class="section-title-row">
            <td colspan="{{ count($leaveTypes) + 3 }}" style="text-align: center">Details of Absent
            </td>
        </tr>
        <thead>
            <tr>
                <th>Coy</th>
                @foreach ($leaveTypes as $leaveType)
                    <th class="vertical-header">{{ $leaveType->name }}</th>
                @endforeach
                <th class="vertical-header">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                <tr>
                    <td>{{ $company->name }}</td>
                    @foreach ($leaveTypes as $leaveType)
                        <td>{{ $leaveTypeManpower[$company->id][$leaveType->id]->count ?? 0 }}</td>
                    @endforeach
                    <td>{{ $leaveTypeCompanyTotals[$company->id] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>Total</td>
                @foreach ($leaveTypes as $leaveType)
                    <td>{{ $leaveTypeTotals[$leaveType->id] ?? 0 }}</td>
                @endforeach
                <td>{{ array_sum($leaveTypeCompanyTotals) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Signature Section for Excel -->
    <table class="signature-table">
        <tr>
            <td class="signature-cell" colspan="3">BSM ______</td>
            <td class="signature-cell" colspan="3">NSA ______</td>
            <td class="signature-cell" colspan="3">Adjt ______</td>
            <td class="signature-cell" colspan="3">21C ______</td>
            <td class="signature-cell" colspan="3">CO ______</td>
        </tr>
    </table>
</body>

</html>
