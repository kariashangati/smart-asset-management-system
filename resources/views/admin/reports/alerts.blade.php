@extends('layouts.admin')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Reports</p>
        <h1>Alert Report</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <span>Alert Report</span>
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.reports.alerts') }}?{{ http_build_query(request()->query()) }}&export=pdf" class="btn btn-primary" download>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span>Download PDF</span>
        </a>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Alert history</h2>
            <p>Filter by severity, status, and date range.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.reports.alerts') }}" class="form-grid mb-4">
        <div class="form-group">
            <label>Severity</label>
            <select name="severity" class="form-control">
                <option value="">All</option>
                <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('severity') == 'high' ? 'selected' : '' }}>High</option>
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="">All</option>
                <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
        </div>
        <div class="form-group">
            <label>Date from</label>
            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
        </div>
        <div class="form-group">
            <label>Date to</label>
            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
        </div>
        <div class="form-group" style="align-self: flex-end;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.reports.alerts') }}" class="btn btn-outline">Reset</a>
        </div>
    </form>

    @if($alerts->isEmpty())
        <x-empty-state 
            icon="alert"
            title="No Alerts Found"
            description="No alerts match your filters. Try adjusting the severity, status, or date range."
            action="{{ route('admin.dashboard') }}"
            actionText="Back to Dashboard"
        />
    @else
        <div class="table-wrap">
            <table class="app-table" data-datatable="true">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Asset</th>
                        <th>Severity</th>
                        <th>Status</th>
                        <th>Triggered at</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alerts as $alert)
                    <tr>
                        <td>
                            <a href="{{ route('admin.alerts.show', $alert) }}" class="link-primary">
                                {{ $alert->title }}
                            </a>
                        </td>
                        <td>{{ $alert->asset->name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $alert->severity == 'high' ? 'badge-danger' : ($alert->severity == 'medium' ? 'badge-warning' : 'badge-success') }}">
                                {{ ucfirst($alert->severity) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $alert->status === 'unread' ? 'badge-danger' : ($alert->status === 'read' ? 'badge-warning' : 'badge-success') }}">
                                {{ ucfirst($alert->status) }}
                            </span>
                        </td>
                        <td>{{ $alert->triggered_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.alerts.show', $alert) }}" class="btn btn-sm btn-outline">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<style>
    .page-actions {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }

    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .btn-outline {
        background-color: transparent;
        border: 1px solid #475569;
        color: #cbd5e1;
    }

    .btn-outline:hover {
        background-color: #334155;
    }

    .h-5 {
        width: 1.25rem;
        height: 1.25rem;
    }

    .link-primary {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
    }

    .link-primary:hover {
        text-decoration: underline;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
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
</style>
@endsection
