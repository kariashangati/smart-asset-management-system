<header class="topbar">
    <div class="topbar-brand">
        <span class="brand-mark">SAMS</span>
        <div>
            <strong>Smart Asset Management System</strong>
            <small>Institutional GPS asset tracking portal</small>
        </div>
    </div>

    <div class="topbar-user">
        @php
            $alertsRoute = auth()->user()->hasRole('admin') ? 'admin.alerts.index' : 'manager.alerts.index';
            $unreadCount = app(\App\Services\AlertService::class)->getUnreadCount();
        @endphp

        <a href="{{ route($alertsRoute) }}" class="alert-bell" style="position: relative; margin-right: 20px; display: inline-flex; align-items: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="stroke: var(--text);">
                <path d="M18 8C18 6.4087 17.3679 4.88258 16.2426 3.75736C15.1174 2.63214 13.5913 2 12 2C10.4087 2 8.88258 2.63214 7.75736 3.75736C6.63214 4.88258 6 6.4087 6 8C6 15 3 17 3 17H21C21 17 18 15 18 8Z"/>
                <path d="M13.73 21C13.5542 21.3031 13.3019 21.5547 12.9982 21.7295C12.6946 21.9044 12.3504 21.9965 12 21.9965C11.6496 21.9965 11.3054 21.9044 11.0018 21.7295C10.6982 21.5547 10.4458 21.3031 10.27 21"/>
            </svg>
            @if($unreadCount > 0)
                <span class="badge badge-danger" style="position: absolute; top: -8px; right: -10px; font-size: 10px; padding: 2px 6px;">{{ $unreadCount }}</span>
            @endif
        </a>

        <div class="user-copy">
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->email }}</small>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline">Logout</button>
        </form>
    </div>
</header>