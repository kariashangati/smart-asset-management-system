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
                class="nav-link {{ request()->routeIs('admin.assets.*') ? 'active' : '' }}"
            >
                Assets
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