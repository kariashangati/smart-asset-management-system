<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Manager Dashboard | Smart Asset Management System</title>
</head>
<body>
    <h1>Asset Manager Dashboard</h1>

    <p>
        Welcome, <strong>{{ auth()->user()->name }}</strong>.
        You are signed in as an asset manager.
    </p>

    <p>
        This portal will manage assets, tracking, geofences, and alerts.
    </p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>