@extends('layouts.manager')

@section('page_title', 'Department Live Map')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Department Assets</p>
        <h1>Department Live Map</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('manager.dashboard') }}">Dashboard</a> / <span>Live Map</span>
        </p>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Asset Locations <span class="badge badge-soft">{{ count($assets) }} Assets</span></h2>
            <p>Real-time view of assets within your department.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('manager.tracking.live-map') }}" class="filter-form">
        <div class="filter-grid">
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="checkbox-row">
                <input type="checkbox" name="actual" id="actualFilter" value="1" {{ request('actual') ? 'checked' : '' }}>
                <label for="actualFilter">With Location Only</label>
            </div>
            <div class="button-row">
                <button type="submit" class="btn btn-primary">Filter Map</button>
                <a href="{{ route('manager.tracking.live-map') }}" class="btn btn-light">Reset</a>
            </div>
        </div>
    </form>

    <div id="map" class="live-map-container"></div>
</div>
@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.initMap = function() {
            try {
                const assets = @json($assets);
                const mapElement = document.getElementById('map');
                
                if (!mapElement) return;

                const map = new google.maps.Map(mapElement, {
                    zoom: 12,
                    center: { lat: -1.2921, lng: 36.8219 }, // Default: Nairobi
                    mapTypeId: 'roadmap',
                    mapTypeControl: true,
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                        position: google.maps.ControlPosition.TOP_RIGHT,
                    },
                });

                const bounds = new google.maps.LatLngBounds();
                const infoWindow = new google.maps.InfoWindow();

                assets.forEach(asset => {
                    if (asset.latest_location) {
                        const pos = {
                            lat: parseFloat(asset.latest_location.latitude),
                            lng: parseFloat(asset.latest_location.longitude)
                        };

                        const marker = new google.maps.Marker({
                            position: pos,
                            map: map,
                            title: asset.name,
                            icon: asset.status === 'active' 
                                ? 'http://maps.google.com/mapfiles/ms/icons/green-dot.png' 
                                : 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                        });

                        marker.addListener('click', () => {
                            const content = `
                                <div style="padding: 10px; min-width: 200px;">
                                    <h3 style="margin: 0 0 8px; font-size: 1.1rem; color: var(--text);">${asset.name}</h3>
                                    <div style="font-size: 0.9rem; line-height: 1.6;">
                                        <p style="margin: 0;"><strong>Category:</strong> ${asset.category ? asset.category.name : 'N/A'}</p>
                                        <p style="margin: 0;"><strong>Status:</strong> <span class="badge ${asset.status === 'active' ? 'badge-success' : 'badge-soft'}">${asset.status}</span></p>
                                        <hr style="margin: 8px 0; border: 0; border-top: 1px solid var(--border);">
                                        <p style="margin: 0; font-size: 0.8rem; color: var(--muted);">Last Update: ${asset.latest_location.captured_at || asset.latest_location.created_at}</p>
                                    </div>
                                </div>
                            `;
                            infoWindow.setContent(content);
                            infoWindow.open(map, marker);
                        });

                        bounds.extend(pos);
                    }
                });

                if (!bounds.isEmpty()) {
                    map.fitBounds(bounds);
                } else {
                    map.setCenter({ lat: -1.2921, lng: 36.8219 });
                    map.setZoom(12);
                }
            } catch (e) {
                console.error("Map initialization failed", e);
                mapElement.innerHTML = '<div style="padding: 2rem; text-align: center; color: var(--danger);">Failed to load map data.</div>';
            }
        };
    });
</script>
@endsection