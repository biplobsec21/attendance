<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            /* Increased from 10px */
            margin: 15px;
            /* Increased margin */
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 20px;
            /* Increased from 18px */
            margin: 8px 0;
            text-transform: uppercase;
        }

        .header p {
            font-size: 14px;
            /* Increased from 12px */
            margin: 5px 0;
        }

        .section-title {
            font-size: 16px;
            /* Increased from 14px */
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
            /* Increased padding */
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            /* Increased from 10px */
        }

        table td {
            border: 1px solid #333;
            padding: 8px;
            /* Increased padding */
            text-align: center;
            font-size: 11px;
            /* Increased from 9px */
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
            /* Increased from 9px */
            text-align: center;
            color: #666;
        }

        .page-break {
            page-break-after: always;
        }

        /* Specific column widths */
        .col-explanation {
            width: 350px;
            /* Wider for combined category + type */
        }

        .col-company {
            width: 80px;
        }

        .col-total {
            width: 80px;
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
                <th class="col-explanation">Explanation</th> <!-- Combined column -->
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

    <!-- Footer -->
    <div class="footer">
        <p>This report was automatically generated by the Attendance Report Module</p>
        <p>Confidential - For Official Use Only</p>
    </div>
</body>

</html>
