@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Map -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📍 {{ $asset->name }} - Live Location</h5>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 500px;"></div>
                </div>
            </div>
        </div>

        <!-- Asset Details -->
        <div class="col-lg-4">
            <!-- Asset Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Asset Information</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Name:</dt>
                        <dd class="col-sm-6">{{ $asset->name }}</dd>

                        <dt class="col-sm-6">Type:</dt>
                        <dd class="col-sm-6">{{ ucfirst($asset->asset_type) }}</dd>

                        <dt class="col-sm-6">Serial:</dt>
                        <dd class="col-sm-6">{{ $asset->serial_number }}</dd>

                        <dt class="col-sm-6">Status:</dt>
                        <dd class="col-sm-6">
                            <span class="badge" style="background-color: {{ $assetData['color']; }}">
                                {{ ucfirst($asset->status) }}
                            </span>
                        </dd>

                        <dt class="col-sm-6">Department:</dt>
                        <dd class="col-sm-6">{{ $asset->department->name ?? 'N/A' }}</dd>
                    </dl>
                </div>
            </div>

            <!-- Current Location -->
            @if($assetData['location'])
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Current Location</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Latitude:</dt>
                        <dd class="col-sm-6">{{ number_format($assetData['location']['latitude'], 6) }}</dd>

                        <dt class="col-sm-6">Longitude:</dt>
                        <dd class="col-sm-6">{{ number_format($assetData['location']['longitude'], 6) }}</dd>

                        <dt class="col-sm-6">Speed:</dt>
                        <dd class="col-sm-6">{{ number_format($assetData['location']['speed'], 2) }} km/h</dd>

                        <dt class="col-sm-6">Motion:</dt>
                        <dd class="col-sm-6">
                            @if($assetData['location']['motion_detected'])
                                <span class="badge bg-success">Moving</span>
                            @else
                                <span class="badge bg-secondary">Stationary</span>
                            @endif
                        </dd>

                        <dt class="col-sm-6">Updated:</dt>
                        <dd class="col-sm-6">{{ $assetData['location']['last_recorded_at'] }}</dd>
                    </dl>
                </div>
            </div>
            @endif

            <!-- Location History Trail -->
            @if($trail['trail_points'])
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Location History (Last {{ count($trail['trail_points']) }} points)</h6>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @foreach($trail['trail_points'] as $index => $point)
                    <div class="mb-2 pb-2 border-bottom">
                        <small>
                            <strong>#{{ count($trail['trail_points']) - $index }}</strong>
                            {{ number_format($point['latitude'], 6) }}, 
                            {{ number_format($point['longitude'], 6) }}
                        </small>
                        <div class="text-muted small">
                            {{ $point['timestamp'] }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ $maps_api_key }}&callback=initMap" async defer></script>

<script>
let map;
let marker;
let polyline;

function initMap() {
    const assetData = @json($assetData);
    const trail = @json($trail);

    if (!assetData.location) {
        console.error('No location data available');
        return;
    }

    const center = {
        lat: assetData.location.latitude,
        lng: assetData.location.longitude
    };

    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: center,
        mapTypeId: 'roadmap',
    });

    // Add current location marker
    marker = new google.maps.Marker({
        position: center,
        map: map,
        title: assetData.name,
        icon: 'http://maps.google.com/mapfiles/ms/micons/blue.png',
    });

    // Draw location trail
    if (trail.trail_points && trail.trail_points.length > 0) {
        const pathCoordinates = trail.trail_points.map(point => ({
            lat: point.latitude,
            lng: point.longitude
        }));

        polyline = new google.maps.Polyline({
            path: pathCoordinates,
            geodesic: true,
            strokeColor: '#3b82f6',
            strokeOpacity: 0.7,
            strokeWeight: 2,
            map: map,
        });
    }

    // Auto-refresh every 30 seconds
    setInterval(() => {
        fetch(`/api/map/assets/{{ $asset->id }}`, {
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.data.location) {
                marker.setPosition({
                    lat: data.data.location.latitude,
                    lng: data.data.location.longitude
                });
            }
        });
    }, 30000);
}
</script>
@endsection
