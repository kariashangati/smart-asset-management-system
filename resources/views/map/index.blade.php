@extends('layouts.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Map Container -->
        <div class="col-lg-8 h-100 p-0">
            <div id="map" class="w-100 h-100" style="min-height: 600px;"></div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 bg-light" style="overflow-y: auto; max-height: 100vh;">
            <div class="p-3">
                <h5 class="mb-3">🗺️ Asset Tracking Map</h5>

                <!-- Map Controls -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Map Controls</h6>
                    </div>
                    <div class="card-body">
                        <!-- Map Type Toggle -->
                        <div class="mb-2">
                            <label class="form-label">Map Type:</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="mapType" id="mapRoadmap" value="roadmap" checked>
                                <label class="btn btn-outline-primary" for="mapRoadmap">Map</label>

                                <input type="radio" class="btn-check" name="mapType" id="mapSatellite" value="satellite">
                                <label class="btn btn-outline-primary" for="mapSatellite">Satellite</label>

                                <input type="radio" class="btn-check" name="mapType" id="mapHybrid" value="hybrid">
                                <label class="btn btn-outline-primary" for="mapHybrid">Hybrid</label>
                            </div>
                        </div>

                        <!-- Filters -->
                        <form id="filterForm" class="mt-3">
                            <div class="mb-2">
                                <label class="form-label">Filter by Status:</label>
                                <select class="form-select form-select-sm" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Filter by Type:</label>
                                <select class="form-select form-select-sm" id="typeFilter">
                                    <option value="">All Types</option>
                                    <option value="vehicle">Vehicle</option>
                                    <option value="equipment">Equipment</option>
                                    <option value="device">Device</option>
                                </select>
                            </div>

                            <button type="button" class="btn btn-primary btn-sm w-100" id="applyFilters">Apply Filters</button>
                        </form>
                    </div>
                </div>

                <!-- Assets List -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="mb-0">Assets (<span id="assetCount">0</span>)</h6>
                        <button class="btn btn-sm btn-light" id="toggleList" title="Toggle List">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="card-body" id="assetsList" style="max-height: 400px; overflow-y: auto;">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>

                <!-- Legend -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Legend</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <span style="color: #10b981;">●</span> Active
                        </div>
                        <div class="mb-2">
                            <span style="color: #f59e0b;">●</span> Maintenance
                        </div>
                        <div class="mb-2">
                            <span style="color: #ef4444;">●</span> Inactive/Retired
                        </div>
                        <div>
                            <span style="color: #3b82f6;">●</span> With Alerts
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{ $maps_api_key }}&callback=initMap" async defer></script>

<script>
let map;
let markers = {};
let geofenceCircles = {};
let currentAssets = [];

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 12,
        center: { lat: 40.7128, lng: -74.0060 },
        mapTypeId: 'roadmap',
        fullscreenControl: true,
        zoomControl: true,
        mapTypeControl: true,
    });

    loadAssets();
    loadGeofences();
    setupEventListeners();

    // Auto-refresh every 30 seconds
    setInterval(loadAssets, 30000);
}

function loadAssets() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;

    let url = '/api/map/assets';
    const params = new URLSearchParams();
    if (statusFilter) params.append('status', statusFilter);
    if (typeFilter) params.append('asset_type', typeFilter);
    if (params.toString()) url += '?' + params.toString();

    fetch(url, {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        currentAssets = data.data;
        document.getElementById('assetCount').textContent = currentAssets.length;
        displayAssets(currentAssets);
        updateMarkers(currentAssets);
    })
    .catch(error => console.error('Error loading assets:', error));
}

function displayAssets(assets) {
    const list = document.getElementById('assetsList');
    if (assets.length === 0) {
        list.innerHTML = '<p class="text-muted">No assets found</p>';
        return;
    }

    list.innerHTML = assets.map(asset => `
        <div class="asset-item p-2 border-bottom cursor-pointer hover-highlight" onclick="focusAsset(${asset.id})">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong>${asset.name}</strong>
                    <div class="small text-muted">${asset.type} • ${asset.status}</div>
                    ${asset.location ? `
                        <div class="small">
                            <i class="fas fa-tachometer-alt"></i> ${asset.location.speed.toFixed(1)} km/h
                        </div>
                    ` : '<div class="small text-danger">No location</div>'}
                </div>
                <span style="background-color: ${asset.color}; width: 12px; height: 12px; border-radius: 50%;"></span>
            </div>
        </div>
    `).join('');
}

function updateMarkers(assets) {
    // Clear old markers
    Object.values(markers).forEach(marker => marker.setMap(null));
    markers = {};

    // Add new markers
    assets.forEach(asset => {
        if (!asset.location) return;

        const marker = new google.maps.Marker({
            position: {
                lat: asset.location.latitude,
                lng: asset.location.longitude
            },
            map: map,
            title: asset.name,
            icon: getMarkerIcon(asset),
        });

        marker.addListener('click', () => {
            showAssetDetails(asset);
        });

        markers[asset.id] = marker;
    });
}

function getMarkerIcon(asset) {
    const color = asset.color.replace('#', '');
    return `https://maps.google.com/mapfiles/ms/micons/${getIconColor(color)}.png`;
}

function getIconColor(color) {
    const colorMap = {
        '10b981': 'green',
        'f59e0b': 'yellow',
        'ef4444': 'red',
        '3b82f6': 'blue',
    };
    return colorMap[color] || 'red';
}

function focusAsset(assetId) {
    const asset = currentAssets.find(a => a.id === assetId);
    if (!asset || !asset.location) return;

    map.panTo({
        lat: asset.location.latitude,
        lng: asset.location.longitude
    });
    map.setZoom(15);

    if (markers[assetId]) {
        markers[assetId].setAnimation(google.maps.Animation.BOUNCE);
        setTimeout(() => markers[assetId].setAnimation(null), 2000);
    }
}

function loadGeofences() {
    fetch('/api/map/geofences', {
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        data.data.forEach(geofence => {
            const circle = new google.maps.Circle({
                center: {
                    lat: geofence.center.latitude,
                    lng: geofence.center.longitude
                },
                radius: geofence.radius,
                map: map,
                fillColor: '#3b82f6',
                fillOpacity: 0.1,
                strokeColor: '#3b82f6',
                strokeOpacity: 0.8,
                strokeWeight: 2,
            });

            geofenceCircles[geofence.id] = circle;
        });
    })
    .catch(error => console.error('Error loading geofences:', error));
}

function showAssetDetails(asset) {
    // Create info window
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div class="map-info-window">
                <h6>${asset.name}</h6>
                <p><strong>Type:</strong> ${asset.type}</p>
                <p><strong>Status:</strong> <span class="badge" style="background-color: ${asset.color};">${asset.status}</span></p>
                ${asset.location ? `
                    <p><strong>Speed:</strong> ${asset.location.speed.toFixed(1)} km/h</p>
                    <p><strong>Updated:</strong> ${new Date(asset.location.last_updated).toLocaleString()}</p>
                ` : ''}
                <a href="/assets/${asset.id}" class="btn btn-sm btn-primary mt-2">View Details</a>
            </div>
        `,
    });

    infoWindow.open(map, markers[asset.id]);
}

function setupEventListeners() {
    // Map type change
    document.querySelectorAll('input[name="mapType"]').forEach(input => {
        input.addEventListener('change', (e) => {
            map.setMapTypeId(e.target.value);
        });
    });

    // Apply filters
    document.getElementById('applyFilters').addEventListener('click', loadAssets);
}

// Initialize map when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMap);
} else {
    initMap();
}
</script>

<style>
.hover-highlight:hover {
    background-color: #f3f4f6;
    cursor: pointer;
}

.map-info-window {
    font-family: Arial, sans-serif;
    min-width: 200px;
}

.map-info-window h6 {
    margin: 0 0 8px 0;
    font-weight: bold;
}

.map-info-window p {
    margin: 4px 0;
    font-size: 12px;
}
</style>
@endsection
