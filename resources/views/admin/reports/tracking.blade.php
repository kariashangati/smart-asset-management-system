@extends('layouts.admin')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Reports</p>
        <h1>Tracking History Report</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <span>Tracking Report</span>
        </p>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Location logs</h2>
            <p>Filter by asset and date range.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.reports.tracking') }}" class="form-grid mb-4">
        <div class="form-group">
            <label>Asset</label>
            <select name="asset_id" class="form-control">
                <option value="">All assets</option>
                @foreach($assets as $asset)
                    <option value="{{ $asset->id }}" {{ request('asset_id') == $asset->id ? 'selected' : '' }}>{{ $asset->name }} ({{ $asset->asset_code }})</option>
                @endforeach
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
            <a href="{{ route('admin.reports.tracking') }}" class="btn btn-outline">Reset</a>
        </div>
    </form>

    <div class="table-wrap">
        <table class="app-table" data-datatable="true">
            <thead>
                <tr>
                    <th>Asset</th>
                    <th>Tracker Device</th>
                    <th>Timestamp</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Speed (km/h)</th>
                    <th>Motion</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->asset->name ?? '—' }}</td>
                    <td>{{ $log->trackerDevice->device_name ?? '—' }}</td>
                    <td>{{ $log->recorded_at->format('d M Y H:i:s') }}</td>
                    <td>{{ $log->latitude }}</td>
                    <td>{{ $log->longitude }}</td>
                    <td>{{ $log->speed ?? '—' }}</td>
                    <td>{{ $log->motion_detected ? 'Yes' : 'No' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection