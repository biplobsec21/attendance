<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 15px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 20px;
            margin: 8px 0;
            text-transform: uppercase;
        }

        .header p {
            font-size: 14px;
            margin: 5px 0;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 15px;
            padding: 8px 12px;
            background-color: #e0e0e0;
            border-left: 4px solid #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table th {
            background-color: #d3d3d3;
            border: 1px solid #333;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }

        table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }

        table td.text-left {
            text-align: left;
        }

        table tr.total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        table tr.category-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
            text-align: center;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }

        /* Specific column widths */
        .col-explanation {
            width: 350px;
        }

        .col-company {
            width: 80px;
        }

        .col-total {
            width: 80px;
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
    <!-- Header -->
    <div class="header">
        <h1>{{ $reportTitle }}</h1>
        <p><strong>Date:</strong> {{ $date }}</p>
        <p><strong>Generated:</strong> {{ now()->format('d M Y H:i:s') }}</p>
    </div>

    <!-- Format 1: Summary by Company and Rank Type -->
    <div class="section-title">Format 1: Summary by Company and Rank Type</div>

    <table>
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

    <div class="page-break"></div>

    <!-- Format 2: Exclusion by Duty / Appointment Type -->
    <div class="section-title">Format 2: Exclusion lists</div>

    <table>
        <thead>
            <tr>
                <th class="col-explanation">Explanation</th>
                @foreach ($companies as $company)
                    <th class="col-company">{{ $company }}</th>
                @endforeach
                <th class="col-total">Total</th>
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
                    <td class="text-left col-explanation">
                        @if ($row['category'] !== 'Total')
                            {{ $row['category'] }} - {{ $row['type'] }}
                        @else
                            {{ $row['type'] }}
                        @endif
                    </td>
                    @foreach ($companies as $company)
                        <td class="col-company">{{ $row[$company] ?? 0 }}</td>
                    @endforeach
                    <td class="col-total">{{ $row['Total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Format 3: Detailed List of Excused Soldiers -->
    <div class="section-title">Format 3: Detailed List of Excused Soldiers</div>

    @if (count($format3Data) > 0)
        <table>
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
                        <td class="col-name text-left">{{ $soldier['name'] }}</td>
                        <td class="col-company-small">{{ $soldier['company'] }}</td>
                        <td class="col-category">{{ $soldier['excusal_category'] }}</td>
                        <td class="col-details text-left">{{ $soldier['excusal_details'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary for Format 3 -->
        <div style="margin-top: 20px; padding: 10px; background-color: #f0f0f0; border: 1px solid #ddd;">
            <strong>Total Excused Soldiers: {{ count($format3Data) }}</strong>
        </div>
    @else
        <div class="no-data">
            No soldiers are excused for {{ $date }}
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was automatically generated by the Attendance Report Module</p>
        <p>Confidential - For Official Use Only</p>
    </div>
</body>

</html>
