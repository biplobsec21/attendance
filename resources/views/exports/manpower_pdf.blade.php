<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Manpower Distribution Report</title>
    <style>
        @page {
            margin: 5;
            size: legal portrait;
        }


        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            margin: 10;
            padding: 0;
            line-height: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
        }

        /* Data cells with proper padding */
        td:not(:first-child) {
            padding: 3px 1.5px;
        }

        th {
            background-color: #bdbdbd;
            font-weight: bold;
            padding: 6px 3px;
        }

        /* Vertical header container */
        .vertical-header {
            position: relative;
            height: 50px;
            width: 100%;
            margin: 0;
            padding: 2px 0;
        }

        /* Vertical text using transform - Bottom to Top */
        .vertical-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-90deg);
            transform-origin: center center;
            white-space: nowrap;
            font-size: 10px;
            font-weight: bold;
            width: 80px;
            text-align: center;
        }

        /* Vertical header container */
        .vertical-header2 {
            position: relative;
            height: 60px;
            width: 100%;
            margin: 0;
            padding: 2px 0;
        }

        /* Vertical text using transform - Bottom to Top */
        .vertical-text2 {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-90deg);
            transform-origin: center center;
            white-space: nowrap;
            font-size: 10px;
            font-weight: bold;
            width: 80px;
            text-align: center;
        }

        .header-row td {
            border: 1px solid #000000;
            font-size: 13px;
            font-weight: bold;
            padding: 8px;
            background-color: #E7E6E6;
            text-align: center;
        }

        .section-title-row td {
            background-color: #D9D9D9;
            border: 1px solid #000000;
            font-weight: bold;
            font-size: 11px;
            padding: 6px;
            text-align: center;
        }

        .total-row {
            background-color: #F2F2F2;
            font-weight: bold;
        }

        .total-row td {
            font-weight: bold;
        }

        /* Company column */
        td:first-child,
        th:first-child {
            text-align: left;
            padding-left: 8px;
            width: 80px;
            min-width: 80px;
            font-size: 11px;
        }

        /* Data columns - proper sizing */
        td:not(:first-child) {
            width: auto;
            min-width: 28px;
            font-size: 11px;
        }

        th:not(:first-child) {
            width: auto;
            min-width: 28px;
            height: 80px;
        }

        .main-header {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            background-color: #E7E6E6;
        }

        .date-header {
            text-align: center;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .footer {
            text-align: right;
            font-size: 9px;
            margin-top: 10px;
            padding: 4px 8px;
            color: #666;
        }

        /* Signature section */
        .signature-section {
            margin-top: 30px;
            width: 100%;
        }

        .signature-line {
            display: inline-block;
            width: 18%;
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            margin: 0 1%;
        }

        .signature-line:first-child {
            margin-left: 0;
        }

        .signature-line:last-child {
            margin-right: 0;
        }

        /* Ensure single page fit */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            table {
                page-break-inside: avoid;
            }
        }

        /* Proper row spacing */
        tbody tr {
            height: auto;
        }

        tbody td {
            height: auto;
            padding: 4px 2px;
        }

        tfoot tr {
            height: auto;
        }

        tfoot td {
            height: auto;
            padding: 4px 2px;
        }

        thead th {
            padding: 8px 4px;
        }
    </style>
</head>

<body>
    <!-- Main Header -->
    <div style="text-align: center;font-weight:bold;">
        Parade State <br> 21 EB
    </div>
    <div style="text-align: right;font-weight:bold;">
        Date: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
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
        $leaveTypeManpower = $leaveTypeManpower ?? collect();
        $leaveTypeTotals = $leaveTypeTotals ?? [];
        $leaveTypeCompanyTotals = $leaveTypeCompanyTotals ?? [];
    @endphp

    <!-- Auth Manpower Section -->
    <table>
        <tr class="" style="text-align: center;font-weight:bold;">
            <td colspan="{{ count($otherRanks) + 3 }}" style="text-align: center">Auth Manpower</td>
        </tr>
        <thead>
            <tr>
                <th style="text-align: left;">Coy</th>
                <th>
                    <div class="vertical-header">
                        <div class="vertical-text">Offrs</div>
                    </div>
                </th>
                @foreach ($otherRanks as $rank)
                    <th>
                        <div class="vertical-header">
                            <div class="vertical-text">{{ $rank->name }}</div>
                        </div>
                    </th>
                @endforeach
                <th>
                    <div class="vertical-header">
                        <div class="vertical-text">Total</div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                <tr>
                    <td style="text-align: left;">{{ $company->name }}</td>
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
                <td style="text-align: left;">Total</td>
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
    <div style="text-align: center;font-weight:bold;">
        Held Manpower
    </div>
    <!-- Received Manpower Distribution Table -->
    <table>
        {{-- <thead>
            <tr class="" style="text-align: center;">
                <td colspan="{{ count($otherRanks) + 3 }}"></td>
            </tr>
        </thead> --}}
        <tbody>
            @foreach ($companies as $company)
                @php
                    $companyId = $company->id;
                    $companyReceivedData = $receivedManpower[$companyId] ?? null;
                @endphp
                <tr>
                    <td style="text-align: left;">{{ $company->name }}</td>
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
                <td style="text-align: left;">Total</td>
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



    <!-- Present Manpower Table -->
    <div style="text-align: center;font-weight:bold;">
        Present
    </div>
    <table>

        {{-- <thead>
            <tr class="" style="text-align: center;">
                <td colspan="{{ count($otherRanks) + 3 }}">Present Manpower</td>
            </tr>
        </thead> --}}
        <tbody>
            @foreach ($companies as $company)
                @php
                    $companyId = $company->id;
                    $companyWithoutLeaveData = $withoutLeaveManpower[$companyId] ?? null;
                @endphp
                <tr>
                    <td style="text-align: left;">{{ $company->name }}</td>
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
                <td style="text-align: left;">Total</td>
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
    <div style="text-align: center;font-weight:bold;">
        Absent
    </div>
    <table>

        {{-- <thead>
            <tr class="" style="text-align: center;">
                <td colspan="{{ count($otherRanks) + 3 }}">Leave Manpower</td>
            </tr>
        </thead> --}}
        <tbody>
            @foreach ($companies as $company)
                @php
                    $companyId = $company->id;
                    $companyLeaveData = $leaveManpower[$companyId] ?? null;
                @endphp
                <tr>
                    <td style="text-align: left;">{{ $company->name }}</td>
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
                <td style="text-align: left;">Total</td>
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
    <div style="text-align: center;font-weight:bold;">
        Details of Absent
    </div>

    <table>
        {{-- <tr class="" style="text-align: center;">
            <td colspan="{{ count($leaveTypes) + 2 }}">Leave Details</td>
        </tr> --}}
        <thead>
            <tr>
                <th style="text-align: left;">Coy</th>
                @foreach ($leaveTypes as $leaveType)
                    <th>
                        <div class="vertical-header2">
                            <div class="vertical-text2">{{ $leaveType->name }}</div>
                        </div>
                    </th>
                @endforeach
                <th>
                    <div class="vertical-header2">
                        <div class="vertical-text2">Total</div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companies as $company)
                <tr>
                    <td style="text-align: left;">{{ $company->name }}</td>
                    @foreach ($leaveTypes as $leaveType)
                        <td>{{ $leaveTypeManpower[$company->id][$leaveType->id]->count ?? 0 }}</td>
                    @endforeach
                    <td>{{ $leaveTypeCompanyTotals[$company->id] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td style="text-align: left;">Total</td>
                @foreach ($leaveTypes as $leaveType)
                    <td>{{ $leaveTypeTotals[$leaveType->id] ?? 0 }}</td>
                @endforeach
                <td>{{ array_sum($leaveTypeCompanyTotals) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- <div class="footer">
        <!-- Signature Section -->
        Generated on: {{ now()->format('F d, Y H:i:s') }}
    </div> --}}
    <div class="signature-section">
        <div class="signature-line">BSM ______</div>
        <div class="signature-line">NSA ______</div>
        <div class="signature-line">Adjt ______</div>
        <div class="signature-line">21C ______</div>
        <div class="signature-line">CO ______</div>
    </div>
</body>

</html>
