@props([
    'title' => 'App',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#3b82f6">

    <title>{{ $title ?? 'Smart Asset Management System' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/dark-mode.css') }}">
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="wrapper">
        <!-- Header/Navigation -->
        <header class="header">
            <div class="header-container">
                <div class="header-left">
                    <a href="{{ route('admin.dashboard') }}" class="logo">
                        <span>📊 Asset Management</span>
                    </a>
                </div>

                <div class="header-right">
                    <!-- Notifications -->
                    <div class="header-item">
                        <button class="notification-btn" title="Notifications">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="badge">3</span>
                        </button>
                    </div>

                    <!-- Theme Toggle -->
                    <div class="header-item">
                        <x-theme-toggle />
                    </div>

                    <!-- User Menu -->
                    <div class="header-item dropdown">
                        <button class="user-menu-btn" title="User menu">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()?->name ?? 'User') }}&background=3b82f6&color=fff" alt="User" class="user-avatar">
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-header">
                                <p class="dropdown-name">{{ auth()->user()?->name ?? 'User' }}</p>
                                <p class="dropdown-email">{{ auth()->user()?->email ?? 'user@example.com' }}</p>
                            </div>
                            <hr class="dropdown-divider">
                            <a href="#" class="dropdown-item">👤 Profile</a>
                            <a href="#" class="dropdown-item">⚙️ Settings</a>
                            <hr class="dropdown-divider">
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="dropdown-item dropdown-item-danger">🚪 Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            {{ $slot }}
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/dark-mode-toggle.js') }}"></script>
    @vite(['resources/js/app.js'])
</body>
</html>

<style>
    :root {
        --transition-speed: 0.3s;
    }

    * {
        transition: background-color var(--transition-speed) ease, color var(--transition-speed) ease, border-color var(--transition-speed) ease;
    }

    body {
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 0;
    }

    .wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* Header Styles */
    .header {
        background-color: var(--color-bg-secondary);
        border-bottom: 1px solid var(--color-border-light);
        position: sticky;
        top: 0;
        z-index: 1000;
        padding: 1rem 0;
    }

    .header-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .logo {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--color-primary);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: opacity 0.2s ease;
    }

    .logo:hover {
        opacity: 0.8;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .header-item {
        display: flex;
        align-items: center;
        position: relative;
    }

    /* Buttons */
    .notification-btn,
    .user-menu-btn {
        background: transparent;
        border: none;
        cursor: pointer;
        color: var(--color-text-primary);
        position: relative;
        padding: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background-color 0.2s ease;
    }

    .notification-btn:hover,
    .user-menu-btn:hover {
        background-color: var(--color-bg-tertiary);
    }

    .h-6 {
        width: 1.5rem;
        height: 1.5rem;
    }

    .badge {
        position: absolute;
        top: 0;
        right: 0;
        background-color: var(--color-danger);
        color: white;
        font-size: 0.75rem;
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }

    .user-avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        border: 2px solid var(--color-primary);
    }

    /* Dropdown Menu */
    .dropdown {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background-color: var(--color-card-bg);
        border: 1px solid var(--color-card-border);
        border-radius: 0.5rem;
        min-width: 200px;
        padding: 0.5rem 0;
        margin-top: 0.5rem;
        display: none;
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        z-index: 1001;
    }

    .dropdown.open .dropdown-menu {
        display: block;
    }

    .dropdown-header {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--color-border-light);
    }

    .dropdown-name {
        margin: 0;
        font-weight: 600;
        color: var(--color-text-primary);
    }

    .dropdown-email {
        margin: 0.25rem 0 0 0;
        font-size: 0.875rem;
        color: var(--color-text-tertiary);
    }

    .dropdown-divider {
        margin: 0.5rem 0;
        border: none;
        border-top: 1px solid var(--color-border-light);
    }

    .dropdown-item {
        display: block;
        width: 100%;
        padding: 0.75rem 1rem;
        color: var(--color-text-primary);
        text-decoration: none;
        background: transparent;
        border: none;
        cursor: pointer;
        text-align: left;
        transition: background-color 0.2s ease;
        font-family: inherit;
        font-size: 0.95rem;
    }

    .dropdown-item:hover {
        background-color: var(--color-bg-tertiary);
    }

    .dropdown-item-danger {
        color: var(--color-danger);
    }

    .dropdown-item-danger:hover {
        background-color: rgba(239, 68, 68, 0.1);
    }

    /* Main Content */
    .main-content {
        flex: 1;
        max-width: 1400px;
        width: 100%;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-container {
            padding: 0 0.75rem;
        }

        .header-right {
            gap: 1rem;
        }

        .logo span {
            display: none;
        }

        .main-content {
            padding: 1rem 0.75rem;
        }
    }
</style>
