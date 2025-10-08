<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
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

        .category-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
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
        }

        .col-company-small {
            width: 80px;
        }

        .col-category {
            width: 100px;
        }

        .col-details {
            width: 200px;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <!-- Header Table -->
    <table>
        <tr class="header-row">
            <td colspan="100%" style="text-align: center;">
                {{ $reportTitle }}<br>
                Date: {{ $date }}<br>
                Generated: {{ now()->format('d M Y H:i:s') }}
            </td>
        </tr>
    </table>

    <!-- Format 1: Summary by Company and Rank Type -->
    <table>
        <tr class="section-title-row">
            <td colspan="100%"> Summary by Company and Rank Type</td>
        </tr>
        <thead>
            <tr>
                <th>Company</th>
                @foreach ($rankTypes as $rankType)
                    <th>{{ $rankType }}</th>
                @endforeach
                <th>Total</th>
                <th>Excused</th>
                <th>All Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($format1Data as $row)
                <tr class="{{ $row['company'] === 'Total' ? 'total-row' : '' }}">
                    <td class="text-left">{{ $row['company'] }}</td>
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
    <table>
        <tr class="section-title-row">
            <td colspan="100%"> Exclusion lists</td>
        </tr>
        <thead>
            <tr>
                <th class="text-left">Explanation</th>
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
                    <td class="text-left">
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
    <table>
        <tr class="section-title-row">
            <td colspan="100%"> Detailed List of Excused Soldiers</td>
        </tr>
        @if (count($format3Data) > 0)
            <thead>
                <tr>
                    <th class="col-serial">S.No</th>
                    <th class="col-army-no">Army No</th>
                    <th class="col-rank">Rank</th>
                    <th class="col-name text-left">Name</th>
                    <th class="col-company-small">Company</th>
                    <th class="col-category">Excusal Category</th>
                    <th class="col-details text-left">Excusal Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($format3Data as $soldier)
                    <tr>
                        <td class="col-serial">{{ $soldier['sl_no'] }}</td>
                        <td class="col-army-no">{{ $soldier['army_no'] }}</td>
                        <td class="col-rank">{{ $soldier['rank'] }}</td>
                        <td class="col-name text-left">{{ $soldier['name'] }}</td>
                        <td class="col-company-small">{{ $soldier['company'] }}</td>
                        <td class="col-category">{{ $soldier['excusal_category'] }}</td>
                        <td class="col-details text-left">{{ $soldier['excusal_details'] }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="6" style="text-align: right;">Total Excused Soldiers:</td>
                    <td><strong>{{ count($format3Data) }}</strong></td>
                </tr>
            </tfoot>
        @else
            <tr>
                <td colspan="7" class="no-data">
                    No soldiers are excused for {{ $date }}
                </td>
            </tr>
        @endif
    </table>

    <!-- Footer Note -->
    <table>
        <tr class="note-row">
            <td colspan="100%">
                This report was automatically generated by the Attendance Report Module<br>
                Confidential - For Official Use Only
            </td>
        </tr>
    </table>
</body>

</html>
