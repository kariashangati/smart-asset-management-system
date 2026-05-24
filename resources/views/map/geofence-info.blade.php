@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">🚫 {{ $geofence->name }} - Breach Violations</h5>
                </div>
                <div class="card-body">
                    <!-- Geofence Details -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <strong>Center Latitude:</strong>
                            {{ $geofence->center_latitude }}
                        </div>
                        <div class="col-md-3">
                            <strong>Center Longitude:</strong>
                            {{ $geofence->center_longitude }}
                        </div>
                        <div class="col-md-3">
                            <strong>Radius:</strong>
                            {{ $geofence->radius_meters }} m
                        </div>
                        <div class="col-md-3">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $geofence->status === 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($geofence->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Violations Table -->
                    <h6 class="mt-4 mb-3">Violations ({{ count($violations['violations']) }})</h6>
                    @if(count($violations['violations']) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Asset Name</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Distance from Center</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($violations['violations'] as $violation)
                                    <tr>
                                        <td>{{ $violation['asset_name'] }}</td>
                                        <td>{{ number_format($violation['latitude'], 6) }}</td>
                                        <td>{{ number_format($violation['longitude'], 6) }}</td>
                                        <td>{{ number_format($violation['distance_from_center'] / 1000, 2) }} km</td>
                                        <td>
                                            <a href="/map/asset/{{ $violation['asset_id'] }}" class="btn btn-sm btn-info">
                                                View on Map
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success" role="alert">
                            ✅ All assets are within geofence boundaries!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
