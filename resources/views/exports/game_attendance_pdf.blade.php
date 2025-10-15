<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    <style>
        @page {
            margin: 10mm;
            size: legal portrait;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            margin: 10px;
            padding: 0;
            line-height: 1.1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
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
            padding: 4px 2px;
        }

        th {
            background-color: #bdbdbd;
            font-weight: bold;
            padding: 8px 4px;
        }

        /* Vertical header container */
        .vertical-header {
            position: relative;
            height: 80px;
            width: 100%;
            margin: 0;
            padding: 8px 0;
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

        .category-row {
            background-color: #f5f5f5;
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

        /* Summary columns styling */
        th.summary-col {
            height: auto;
            padding: 8px 4px;
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

        /* Format 2 specific styles */
        table.format2-table {
            width: 50%;
            /* table-layout: auto; */
        }

        table.format1 {
            width: 50%;
            /* table-layout: auto; */
        }


        table.format2-table th:first-child,
        table.format2-table td:first-child {
            text-align: left;
            padding-left: 8px;
            width: 40%;
            min-width: 200px;
        }

        table.format2-table th:not(:first-child):not(:last-child),
        table.format2-table td:not(:first-child):not(:last-child) {
            width: auto;
            min-width: 50px;
        }

        table.format2-table th:last-child,
        table.format2-table td:last-child {
            width: 60px;
            min-width: 60px;
            font-weight: bold;
        }

        /* Format 3 specific styles */
        .col-serial {
            width: 40px;
        }

        .col-army-no {
            width: 80px;
        }

        .col-rank {
            width: 70px;
        }

        .col-name {
            width: 150px;
            text-align: left;
            padding-left: 8px;
        }

        .col-company-small {
            width: 80px;
        }

        .col-category {
            width: 100px;
        }

        .col-details {
            width: 200px;
            text-align: left;
            padding-left: 8px;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
            background-color: #f9f9f9;
            border: 1px solid #000;
        }

        /* Ensure single page fit */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            tfoot {
                display: table-footer-group;
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

        .page-break {
            page-break-after: always;
        }

        /* Text alignment utilities */
        .text-left {
            text-align: left;
            padding-left: 8px;
        }

        .text-center {
            text-align: center;
        }

        /* Spacing control */
        .spacing-small {
            margin-bottom: 2px;
        }

        .spacing-medium {
            margin-bottom: 6px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <table class="spacing-small format1">
        <tr class="header-row">
            <td colspan="100%">
                {{ $reportTitle }}<br>
                Date: {{ $date }}
            </td>
        </tr>
    </table>

    <!-- Format 1: Summary by Company and Rank Type -->
    <table class="spacing-medium format1">
        <thead>
            <tr>
                <th>Coy</th>
                @foreach ($rankTypes as $rankType)
                    <th>
                        <div class="vertical-header">
                            <div class="vertical-text">{{ $rankType }}</div>
                        </div>
                    </th>
                @endforeach
                <th>

                    <div class="vertical-header">
                        <div class="vertical-text">Total</div>
                    </div>
                </th>
                <th>

                    <div class="vertical-header">
                        <div class="vertical-text">Excused</div>
                    </div>
                </th>
                <th>

                    <div class="vertical-header">
                        <div class="vertical-text">All Total</div>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($format1Data as $row)
                <tr class="{{ $row['company'] === 'Total' ? 'total-row' : '' }}">
                    <td>{{ $row['company'] }}</td>
                    @foreach ($rankTypes as $rankType)
                        <td>{{ $row[$rankType] ?? 0 }}</td>
                    @endforeach
                    <td>{{ $row['Total'] }}</td>
                    <td>{{ $row['Excused'] }}</td>
                    <td>{{ $row['All Total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Format 2: Exclusion by Duty / Appointment Type -->
    <table class="format2-table spacing-medium">
        <tr class="">
            <td colspan="{{ count($companies) + 2 }}" style="text-align: center;font-weight:bold">Exclusion lists</td>
        </tr>
        <thead>
            <tr>
                <th>Explanation</th>
                @foreach ($companies as $company)
                    <th>{{ $company }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $currentCategory = '';
            @endphp
            @foreach ($format2Data as $row)
                @php
                    $isNewCategory = $row['category'] !== $currentCategory && $row['category'] !== 'Total';
                    $currentCategory = $row['category'];
                @endphp

                <tr class="{{ $row['category'] === 'Total' ? 'total-row' : ($isNewCategory ? 'category-row' : '') }}">
                    <td>
                        @if ($row['category'] !== 'Total')
                            {{ $row['category'] }} - {{ $row['type'] }}
                        @else
                            {{ $row['type'] }}
                        @endif
                    </td>
                    @foreach ($companies as $company)
                        <td>{{ $row[$company] ?? 0 }}</td>
                    @endforeach
                    <td>{{ $row['Total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Format 3: Detailed List of Excused Soldiers -->
    <div style="page-break-before: always;"></div>


    <table class="spacing-medium">
        <tr class="">
            <td colspan="7" style="text-align: center;font-weight:bold">Detailed List of Excused Soldiers</td>
        </tr>
        @if (count($format3Data) > 0)
            <thead>
                <tr>
                    <th class="col-serial">S.No</th>
                    <th class="col-army-no">Army No</th>
                    <th class="col-rank">Rank</th>
                    <th class="col-name">Name</th>
                    <th class="col-company-small">Company</th>
                    <th class="col-category">Excusal Category</th>
                    <th class="col-details">Excusal Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($format3Data as $soldier)
                    <tr>
                        <td class="col-serial">{{ $soldier['sl_no'] }}</td>
                        <td class="col-army-no">{{ $soldier['army_no'] }}</td>
                        <td class="col-rank">{{ $soldier['rank'] }}</td>
                        <td class="col-name">{{ $soldier['name'] }}</td>
                        <td class="col-company-small">{{ $soldier['company'] }}</td>
                        <td class="col-category">{{ $soldier['excusal_category'] }}</td>
                        <td class="col-details">{{ $soldier['excusal_details'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="6" class="text-left">Total Excused Soldiers:</td>
                    <td><strong>{{ count($format3Data) }}</strong></td>
                </tr>
            </tfoot>
        @else
            <tbody>
                <tr>
                    <td colspan="7" class="no-data">
                        No soldiers are excused for {{ $date }}
                    </td>
                </tr>
            </tbody>
        @endif
    </table>

    <div class="footer">
        Generated: {{ now()->format('d M Y H:i:s') }} | Confidential - For Official Use Only
    </div>
</body>

</html>
