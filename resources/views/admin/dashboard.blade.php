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
    </div>

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
                        <td>{{ $asset->asset_code }}</td>
                        <td>{{ $asset->name }}</td>
                        <td>{{ ucfirst($asset->status) }}</td>
                        <td>{{ $asset->created_at->format('d M Y') }}</td>
                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

</section>

<section class="content-card">

    <div class="section-header">
        <div>
            <h2>Recent Users</h2>
            <p>Latest system users</p>
        </div>
    </div>

    <div class="table-wrap">

        <table class="display app-table" data-datatable="true">

            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created</th>
                </tr>
            </thead>

            <tbody>

                @foreach($recentUsers as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                @endforeach

            </tbody>

        </table>

    </div>

</section>

@endsection