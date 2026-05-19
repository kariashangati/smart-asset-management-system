<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentication') | Smart Asset Management System</title>

    @include('partials.flash-messages')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
    <main class="auth-shell">
        <section class="auth-brand-panel">
            <span class="brand-mark large">SAMS</span>
            <h1>Smart Asset Management System</h1>
            <p>
                A secure portal for tracking institutional assets,
                geofences, alerts, and embedded GPS device integration.
            </p>
        </section>

        <section class="auth-form-panel">
            @yield('content')
        </section>
    </main>
</body>
</html>