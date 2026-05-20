@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Asset Details: {{ $asset->name }}</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 text-center">
                @if($asset->image)
                    <img src="{{ Storage::url($asset->image) }}" class="img-fluid rounded" style="max-height: 300px;">
                @else
                    <p>No image available</p>
                @endif
            </div>
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr><th width="200">Asset Code</th><td>{{ $asset->asset_code }}</td></tr>
                    <tr><th>Name</th><td>{{ $asset->name }}</td></tr>
                    <tr><th>Serial Number</th><td>{{ $asset->serial_number ?? '—' }}</td></tr>
                    <tr><th>Category</th><td>{{ $asset->category->name ?? '—' }}</td></tr>
                    <tr><th>Department</th><td>{{ $asset->department->name ?? '—' }}</td></tr>
                    <tr><th>Purchase Date</th><td>{{ $asset->purchase_date ? $asset->purchase_date->format('d M Y') : '—' }}</td></tr>
                    <tr><th>Status</th>
                        <td>
                            <span class="badge bg-{{ $asset->status == 'active' ? 'success' : ($asset->status == 'inactive' ? 'warning' : 'danger') }}">
                                {{ ucfirst($asset->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr><th>Description</th><td>{{ $asset->description ?? '—' }}</td></tr>
                </table>

                <hr>
                <h5>Active Device Assignment</h5>
                @if($asset->activeAssignment && $asset->activeAssignment->trackerDevice)
                    <p>Device: {{ $asset->activeAssignment->trackerDevice->device_name }} ({{ $asset->activeAssignment->trackerDevice->device_code }})</p>
                    <p>Assigned at: {{ $asset->activeAssignment->assigned_at->format('d M Y H:i') }}</p>
                @else
                    <p>No device currently assigned.</p>
                @endif

                <hr>
                <h5>Active Geofence</h5>
                @php $activeGeofence = $asset->geofences->where('status', 'active')->first(); @endphp
                @if($activeGeofence)
                    <p>Perimeter: {{ $activeGeofence->name }}</p>
                    <p>Center: {{ $activeGeofence->center_latitude }}, {{ $activeGeofence->center_longitude }}</p>
                    <p>Radius: {{ $activeGeofence->radius_meters }} meters</p>
                @else
                    <p>No active perimeter defined.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection