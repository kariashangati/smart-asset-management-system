<aside class="sidebar">
    <div class="sidebar-header">
        <strong>Asset Manager Portal</strong>
        <small>Operational monitoring</small>
    </div>

    <nav class="sidebar-nav">
        <a
            href="{{ route('manager.dashboard') }}"
            class="nav-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}"
        >
            Dashboard
        </a>

        {{-- Tracking Group (uses existing routes from Phase 8) --}}
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

        {{-- Placeholder groups for future modules (no links) --}}
        <div class="sidebar-group">
            <span>Assets</span>
            <small>Asset list and registration</small>
        </div>

        <div class="sidebar-group">
            <span>Geofences</span>
            <small>Allowed asset zones</small>
        </div>

        <div class="sidebar-group">
            <span>Alerts</span>
            <small>Movement and perimeter alerts</small>
        </div>
    </nav>
</aside>