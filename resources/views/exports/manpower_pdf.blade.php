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

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
        }

        .section-title {
            font-weight: bold;
            font-size: 14px;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            margin-bottom: 10px;
        }

        .note-box {
            background-color: #fffbeb;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #f59e0b;
            font-size: 11px;
        }

        .footer {
            text-align: right;
            font-size: 10px;
            margin-top: 20px;
        }

        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Manpower Distribution Report</h1>
        <p>Date: {{ $formattedDate }}</p>
    </div>

    <!-- Initialize all variables with defaults to prevent undefined errors -->
    @php
        use App\Helpers\ManpowerViewHelper;

        $receivedManpower = $receivedManpower ?? collect();
        $receivedOfficerTotals = $receivedOfficerTotals ?? [];
        $leaveManpower = $leaveManpower ?? collect();
        $leaveOfficerTotals = $leaveOfficerTotals ?? [];
        $withoutLeaveManpower = $withoutLeaveManpower ?? collect();
        $withoutLeaveOfficerTotals = $withoutLeaveOfficerTotals ?? [];

        // Debug: Check if data is actually available
        // \Log::info('PDF View Data Check', [
        //     'withoutLeaveManpower_empty' => $withoutLeaveManpower->isEmpty(),
        //     'withoutLeaveManpower_counts' => $withoutLeaveManpower->count(),
        //     'withoutLeaveOfficerTotals' => $withoutLeaveOfficerTotals
        // ]);

    @endphp

    <div class="section-title">Planned Manpower Distribution</div>
    <table>
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

    <div class="section-title">Received Manpower Distribution</div>
    <table>
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

    <div class="section-title">Manpower with Leave</div>
    <table>
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

    <div class="section-title">Manpower without Leave</div>
    <table>
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

                    // Debug individual company data
                    // \Log::info("PDF Company {$companyId} without leave data", [
                    //     'company_data' => $companyWithoutLeaveData ? $companyWithoutLeaveData->toArray() : 'null',
                    //     'officer_total' => $withoutLeaveOfficerTotals[$companyId] ?? 0
                    // ]);

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

    <div class="footer">
        <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
    </div>
</body>

</html>
