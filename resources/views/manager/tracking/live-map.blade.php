@extends('layouts.manager')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Real-time</p>
        <h1>Live Asset Map</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('manager.dashboard') }}">Dashboard</a> / <span>Live Map</span>
        </p>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Asset locations</h2>
            <p>Last known positions of all tracked assets.</p>
        </div>
    </div>
    <div id="map" style="height: 500px; width: 100%; border-radius: var(--radius-md);"></div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { z-index: 1; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([-6.792354, 39.208328], 13);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
        }).addTo(map);

        var assets = @json($assets);

        assets.forEach(function(asset) {
            var location = asset.latest_location;
            if (location && location.latitude && location.longitude) {
                var marker = L.marker([location.latitude, location.longitude]).addTo(map);
                marker.bindPopup(`
                    <strong>${asset.name}</strong><br>
                    Asset code: ${asset.asset_code}<br>
                    Last seen: ${new Date(location.last_recorded_at).toLocaleString()}<br>
                    <a href="{{ url('/manager/tracking/asset-history') }}/${asset.id}">View history</a>
                `);
            }
        });
    });
</script>
@endpush
@endsection