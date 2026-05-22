@extends('layouts.manager')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Movement log</p>
        <h1>Asset History: {{ $asset->name }}</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('manager.dashboard') }}">Dashboard</a> /
            <a href="{{ route('manager.tracking.history') }}">History</a> /
            <span>{{ $asset->asset_code }}</span>
        </p>
    </div>
    <div class="button-row">
        <a href="{{ route('manager.tracking.live-map') }}" class="btn btn-outline">Back to Map</a>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Location timeline</h2>
            <p>All recorded positions for this asset.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="app-table" data-datatable="true">
            <thead>
                <tr><th>Timestamp</th><th>Latitude</th><th>Longitude</th><th>Speed (km/h)</th><th>Motion</th></tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
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
</div>
@endsection