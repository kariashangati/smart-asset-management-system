@extends('layouts.admin')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Notifications</p>
        <h1>Alerts</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <span>Alerts</span>
        </p>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Alert history</h2>
            <p>System-generated alerts from geofence violations, device status, and motion.</p>
        </div>
    </div>

    @if($alerts->isEmpty())
        <x-empty-state 
            icon="alert"
            title="No Alerts"
            description="There are no alerts in the system. All systems are operating normally."
            action="{{ route('admin.dashboard') }}"
            actionText="Back to Dashboard"
        />
    @else
        <div class="table-wrap">
            <table class="app-table" data-datatable="true">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Severity</th>
                        <th>Title</th>
                        <th>Asset</th>
                        <th>Triggered at</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alerts as $alert)
                    <tr>
                        <td><span class="badge badge-soft">{{ str_replace('_', ' ', ucfirst($alert->alert_type)) }}</span></td>
                        <td>
                            <span class="badge {{ $alert->severity === 'high' ? 'badge-danger' : ($alert->severity === 'medium' ? 'badge-warning' : 'badge-success') }}">
                                {{ ucfirst($alert->severity) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.alerts.show', $alert) }}" class="link-primary">
                                {{ $alert->title }}
                            </a>
                        </td>
                        <td>{{ $alert->asset->name ?? '—' }}</td>
                        <td>{{ $alert->triggered_at->format('d M Y H:i') }}</td>
                        <td>
                            <span class="badge {{ $alert->status === 'unread' ? 'badge-danger' : ($alert->status === 'read' ? 'badge-warning' : 'badge-success') }}">
                                {{ ucfirst($alert->status) }}
                            </span>
                        </td>
                        <td class="inline-actions">
                            <a href="{{ route('admin.alerts.show', $alert) }}" class="btn btn-outline btn-sm">View</a>
                            @if($alert->status === 'unread')
                                <form method="POST" action="{{ route('admin.alerts.mark-read', $alert) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline btn-sm">Mark as read</button>
                                </form>
                            @endif
                            @if($alert->status !== 'resolved')
                                <form method="POST" action="{{ route('admin.alerts.mark-resolved', $alert) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline btn-sm">Resolve</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.alerts.destroy', $alert) }}" class="js-confirm-delete" data-title="Delete alert" data-text="This alert will be permanently removed." style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<style>
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

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.75rem;
        border-radius: 0.375rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .btn-sm {
        padding: 0.25rem 0.625rem;
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

    .btn-danger {
        background-color: rgba(239, 68, 68, 0.1);
        color: #f87171;
        border: 1px solid #7f1d1d;
    }

    .btn-danger:hover {
        background-color: rgba(239, 68, 68, 0.2);
    }

    .inline-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
</style>
@endsection
