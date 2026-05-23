@extends('layouts.admin')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">System</p>
        <h1>Audit Logs</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <span>Audit Logs</span>
        </p>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>User activity history</h2>
            <p>Every create, update, and delete action is recorded.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="form-grid mb-4">
        <div class="form-group">
            <label>Module</label>
            <select name="module" class="form-control">
                <option value="">All</option>
                @foreach($modules as $module)
                    <option value="{{ $module }}" {{ $filters['module'] == $module ? 'selected' : '' }}>{{ $module }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Action</label>
            <select name="action" class="form-control">
                <option value="">All</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ $filters['action'] == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>User</label>
            <input type="text" name="user_id" placeholder="User ID" class="form-control" value="{{ $filters['user_id'] }}">
        </div>
        <div class="form-group">
            <label>Date from</label>
            <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] }}">
        </div>
        <div class="form-group">
            <label>Date to</label>
            <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] }}">
        </div>
        <div class="form-group" style="align-self: flex-end;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-outline">Reset</a>
        </div>
    </form>

    <div class="table-wrap">
        <table class="app-table" data-datatable="true">
            <thead>
                <tr><th>User</th><th>Action</th><th>Module</th><th>Description</th><th>IP Address</th><th>Timestamp</th></tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td><span class="badge badge-soft">{{ ucfirst($log->action) }}</span></td>
                    <td>{{ $log->module }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->ip_address ?? '—' }}</td>
                    <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection