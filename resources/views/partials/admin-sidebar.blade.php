<aside class="sidebar">
    <div class="sidebar-header">
        <strong>Admin Portal</strong>
        <small>System administration</small>
    </div>

    <nav class="sidebar-nav">
        <a
            href="{{ route('admin.dashboard') }}"
            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
        >
            Dashboard
        </a>

        @can('users.view')
            <a
                href="{{ route('admin.users.index') }}"
                class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
            >
                Users
            </a>
        @endcan

        @can('roles.view')
            <a
                href="{{ route('admin.roles.index') }}"
                class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
            >
                Roles
            </a>
        @endcan

        @can('departments.view')
            <a
                href="{{ route('admin.departments.index') }}"
                class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}"
            >
                Departments
            </a>
        @endcan

        @can('asset_categories.view')
            <a
                href="{{ route('admin.asset-categories.index') }}"
                class="nav-link {{ request()->routeIs('admin.asset-categories.*') ? 'active' : '' }}"
            >
                Asset Categories
            </a>
        @endcan

        @can('assets.view')
            <a
                href="{{ route('admin.assets.index') }}"
                class="nav-link {{ request()->routeIs('admin.assets.index') ? 'active' : '' }}"
            >
                Assets
            </a>
        @endcan

        @can('assets.view')
            <a
                href="#"
                onclick="event.preventDefault()"
                class="nav-link {{ request()->routeIs('admin.assets.rules.*') ? 'active' : '' }}"
                style="padding-left: 2rem; font-size: 0.9rem;"
                id="alertRulesNavLink"
            >
                ↳ Alert Rules
            </a>
        @endcan

        @can('devices.view')
            <a
                href="{{ route('admin.devices.index') }}"
                class="nav-link {{ request()->routeIs('admin.devices.*') ? 'active' : '' }}"
            >
                Tracker Devices
            </a>
        @endcan

        @can('assignments.view')
            <a
                href="{{ route('admin.assignments.index') }}"
                class="nav-link {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}"
            >
                Device Assignments
            </a>
        @endcan

        @can('geofences.view')
            <a
                href="{{ route('admin.geofences.index') }}"
                class="nav-link {{ request()->routeIs('admin.geofences.*') ? 'active' : '' }}"
            >
                Geofences
            </a>
        @endcan

        {{-- MONITORING GROUP --}}
        @canany(['tracking.live_map.view', 'tracking.history.view'])
            <div class="sidebar-group">
                <span>Monitoring</span>
                <small>Real-time and logs</small>
            </div>

            @can('tracking.live_map.view')
                <a
                    href="{{ route('admin.tracking.live-map') }}"
                    class="nav-link {{ request()->routeIs('admin.tracking.live-map') ? 'active' : '' }}"
                >
                    Live Map
                </a>
            @endcan

            @can('tracking.history.view')
                <a
                    href="{{ route('admin.tracking.history') }}"
                    class="nav-link {{ request()->routeIs('admin.tracking.history') ? 'active' : '' }}"
                >
                    Location History
                </a>
            @endcan
        @endcanany

        {{-- REPORTS GROUP --}}
        @can('reports.view')
            <div class="sidebar-group">
                <span>Reports</span>
                <small>Asset, tracking, and alert reports</small>
            </div>
            <a
                href="{{ route('admin.reports.assets') }}"
                class="nav-link {{ request()->routeIs('admin.reports.assets') ? 'active' : '' }}"
            >
                Asset Report
            </a>
            <a
                href="{{ route('admin.reports.tracking') }}"
                class="nav-link {{ request()->routeIs('admin.reports.tracking') ? 'active' : '' }}"
            >
                Tracking Report
            </a>
            <a
                href="{{ route('admin.reports.alerts') }}"
                class="nav-link {{ request()->routeIs('admin.reports.alerts') ? 'active' : '' }}"
            >
                Alert Report
            </a>
        @endcan

        {{-- AUDIT LOGS --}}
        @can('audit_logs.view')
            <a
                href="{{ route('admin.audit-logs.index') }}"
                class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}"
            >
                Audit Logs
            </a>
        @endcan
    </nav>
</aside>
