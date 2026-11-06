
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Greenhouse Monitoring</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 25px;
            color: #1e293b;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .header, .card, .panel {
            background: white;
            padding: 22px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px #00000010;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .value {
            font-size: 34px;
            font-weight: bold;
            margin: 10px 0;
        }

        .muted {
            color: #64748b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .refresh {
            padding: 10px 15px;
            background: #166534;
            color: white;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <h1>Smart Greenhouse Monitoring</h1>
        <p class="muted">ESP32 + Laravel + MySQL IoT System</p>

        <button class="refresh" onclick="location.reload()">
            Refresh Readings
        </button>
    </div>

    <div class="cards">

        <div class="card">
            <h3>Temperature</h3>

            @if ($latest)
                <div class="value">
                    {{ number_format($latest->temperature, 2) }} °C
                </div>
            @else
                <div class="value">-- °C</div>
            @endif

            <p class="muted">Latest temperature reading</p>
        </div>

        <div class="card">
            <h3>Humidity</h3>

            @if ($latest)
                <div class="value">
                    {{ number_format($latest->humidity, 2) }} %
                </div>
            @else
                <div class="value">-- %</div>
            @endif

            <p class="muted">Latest humidity reading</p>
        </div>

        <div class="card">
            <h3>Device ID</h3>

            <div class="value" style="font-size: 22px;">
                {{ $latest->device_id ?? 'No device' }}
            </div>

            <p class="muted">Connected sensor device</p>
        </div>

        <div class="card">
            <h3>Last Received</h3>

            <div class="value" style="font-size: 20px;">
                {{ $latest ? $latest->created_at->format('M d, Y h:i A') : 'No data' }}
            </div>

            <p class="muted">Date and time saved by Laravel</p>
        </div>

    </div>

    <div class="panel">
        <h2>Recent Sensor Readings</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Device ID</th>
                        <th>Temperature (°C)</th>
                        <th>Humidity (%)</th>
                        <th>Recorded At</th>
                        <th>Received At</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($readings as $reading)
                        <tr>
                            <td>{{ $reading->id }}</td>
                            <td>{{ $reading->device_id }}</td>
                            <td>{{ number_format($reading->temperature, 2) }}</td>
                            <td>{{ number_format($reading->humidity, 2) }}</td>
                            <td>
                                {{ $reading->recorded_at
                                    ? $reading->recorded_at->format('M d, Y h:i A')
                                    : 'N/A' }}
                            </td>
                            <td>
                                {{ $reading->created_at->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;">
                                No sensor readings available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
