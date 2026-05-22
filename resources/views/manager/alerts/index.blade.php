@extends('layouts.manager')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Notifications</p>
        <h1>Alerts</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('manager.dashboard') }}">Dashboard</a> / <span>Alerts</span>
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
                    <td>{{ $alert->title }}</td>
                    <td>{{ $alert->asset->name ?? '—' }}</td>
                    <td>{{ $alert->triggered_at->format('d M Y H:i') }}</td>
                    <td>
                        <span class="badge {{ $alert->status === 'unread' ? 'badge-danger' : ($alert->status === 'read' ? 'badge-warning' : 'badge-success') }}">
                            {{ ucfirst($alert->status) }}
                        </span>
                    </td>
                    <td class="inline-actions">
                        <a href="{{ route('manager.alerts.show', $alert) }}" class="btn btn-outline btn-sm">View</a>
                        @if($alert->status === 'unread')
                            <form method="POST" action="{{ route('manager.alerts.mark-read', $alert) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-outline btn-sm">Mark as read</button>
                            </form>
                        @endif
                        @if($alert->status !== 'resolved')
                            <form method="POST" action="{{ route('manager.alerts.mark-resolved', $alert) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-outline btn-sm">Resolve</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('manager.alerts.destroy', $alert) }}" class="js-confirm-delete" data-title="Delete alert" data-text="This alert will be permanently removed." style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection