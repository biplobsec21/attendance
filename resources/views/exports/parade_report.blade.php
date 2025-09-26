<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Parade Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            /* Smaller font size */
            line-height: 1.2;
        }

        .title {
            text-align: center;
            font-size: 14px;
            /* Smaller title */
            font-weight: bold;
            margin-bottom: 15px;
        }

        .subtitle {
            text-align: center;
            font-size: 12px;
            /* Smaller subtitle */
            font-weight: bold;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            /* Smaller padding */
            font-size: 9px;
            /* Smaller table font */
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            /* Center align headers */
        }

        /* Text cells - left aligned */
        .text-cell {
            text-align: left;
        }

        /* Number cells - center aligned */
        .number-cell {
            text-align: center;
        }

        .separator {
            height: 15px;
        }

        /* Zero value styling - show as dash */
        .zero-value {
            color: #999;
        }
    </style>
</head>

<body>
    <div class="title">Parade Report 21EB Date: {{ $formattedDate }}</div>

    <div class="subtitle">Company and Rank Summary</div>
    <table>
        <thead>
            <tr>
                <th class="text-cell">Company Name</th>
                @foreach ($rankTypes as $rankType)
                    <th>{{ $rankType }}</th>
                @endforeach
                <th>Total</th>
                <th>Appointed</th>
                <th>Final Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($companyRankData as $row)
                <tr>
                    <td class="text-cell">{{ $row['company_name'] }}</td>
                    @foreach ($rankTypes as $rankType)
                        @php
                            $value = $row[$rankType] ?? 0;
                            $displayValue = $value == 0 ? '-' : $value;
                            $cellClass = $value == 0 ? 'number-cell zero-value' : 'number-cell';
                        @endphp
                        <td class="{{ $cellClass }}">{{ $displayValue }}</td>
                    @endforeach
                    @php
                        $totalValue = $row['total'] ?? 0;
                        $appointedValue = $row['appointed'] ?? 0;
                        $finalTotalValue = $row['final_total'] ?? 0;
                    @endphp
                    <td class="number-cell">{{ $totalValue == 0 ? '-' : $totalValue }}</td>
                    <td class="number-cell">{{ $appointedValue == 0 ? '-' : $appointedValue }}</td>
                    <td class="number-cell">{{ $finalTotalValue == 0 ? '-' : $finalTotalValue }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="separator"></div>

    <div class="subtitle">Parade Report</div>
    <table>
        <thead>
            <tr>
                <th class="text-cell">Appointment Name</th>
                @foreach ($companies as $company)
                    <th>{{ $company }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paradeData as $row)
                <tr>
                    <td class="text-cell">{{ $row['appointment_name'] }}</td>
                    @foreach ($companies as $company)
                        @php
                            $value = $row[$company] ?? 0;
                            $displayValue = $value == 0 ? '-' : $value;
                            $cellClass = $value == 0 ? 'number-cell zero-value' : 'number-cell';
                        @endphp
                        <td class="{{ $cellClass }}">{{ $displayValue }}</td>
                    @endforeach
                    @php
                        $totalValue = $row['total'] ?? 0;
                    @endphp
                    <td class="number-cell">{{ $totalValue == 0 ? '-' : $totalValue }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
