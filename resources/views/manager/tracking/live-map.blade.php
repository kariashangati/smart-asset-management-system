@extends('layouts.manager')

@section('page_title', 'Department Live Map')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Department Tracking</p>
        <h1>Live Asset Map</h1>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Filter Department Assets</h2>
            <p>Filter your department's assets by category or status.</p>
        </div>
    </div>
    
    <form method="GET" action="{{ route('manager.tracking.live-map') }}" class="filter-form">
        <div class="filter-grid">
            <div class="form-group">
                <label class="form-label">Category</label>
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
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">&nbsp;</label>
                <div class="checkbox-row" style="margin-top: 6px;">
                    <input type="checkbox" id="actualFilter" name="actual" value="1" {{ request('actual') ? 'checked' : '' }}>
                    <label for="actualFilter" style="margin: 0; font-weight: 700;">With Location</label>
                </div>
            </div>
        </div>
        
        <div class="button-row" style="margin-top: 18px;">
            <button type="submit" class="btn btn-primary">Filter Map</button>
            <a href="{{ route('manager.tracking.live-map') }}" class="btn btn-outline">Reset</a>
        </div>
    </form>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Asset Locations</h2>
            <p>Real-time positions of your department's assets. Click markers for details.</p>
        </div>
        <div>
            <small style="color: var(--muted);">Total Assets: <strong>{{ $assets->count() }}</strong></small>
        </div>
    </div>
    
    <div id="map" class="live-map-container"></div>
</div>

@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
    });

    function initMap() {
        const assets = @json($assets);
        const mapElement = document.getElementById('map');
        
        if (!mapElement) return;

        const map = new google.maps.Map(mapElement, {
            zoom: 12,
            center: { lat: -1.2921, lng: 36.8219 }, // Default center (Nairobi)
            mapTypeId: 'roadmap',
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.TOP_RIGHT,
            },
            fullscreenControl: true,
            zoomControl: true,
        });

        const bounds = new google.maps.LatLngBounds();
        const infoWindows = [];
        let hasLocations = false;

        assets.forEach((asset, index) => {
            if (asset.latest_location) {
                hasLocations = true;
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

                const infoWindow = new google.maps.InfoWindow({
                    content: createInfoWindowContent(asset)
                });

                marker.addListener('click', () => {
                    // Close other info windows
                    infoWindows.forEach(iw => iw.close());
                    infoWindow.open(map, marker);
                });

                infoWindows.push(infoWindow);
                bounds.extend(pos);
            }
        });

        if (hasLocations && !bounds.isEmpty()) {
            map.fitBounds(bounds);
            // Add padding to bounds
            const padding = 50;
            map.panToBounds(bounds, padding);
        } else {
            map.setCenter({ lat: -1.2921, lng: 36.8219 });
            map.setZoom(12);
        }
    }

    function createInfoWindowContent(asset) {
        const statusBadge = asset.status === 'active' 
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge" style="background: #fee2e2; color: #991b1b;">Inactive</span>';
        
        return `
            <div style="padding: 12px; min-width: 220px; font-family: inherit;">
                <h6 style="margin: 0 0 8px; font-size: 1.05rem; font-weight: 700;">${asset.name}</h6>
                <p style="margin: 6px 0; font-size: 0.9rem;"><strong>Code:</strong> ${asset.asset_code || 'N/A'}</p>
                <p style="margin: 6px 0; font-size: 0.9rem;"><strong>Category:</strong> ${asset.category ? asset.category.name : 'N/A'}</p>
                <p style="margin: 6px 0; font-size: 0.9rem;"><strong>Status:</strong> ${statusBadge}</p>
                <hr style="margin: 8px 0; border: none; border-top: 1px solid #dbe4f0;">
                <p style="margin: 6px 0 0; font-size: 0.85rem; color: #64748b;">
                    Last Update: ${asset.latest_location.captured_at || asset.latest_location.created_at}
                </p>
            </div>
        `;
    }
</script>
@endsection
