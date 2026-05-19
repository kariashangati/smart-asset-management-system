@extends('layouts.admin')

@section('title', 'User Details')
@section('portal_label', 'Admin Portal')
@section('page_title', 'User Details')
@section('dashboard_url', route('admin.dashboard'))

@section('content')
    <section class="content-card">
        <div class="section-header">
            <div>
                <h2>{{ $user->name }}</h2>
                <p>View account details, assigned roles, and direct permissions.</p>
            </div>

            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                Back to Users
            </a>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <span>Email Address</span>
                <strong>{{ $user->email }}</strong>
            </div>

            <div class="detail-item">
                <span>Phone</span>
                <strong>{{ $user->phone ?: '—' }}</strong>
            </div>

            <div class="detail-item">
                <span>Status</span>
                <strong>
                    @if ($user->isActive())
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-warning">Inactive</span>
                    @endif
                </strong>
            </div>

            <div class="detail-item">
                <span>Created On</span>
                <strong>{{ $user->created_at->format('d M Y, H:i') }}</strong>
            </div>
        </div>
    </section>

    <section class="content-card">
        <div class="section-header">
            <div>
                <h2>Assigned Roles</h2>
                <p>Roles control the user's access through Spatie permissions.</p>
            </div>
        </div>

        <div class="tag-list">
            @forelse ($user->getRoleNames() as $role)
                <span class="badge badge-soft">{{ $role }}</span>
            @empty
                <span class="badge badge-warning">No roles assigned</span>
            @endforelse
        </div>
    </section>

    <section class="content-card">
        <div class="section-header">
            <div>
                <h2>Direct Permissions</h2>
                <p>Direct user permissions are shown here if assigned in the future.</p>
            </div>
        </div>

        <div class="tag-list">
            @forelse ($user->getDirectPermissions() as $permission)
                <span class="badge badge-soft">{{ $permission->name }}</span>
            @empty
                <span class="badge badge-warning">No direct permissions assigned</span>
            @endforelse
        </div>
    </section>
@endsection