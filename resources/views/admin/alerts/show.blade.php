@extends('layouts.admin')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Alert details</p>
        <h1>{{ $alert->title }}</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
            <a href="{{ route('admin.alerts.index') }}">Alerts</a> /
            <span>Detail</span>
        </p>
    </div>
</div>

<div class="content-card">
    <div class="detail-grid">
        <div class="detail-item">
            <span>Alert type</span>
            <strong>{{ str_replace('_', ' ', ucfirst($alert->alert_type)) }}</strong>
        </div>
        <div class="detail-item">
            <span>Severity</span>
            <strong>{{ ucfirst($alert->severity) }}</strong>
        </div>
        <div class="detail-item">
            <span>Asset</span>
            <strong>{{ $alert->asset->name ?? '—' }} ({{ $alert->asset->asset_code ?? 'N/A' }})</strong>
        </div>
        <div class="detail-item">
            <span>Tracker device</span>
            <strong>{{ $alert->trackerDevice->device_name ?? '—' }}</strong>
        </div>
        <div class="detail-item">
            <span>Triggered at</span>
            <strong>{{ $alert->triggered_at->format('d M Y H:i:s') }}</strong>
        </div>
        <div class="detail-item">
            <span>Status</span>
            <strong>{{ ucfirst($alert->status) }}</strong>
        </div>
        @if($alert->latitude && $alert->longitude)
        <div class="detail-item field-span-2">
            <span>Location</span>
            <strong>Lat: {{ $alert->latitude }}, Lng: {{ $alert->longitude }}</strong>
            <div id="map" style="height: 300px; margin-top: 12px;"></div>
        </div>
        @endif
        <div class="detail-item field-span-2">
            <span>Message</span>
            <strong>{{ $alert->message }}</strong>
        </div>
    </div>

    @if($alert->status === 'unread' || $alert->status !== 'resolved')
    <div class="button-row mt-4">
        @if($alert->status === 'unread')
            <form method="POST" action="{{ route('admin.alerts.mark-read', $alert) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary">Mark as read</button>
            </form>
        @endif
        @if($alert->status !== 'resolved')
            <form method="POST" action="{{ route('admin.alerts.mark-resolved', $alert) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-success">Resolve alert</button>
            </form>
        @endif
        <a href="{{ route('admin.alerts.index') }}" class="btn btn-outline">Back to list</a>
    </div>
    @endif
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    @if($alert->latitude && $alert->longitude)
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([{{ $alert->latitude }}, {{ $alert->longitude }}], 15);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB'
        }).addTo(map);
        L.marker([{{ $alert->latitude }}, {{ $alert->longitude }}]).addTo(map)
            .bindPopup('Alert location').openPopup();
    });
    @endif
</script>
@endpush