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

        <div class="sidebar-group">
            <span>Assets</span>
            <small>Asset records come in Phase 6</small>
        </div>

        <div class="sidebar-group">
            <span>Tracking Devices</span>
            <small>Devices and assignments</small>
        </div>

        <div class="sidebar-group">
            <span>Monitoring</span>
            <small>Map, geofences, and alerts</small>
        </div>

        <div class="sidebar-group">
            <span>Reports</span>
            <small>Asset, tracking, and alert reports</small>
        </div>
    </nav>
</aside>