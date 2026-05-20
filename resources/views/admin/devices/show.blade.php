@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Device Details: {{ $trackerDevice->device_name }}</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr><th width="200">Device Code</th><td>{{ $trackerDevice->device_code }}</td></tr>
            <tr><th>Device Name</th><td>{{ $trackerDevice->device_name }}</td></tr>
            <tr><th>IMEI</th><td>{{ $trackerDevice->imei ?? '—' }}</td></tr>
            <tr><th>SIM Number</th><td>{{ $trackerDevice->sim_number ?? '—' }}</td></tr>
            <tr><th>API Token Hash</th><td>{{ substr($trackerDevice->api_token_hash, 0, 20) }}...</td></tr>
            <tr><th>Status</th><td><span class="badge bg-{{ $trackerDevice->status == 'active' ? 'success' : ($trackerDevice->status == 'inactive' ? 'warning' : 'danger') }}">{{ ucfirst($trackerDevice->status) }}</span></td></tr>
            <tr><th>Last Seen</th><td>{{ $trackerDevice->last_seen_at ? $trackerDevice->last_seen_at->format('d M Y H:i:s') : 'Never' }}</td></tr>
            <tr><th>Battery Level</th><td>{{ $trackerDevice->battery_level ? $trackerDevice->battery_level . '%' : '—' }}</td></tr>
            <tr><th>Firmware Version</th><td>{{ $trackerDevice->firmware_version ?? '—' }}</td></tr>
            <tr><th>Created At</th><td>{{ $trackerDevice->created_at->format('d M Y H:i:s') }}</td></tr>
            <tr><th>Updated At</th><td>{{ $trackerDevice->updated_at->format('d M Y H:i:s') }}</td></tr>
        </table>
        <hr>
        <h5>Current Assignment</h5>
        @if($trackerDevice->activeAssignment && $trackerDevice->activeAssignment->asset)
            <p>Asset: <a href="{{ route('admin.assets.show', $trackerDevice->activeAssignment->asset) }}">{{ $trackerDevice->activeAssignment->asset->name }}</a></p>
            <p>Assigned at: {{ $trackerDevice->activeAssignment->assigned_at->format('d M Y H:i') }}</p>
        @else
            <p>No current assignment.</p>
        @endif
    </div>
</div>
@endsection