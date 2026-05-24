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
    <div class="page-actions">
        <a href="{{ route('admin.reports.tracking') }}?{{ http_build_query(request()->query()) }}&export=pdf" class="btn btn-primary" download>
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

    @if($logs->isEmpty())
        <x-empty-state 
            icon="location"
            title="No Tracking Data Found"
            description="No location logs match your filters. Try adjusting the date range or selecting a different asset."
            action="{{ route('admin.dashboard') }}"
            actionText="Back to Dashboard"
        />
    @else
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
                        <td>
                            <a href="{{ route('admin.assets.show', $log->asset) }}" class="link-primary">
                                {{ $log->asset->name ?? '—' }}
                            </a>
                        </td>
                        <td>{{ $log->trackerDevice->device_name ?? '—' }}</td>
                        <td>{{ $log->recorded_at->format('d M Y H:i:s') }}</td>
                        <td><code>{{ $log->latitude }}</code></td>
                        <td><code>{{ $log->longitude }}</code></td>
                        <td>{{ $log->speed ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $log->motion_detected ? 'badge-success' : 'badge-soft' }}">
                                {{ $log->motion_detected ? 'Yes' : 'No' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $logs->links() }}
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

    code {
        background-color: rgba(15, 23, 42, 0.5);
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 0.875rem;
        color: #cbd5e1;
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

    .badge-soft {
        background-color: rgba(100, 116, 139, 0.1);
        color: #cbd5e1;
    }
</style>
@endsection
