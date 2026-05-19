<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Smart Asset Management System</title>
</head>
<body>
    <h1>Admin Dashboard</h1>

    <p>
        Welcome, <strong>{{ auth()->user()->name }}</strong>.
        You are signed in as an administrator.
    </p>

    <p>
        This portal will manage users, assets, devices, alerts, reports, and settings.
    </p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>