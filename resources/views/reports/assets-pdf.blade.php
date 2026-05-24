<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Assets Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .metric-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .metric-label {
            font-weight: bold;
            color: #555;
        }
        .metric-value {
            font-size: 24px;
            color: #333;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f0f0f0;
            padding: 10px;
            text-align: left;
            border-bottom: 2px solid #333;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #888;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Assets Report</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="metrics">
        <div class="metric-box">
            <div class="metric-label">Total Assets</div>
            <div class="metric-value">{{ $report['total_assets'] }}</div>
        </div>
        <div class="metric-box">
            <div class="metric-label">Total Value</div>
            <div class="metric-value">${{ number_format($report['total_value'], 2) }}</div>
        </div>
        <div class="metric-box">
            <div class="metric-label">By Status</div>
            <div class="metric-value">{{ count($report['by_status']) }}</div>
        </div>
    </div>

    <h2>By Status</h2>
    <table>
        <thead>
            <tr>
                <th>Status</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['by_status'] as $status)
            <tr>
                <td>{{ $status['status'] }}</td>
                <td>{{ $status['count'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>By Type</h2>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['by_type'] as $type)
            <tr>
                <td>{{ $type['type'] }}</td>
                <td>{{ $type['count'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>By Department</h2>
    <table>
        <thead>
            <tr>
                <th>Department</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['by_department'] as $dept)
            <tr>
                <td>{{ $dept['department'] }}</td>
                <td>{{ $dept['count'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is an automated report. Please contact administrator for inquiries.</p>
    </div>
</body>
</html>
