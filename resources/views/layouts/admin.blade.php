<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Portal') | Smart Asset Management System</title>

    @include('partials.flash-messages')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="portal-body">
    <div class="portal-shell">
        @include('partials.admin-sidebar')

        <div class="portal-main">
            @include('partials.topbar')

            <main class="content-area">
                @include('partials.breadcrumbs')

                @yield('content')
            </main>

            @include('partials.footer')
        </div>
    </div>
</body>
</html>