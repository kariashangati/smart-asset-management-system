@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('portal_label', 'Admin Portal')
@section('page_title', 'System Dashboard')
@section('dashboard_url', route('admin.dashboard'))

@section('content')

<div class="stat-grid">

    <article class="stat-card">
        <span>Total Assets</span>
        <strong>{{ $totalAssets }}</strong>
        <small>Registered institutional assets</small>
    </article>

    <article class="stat-card">
        <span>Total Devices</span>
        <strong>{{ $totalDevices }}</strong>
        <small>Tracker devices in system</small>
    </article>

    <article class="stat-card">
        <span>Total Users</span>
        <strong>{{ $totalUsers }}</strong>
        <small>System users</small>
    </article>

    <article class="stat-card">
        <span>Unread Alerts</span>
        <strong>{{ $activeAlerts }}</strong>
        <small>Pending system alerts</small>
    </article>

</div>

<div class="stat-grid">

    <article class="stat-card">
        <span>Departments</span>
        <strong>{{ $totalDepartments }}</strong>
        <small>Registered departments</small>
    </article>

    <article class="stat-card">
        <span>Asset Categories</span>
        <strong>{{ $totalCategories }}</strong>
        <small>Available categories</small>
    </article>

    <article class="stat-card">
        <span>Active Assets</span>
        <strong>{{ $activeAssets }}</strong>
        <small>Operational assets</small>
    </article>

    <article class="stat-card">
        <span>Maintenance</span>
        <strong>{{ $maintenanceAssets }}</strong>
        <small>Assets under maintenance</small>
    </article>

</div>

<section class="content-card">

    <div class="section-header">
        <div>
            <h2>Quick Actions</h2>
            <p>Administrative shortcuts</p>
        </div>
    </div>

    <div class="button-row">

        <a href="{{ route('admin.assets.index') }}" class="btn btn-primary">
            Manage Assets
        </a>

        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
            Manage Users
        </a>

        <a href="{{ route('admin.departments.index') }}" class="btn btn-primary">
            Departments
        </a>

        <a href="{{ route('admin.asset-categories.index') }}" class="btn btn-primary">
            Categories
        </a>

    </div>

</section>

<section class="content-card">

    <div class="section-header">
        <div>
            <h2>Recent Assets</h2>
            <p>Latest registered assets</p>
        </div>
        <a href="{{ route('admin.assets.index') }}" class="btn btn-outline btn-sm">View All</a>
    </div>

    @if($recentAssets->isEmpty())
        <x-empty-state 
            icon="inbox"
            title="No Recent Assets"
            description="Start by creating your first asset."
            action="{{ route('admin.assets.create') }}"
            actionText="Create Asset"
        />
    @else
        <div class="table-wrap">
            <table class="display app-table" data-datatable="true">
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAssets as $asset)
                        <tr>
                            <td><span class="badge badge-soft">{{ $asset->asset_code }}</span></td>
                            <td>
                                <a href="{{ route('admin.assets.show', $asset) }}" class="link-primary">{{ $asset->name }}</a>
                            </td>
                            <td>
                                <span class="badge {{ $asset->status === 'active' ? 'badge-success' : ($asset->status === 'maintenance' ? 'badge-warning' : 'badge-danger') }}">
                                    {{ ucfirst($asset->status) }}
                                </span>
                            </td>
                            <td>{{ $asset->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</section>

<section class="content-card">

    <div class="section-header">
        <div>
            <h2>Recent Users</h2>
            <p>Latest system users</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">View All</a>
    </div>

    @if($recentUsers->isEmpty())
        <x-empty-state 
            icon="inbox"
            title="No Recent Users"
            description="No users have been added to the system yet."
            action="{{ route('admin.users.index') }}"
            actionText="Manage Users"
        />
    @else
        <div class="table-wrap">
            <table class="display app-table" data-datatable="true">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>
                                <code>{{ $user->email }}</code>
                            </td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge badge-soft">{{ ucfirst($role->name) }}</span>
                                @endforeach
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</section>

<style>
    .stat-card {
        background-color: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(71, 85, 105, 0.3);
        border-radius: 0.5rem;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .stat-card span {
        color: #94a3b8;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .stat-card strong {
        color: #e2e8f0;
        font-size: 2rem;
        font-weight: 700;
    }

    .stat-card small {
        color: #64748b;
        font-size: 0.75rem;
    }

    .button-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }

    .btn-outline {
        background-color: transparent;
        border: 1px solid #475569;
        color: #cbd5e1;
    }

    .btn-outline:hover {
        background-color: #334155;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .badge-soft {
        background-color: rgba(100, 116, 139, 0.1);
        color: #cbd5e1;
    }

    .badge-success {
        background-color: rgba(34, 197, 94, 0.1);
        color: #86efac;
    }

    .badge-warning {
        background-color: rgba(251, 146, 60, 0.1);
        color: #fb923c;
    }

    .badge-danger {
        background-color: rgba(239, 68, 68, 0.1);
        color: #f87171;
    }

    .link-primary {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
    }

    .link-primary:hover {
        text-decoration: underline;
    }

    code {
        background-color: rgba(15, 23, 42, 0.5);
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 0.875rem;
        color: #cbd5e1;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
</style>

@endsection
