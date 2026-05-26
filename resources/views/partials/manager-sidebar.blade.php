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

        {{-- Assets --}}
        @can('assets.view')
            <div class="sidebar-group">
                <span>Assets</span>
                <small>Asset list and registration</small>
            </div>

            <a
                href="{{ route('manager.assets.index') }}"
                class="nav-link {{ request()->routeIs('manager.assets.*') ? 'active' : '' }}"
            >
                My Department Assets
            </a>
        @endcan

        {{-- Geofences (actual route) --}}
        @can('geofences.view')
            <div class="sidebar-group">
                <span>Zones</span>
                <small>Geographical boundaries</small>
            </div>
            <a
                href="{{ route('manager.geofences.index') }}"
                class="nav-link {{ request()->routeIs('manager.geofences.*') ? 'active' : '' }}"
            >
                My Geofences
            </a>
        @endcan

        {{-- Alerts (actual route) --}}
        @can('alerts.view')
            <div class="sidebar-group">
                <span>Alerts</span>
                <small>Issue monitoring</small>
            </div>
            <a
                href="{{ route('manager.alerts.index') }}"
                class="nav-link {{ request()->routeIs('manager.alerts.*') ? 'active' : '' }}"
            >
                Alerts
            </a>
        @endcan

        {{-- Reports --}}
        @can('reports.view')
            <div class="sidebar-group">
                <span>Reports</span>
                <small>Operational insights</small>
            </div>
            <a
                href="{{ route('manager.reports.alerts') }}"
                class="nav-link {{ request()->routeIs('manager.reports.alerts') ? 'active' : '' }}"
            >
                Department Alerts
            </a>
        @endcan
    </nav>
</aside>