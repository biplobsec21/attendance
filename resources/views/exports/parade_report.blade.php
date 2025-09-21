<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Parade Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .separator {
            height: 20px;
        }
    </style>
</head>

<body>
    <div class="title">Parade Report 21EB Date: {{ $formattedDate }}</div>

    <div class="subtitle">Company and Rank Summary</div>
    <table>
        <thead>
            <tr>
                <th>Company Name</th>
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
                    <td>{{ $row['company_name'] }}</td>
                    @foreach ($rankTypes as $rankType)
                        <td>{{ $row[$rankType] ?? 0 }}</td>
                    @endforeach
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $row['appointed'] }}</td>
                    <td>{{ $row['final_total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="separator"></div>

    <div class="subtitle">Parade Report</div>
    <table>
        <thead>
            <tr>
                <th>Appointment Name</th>
                @foreach ($companies as $company)
                    <th>{{ $company }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paradeData as $row)
                <tr>
                    <td>{{ $row['appointment_name'] }}</td>
                    @foreach ($companies as $company)
                        <td>{{ $row[$company] ?? 0 }}</td>
                    @endforeach
                    <td>{{ $row['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
