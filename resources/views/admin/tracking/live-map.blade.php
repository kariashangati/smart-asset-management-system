@extends('layouts.admin')

@section('title', 'Live Asset Map')
@section('portal_label', 'Admin Portal')
@section('page_title', 'Live Asset Map')
@section('dashboard_url', route('admin.dashboard'))

@push('styles')
<style>
    .live-map-container {
        height: 560px;
        width: 100%;
        border-radius: var(--radius-md);
        overflow: hidden;
        border: 1px solid var(--border);
        margin-top: 20px;
    }

    .filter-form {
        margin-bottom: 0;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
        align-items: end;
    }

    .filter-grid .form-group label {
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: 4px;
        display: block;
        color: var(--muted);
    }

    .filter-grid .form-group select {
        width: 100%;
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 10px 12px;
        font: inherit;
        font-size: 0.9rem;
        background: #fff;
        outline: none;
    }

    .filter-grid .form-group select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(21, 94, 239, 0.1);
    }

    .filter-grid .checkbox-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 2px;
        font-weight: 600;
        font-size: 0.88rem;
    }

    .filter-grid .button-row {
        display: flex;
        gap: 10px;
    }

    .map-legend {
        display: flex;
        gap: 18px;
        margin-top: 12px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.83rem;
        font-weight: 600;
        color: var(--muted);
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .legend-dot.green  { background: #16a34a; }
    .legend-dot.orange { background: #ea580c; }
    .legend-dot.gray   { background: #9ca3af; }

    .map-no-assets {
        height: 560px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: var(--surface-soft);
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        color: var(--muted);
        margin-top: 20px;
        gap: 12px;
    }

    .map-no-assets svg {
        opacity: 0.3;
    }

    .btn-light {
        background: var(--surface-soft);
        color: var(--text);
        border: 1px solid var(--border);
        border-radius: 999px;
        padding: 10px 18px;
        cursor: pointer;
        font-weight: 700;
        font: inherit;
        text-decoration: none;
        display: inline-block;
        transition: background 0.15s;
    }

    .btn-light:hover {
        background: var(--border);
    }
</style>
@endpush

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Asset tracking</p>
        <h1>Live Asset Map</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
            <span>Live Map</span>
        </p>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>
                Active Tracking
                <span class="badge badge-soft" style="font-size:0.82rem; margin-left:8px;">
                    {{ $assets->count() }} asset{{ $assets->count() !== 1 ? 's' : '' }}
                </span>
            </h2>
            <p>Real-time location of all system assets. Click a pin for details.</p>
        </div>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('admin.tracking.live-map') }}" class="filter-form">
        <div class="filter-grid">
            <div class="form-group">
                <label>Department</label>
                <select name="department_id">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Category</label>
                <select name="category_id">
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
                <select name="status">
                    <option value="">All Statuses</option>
                    <option value="active"      {{ request('status') === 'active'      ? 'selected' : '' }}>Active</option>
                    <option value="inactive"    {{ request('status') === 'inactive'    ? 'selected' : '' }}>Inactive</option>
                    <option value="missing"     {{ request('status') === 'missing'     ? 'selected' : '' }}>Missing</option>
                    <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>

            <label class="checkbox-row" style="align-self:end; padding-bottom:6px;">
                <input type="checkbox" name="actual" value="1" {{ request()->boolean('actual') ? 'checked' : '' }}>
                Has GPS data only
            </label>

            <div class="button-row" style="align-self:end;">
                <button type="submit" class="btn btn-primary">Apply</button>
                <a href="{{ route('admin.tracking.live-map') }}" class="btn-light">Reset</a>
            </div>
        </div>
    </form>

    {{-- Legend --}}
    <div class="map-legend">
        <div class="legend-item"><div class="legend-dot green"></div> Active</div>
        <div class="legend-item"><div class="legend-dot orange"></div> Missing / Maintenance</div>
        <div class="legend-item"><div class="legend-dot gray"></div> Inactive / No GPS</div>
    </div>

    {{-- Map or empty state --}}
    @php
        $assetsWithLocation = $assets->filter(fn($a) => $a->latestLocation);
    @endphp

    @if($assetsWithLocation->isNotEmpty())
        <div id="map" class="live-map-container"></div>
    @else
        <div class="map-no-assets">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <p style="margin:0; font-weight:700;">No GPS data available for the current filter.</p>
            <p style="margin:0; font-size:0.88rem;">Assign tracker devices to assets and send location updates to see them here.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
{{-- Inline asset data for the map --}}
<script>
    window.__ASSETS__ = @json(
        $assets->map(function ($asset) {
            $loc = $asset->latestLocation;
            return [
                'id'         => $asset->id,
                'name'       => $asset->name,
                'asset_code' => $asset->asset_code,
                'status'     => $asset->status,
                'department' => optional($asset->department)->name,
                'category'   => optional($asset->category)->name,
                'history_url'=> route('admin.tracking.asset-history', $asset),
                'lat'        => $loc ? (float) $loc->latitude  : null,
                'lng'        => $loc ? (float) $loc->longitude : null,
                'last_seen'  => $loc ? optional($loc->last_recorded_at)->format('d M Y H:i') : null,
            ];
        })
    );

    window.GOOGLE_MAPS_API_KEY = "{{ env('GOOGLE_MAPS_API_KEY') }}";
</script>

<script>
    (function () {
        'use strict';

        function loadGoogleMaps(apiKey, callback) {
            if (window.google && window.google.maps) {
                callback();
                return;
            }
            window.__mapInitCallback__ = callback;
            var script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key=' + apiKey + '&callback=__mapInitCallback__';
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        }

        function iconUrl(status) {
            var colors = {
                active:      'green',
                inactive:    'gray',
                missing:     'orange',
                maintenance: 'orange',
            };
            var color = colors[status] || 'gray';
            return 'https://maps.google.com/mapfiles/ms/icons/' + color + '-dot.png';
        }

        function buildInfoContent(asset) {
            var statusBadge = {
                active:      '<span style="color:#166534;background:#dcfce7;padding:2px 8px;border-radius:999px;font-size:0.78rem;font-weight:700;">Active</span>',
                inactive:    '<span style="color:#854d0e;background:#fef3c7;padding:2px 8px;border-radius:999px;font-size:0.78rem;font-weight:700;">Inactive</span>',
                missing:     '<span style="color:#991b1b;background:#fee2e2;padding:2px 8px;border-radius:999px;font-size:0.78rem;font-weight:700;">Missing</span>',
                maintenance: '<span style="color:#854d0e;background:#fef3c7;padding:2px 8px;border-radius:999px;font-size:0.78rem;font-weight:700;">Maintenance</span>',
            };
            return '<div style="padding:6px 2px; min-width:200px; font-family:inherit;">'
                + '<strong style="font-size:1rem;">' + asset.name + '</strong><br>'
                + '<span style="color:#64748b;font-size:0.82rem;">' + asset.asset_code + '</span>'
                + '<hr style="margin:8px 0;border:0;border-top:1px solid #e2e8f0;">'
                + '<table style="font-size:0.85rem;border-collapse:collapse;width:100%;">'
                + '<tr><td style="color:#64748b;padding:2px 0;padding-right:8px;">Status</td><td>' + (statusBadge[asset.status] || asset.status) + '</td></tr>'
                + (asset.department ? '<tr><td style="color:#64748b;padding:2px 0;padding-right:8px;">Dept</td><td>' + asset.department + '</td></tr>' : '')
                + (asset.category   ? '<tr><td style="color:#64748b;padding:2px 0;padding-right:8px;">Category</td><td>' + asset.category + '</td></tr>' : '')
                + (asset.last_seen  ? '<tr><td style="color:#64748b;padding:2px 0;padding-right:8px;">Last seen</td><td>' + asset.last_seen + '</td></tr>' : '')
                + '</table>'
                + '<a href="' + asset.history_url + '" style="display:inline-block;margin-top:10px;background:#155eef;color:#fff;padding:6px 14px;border-radius:999px;font-size:0.82rem;font-weight:700;text-decoration:none;">View history →</a>'
                + '</div>';
        }

        function initMap() {
            var assets = window.__ASSETS__ || [];
            var mapEl  = document.getElementById('map');
            if (!mapEl) return;

            var map = new google.maps.Map(mapEl, {
                zoom: 12,
                center: { lat: -6.792354, lng: 39.208328 }, // Dar es Salaam default
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_RIGHT,
                    mapTypeIds: [
                        google.maps.MapTypeId.ROADMAP,
                        google.maps.MapTypeId.SATELLITE,
                        google.maps.MapTypeId.HYBRID,
                        google.maps.MapTypeId.TERRAIN,
                    ],
                },
                fullscreenControl: true,
                streetViewControl: false,
                zoomControl: true,
            });

            var infoWindow = new google.maps.InfoWindow();
            var bounds    = new google.maps.LatLngBounds();
            var pinCount  = 0;

            assets.forEach(function (asset) {
                if (asset.lat === null || asset.lng === null) return;

                var pos    = { lat: asset.lat, lng: asset.lng };
                var marker = new google.maps.Marker({
                    position: pos,
                    map:      map,
                    title:    asset.name,
                    icon:     iconUrl(asset.status),
                    animation: google.maps.Animation.DROP,
                });

                marker.addListener('click', function () {
                    infoWindow.setContent(buildInfoContent(asset));
                    infoWindow.open(map, marker);
                });

                bounds.extend(pos);
                pinCount++;
            });

            if (pinCount > 0) {
                map.fitBounds(bounds);
                // Don't zoom too far in when there's only one pin
                google.maps.event.addListenerOnce(map, 'bounds_changed', function () {
                    if (map.getZoom() > 16) map.setZoom(16);
                });
            }
        }

        // Expose globally so the Google callback can reach it
        window.__mapInitCallback__ = initMap;

        document.addEventListener('DOMContentLoaded', function () {
            var key = window.GOOGLE_MAPS_API_KEY;
            if (!key || key === '' || key === 'null') {
                var el = document.getElementById('map');
                if (el) {
                    el.innerHTML = '<div style="height:100%;display:flex;align-items:center;justify-content:center;color:#64748b;flex-direction:column;gap:8px;">'
                        + '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>'
                        + '<strong>Google Maps API key not configured.</strong>'
                        + '<span style="font-size:0.85rem;">Set GOOGLE_MAPS_API_KEY in your .env file.</span>'
                        + '</div>';
                }
                return;
            }
            loadGoogleMaps(key, initMap);
        });
    })();
</script>
@endpush
