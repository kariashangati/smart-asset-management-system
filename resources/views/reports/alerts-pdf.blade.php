<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Alerts Report</title>
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
        .critical {
            color: #dc2626;
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
        <h1>🚨 Alerts Report</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="metrics">
        <div class="metric-box">
            <div class="metric-label">Total Alerts</div>
            <div class="metric-value">{{ $report['total_alerts'] }}</div>
        </div>
        <div class="metric-box">
            <div class="metric-label critical">Critical Alerts</div>
            <div class="metric-value critical">{{ $report['critical_alerts'] }}</div>
        </div>
        <div class="metric-box">
            <div class="metric-label">Alert Types</div>
            <div class="metric-value">{{ count($report['by_type']) }}</div>
        </div>
    </div>

    <h2>Alerts By Severity</h2>
    <table>
        <thead>
            <tr>
                <th>Severity</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['by_severity'] as $severity)
            <tr>
                <td>{{ $severity['severity'] }}</td>
                <td>{{ $severity['count'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Recent Alerts</h2>
    <table>
        <thead>
            <tr>
                <th>Asset</th>
                <th>Type</th>
                <th>Severity</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['alerts_list'] as $alert)
            <tr>
                <td>{{ $alert['asset_name'] }}</td>
                <td>{{ $alert['type'] }}</td>
                <td>{{ ucfirst($alert['severity']) }}</td>
                <td>{{ ucfirst($alert['status']) }}</td>
                <td>{{ $alert['created_at'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is an automated report. Please contact administrator for inquiries.</p>
    </div>
</body>
</html>
