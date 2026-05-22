<aside class="sidebar">
    <div class="sidebar-header">
        <strong>Asset Manager Portal</strong>
        <small>Operational monitoring</small>
    </div>

    <nav class="sidebar-nav">
        {{-- Dashboard --}}
        <a
            href="{{ route('manager.dashboard') }}"
            class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}"
        >
            Dashboard
        </a>

        {{-- Tracking --}}
        @canany(['tracking.live_map.view', 'tracking.history.view'])
            <div class="sidebar-group">
                <span>Tracking</span>
                <small>Live map and location history</small>
            </div>

            @can('tracking.live_map.view')
                <a
                    href="{{ route('manager.tracking.live-map') }}"
                    class="nav-link {{ request()->routeIs('manager.tracking.live-map') ? 'active' : '' }}"
                >
                    Live Map
                </a>
            @endcan

            @can('tracking.history.view')
                <a
                    href="{{ route('manager.tracking.history') }}"
                    class="nav-link {{ request()->routeIs('manager.tracking.history') ? 'active' : '' }}"
                >
                    Location History
                </a>
            @endcan
        @endcanany

        {{-- Assets (Placeholder – no manager asset routes yet) --}}
        <div class="sidebar-group">
            <span>Assets</span>
            <small>Asset list and registration</small>
        </div>

        {{-- Geofences (actual route) --}}
        @can('geofences.view')
            <a
                href="{{ route('manager.geofences.index') }}"
                class="nav-link {{ request()->routeIs('manager.geofences.*') ? 'active' : '' }}"
            >
                Geofences
            </a>
        @endcan

        {{-- Alerts (actual route) --}}
        @can('alerts.view')
            <a
                href="{{ route('manager.alerts.index') }}"
                class="nav-link {{ request()->routeIs('manager.alerts.*') ? 'active' : '' }}"
            >
                Alerts
            </a>
        @endcan
    </nav>
</aside>