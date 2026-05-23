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

    <div class="table-wrap">
        <table class="app-table" data-datatable="true">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Asset</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Triggered at</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alerts as $alert)
                <tr>
                    <td>{{ $alert->title }}</td>
                    <td>{{ $alert->asset->name ?? '—' }}</td>
                    <td><span class="badge {{ $alert->severity == 'high' ? 'badge-danger' : ($alert->severity == 'medium' ? 'badge-warning' : 'badge-success') }}">{{ ucfirst($alert->severity) }}</span></td>
                    <td><span class="badge {{ $alert->status == 'unread' ? 'badge-danger' : ($alert->status == 'read' ? 'badge-warning' : 'badge-success') }}">{{ ucfirst($alert->status) }}</span></td>
                    <td>{{ $alert->triggered_at->format('d M Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $alerts->links() }}
    </div>
</div>
@endsection