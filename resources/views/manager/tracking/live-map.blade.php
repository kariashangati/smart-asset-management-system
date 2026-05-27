@extends('layouts.manager')

@section('page_title', 'Department Live Map')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Filter Department Assets</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('manager.tracking.live-map') }}" class="row g-3">
            <div class="col-md-4">
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
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="actual" id="actualFilter" value="1" {{ request('actual') ? 'checked' : '' }}>
                    <label class="form-check-label" for="actualFilter">
                        With Location
                    </label>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filter Map</button>
                <a href="{{ route('manager.tracking.live-map') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>
</div>

<div id="map" style="height: 700px; width: 100%; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
<script>
    function initMap() {
        const assets = @json($assets);
        const mapElement = document.getElementById('map');
        
        const map = new google.maps.Map(mapElement, {
            zoom: 12,
            center: { lat: 0, lng: 0 },
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
                    icon: asset.status === 'active' ? 'http://maps.google.com/mapfiles/ms/icons/green-dot.png' : 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                });

                marker.addListener('click', () => {
                    const content = `
                        <div style="padding: 10px; min-width: 180px;">
                            <h6 class="mb-1">${asset.name}</h6>
                            <p class="mb-1 small"><strong>Category:</strong> ${asset.category ? asset.category.name : 'N/A'}</p>
                            <p class="mb-1 small"><strong>Status:</strong> <span class="badge ${asset.status === 'active' ? 'bg-success' : 'bg-secondary'}">${asset.status}</span></p>
                            <hr class="my-2">
                            <p class="mb-0 x-small text-muted">Last Update: ${asset.latest_location.captured_at || asset.latest_location.created_at}</p>
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
            map.setCenter({ lat: 0, lng: 0 });
            map.setZoom(2);
        }
    }
</script>
@endsection
@endsection