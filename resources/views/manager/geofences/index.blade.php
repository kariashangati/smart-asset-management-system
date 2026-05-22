@extends('layouts.manager')

@php
    if (!isset($assets)) {
        $assets = App\Models\Asset::all();
    }
@endphp

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Safe zones</p>
        <h1>Geofences</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('manager.dashboard') }}">Dashboard</a> / <span>Geofences</span>
        </p>
    </div>
    <div class="button-row">
        <button type="button" class="btn btn-primary" data-modal-open="createGeofenceModal">+ Create Geofence</button>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Asset perimeters</h2>
            <p>Define circular zones around assets for location‑based alerts.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="app-table" data-datatable="true">
            <thead>
                <tr><th>Asset</th><th>Name</th><th>Center (lat/lng)</th><th>Radius (m)</th><th>Status</th><th>Created by</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($geofences as $geofence)
                <tr data-geofence-id="{{ $geofence->id }}"
                    data-asset-id="{{ $geofence->asset_id }}"
                    data-name="{{ $geofence->name }}"
                    data-lat="{{ $geofence->center_latitude }}"
                    data-lng="{{ $geofence->center_longitude }}"
                    data-radius="{{ $geofence->radius_meters }}"
                    data-status="{{ $geofence->status }}">
                    <td>{{ $geofence->asset->name ?? '—' }}</td>
                    <td>{{ $geofence->name }}</td>
                    <td>{{ $geofence->center_latitude }}, {{ $geofence->center_longitude }}</td>
                    <td>{{ $geofence->radius_meters }}</td>
                    <td><span class="badge {{ $geofence->status === 'active' ? 'badge-success' : 'badge-soft' }}">{{ ucfirst($geofence->status) }}</span></td>
                    <td>{{ $geofence->createdByUser->name ?? '—' }}</td>
                    <td class="inline-actions">
                        <button type="button" class="btn btn-outline edit-geofence-btn" data-geofence-id="{{ $geofence->id }}">Edit</button>
                        <form method="POST" action="{{ route('manager.geofences.destroy', $geofence) }}" class="js-confirm-delete" data-title="Delete geofence" data-text="Perimeter {{ $geofence->name }} will be removed.">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- CREATE MODAL (same as admin, but route points to manager.store) --}}
<div id="createGeofenceModal" class="app-modal">
    <div class="modal-panel">
        <form method="POST" action="{{ route('manager.geofences.store') }}">
            @csrf
            <div class="modal-header">
                <div><h2>Create geofence</h2><p>Set a circular perimeter for an asset.</p></div>
                <button type="button" class="icon-button" data-modal-close>✕</button>
            </div>
            <div class="modal-body">
                <div class="form-stack">
                    <div class="form-group"><label>Asset *</label><select name="asset_id" required>@foreach($assets as $asset)<option value="{{ $asset->id }}">{{ $asset->asset_code }} – {{ $asset->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label>Geofence name *</label><input type="text" name="name" required></div>
                    <div class="form-group"><label>Center latitude *</label><input type="number" step="any" name="center_latitude" required></div>
                    <div class="form-group"><label>Center longitude *</label><input type="number" step="any" name="center_longitude" required></div>
                    <div class="form-group"><label>Radius (meters) *</label><input type="number" name="radius_meters" value="100" required></div>
                    <div class="form-group"><label>Status</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div id="editGeofenceModal" class="app-modal">
    <div class="modal-panel">
        <form method="POST" id="editGeofenceForm">
            @csrf @method('PUT')
            <div class="modal-header">
                <div><h2>Edit geofence</h2><p>Modify perimeter settings.</p></div>
                <button type="button" class="icon-button" data-modal-close>✕</button>
            </div>
            <div class="modal-body">
                <div class="form-stack">
                    <div class="form-group"><label>Asset *</label><select name="asset_id" id="edit_asset_id" required>@foreach($assets as $asset)<option value="{{ $asset->id }}">{{ $asset->asset_code }} – {{ $asset->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label>Geofence name *</label><input type="text" name="name" id="edit_name" required></div>
                    <div class="form-group"><label>Center latitude *</label><input type="number" step="any" name="center_latitude" id="edit_lat" required></div>
                    <div class="form-group"><label>Center longitude *</label><input type="number" step="any" name="center_longitude" id="edit_lng" required></div>
                    <div class="form-group"><label>Radius (meters) *</label><input type="number" name="radius_meters" id="edit_radius" required></div>
                    <div class="form-group"><label>Status</label><select name="status" id="edit_status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.edit-geofence-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const row = btn.closest('tr');
                const geofenceId = row.dataset.geofenceId;
                const form = document.getElementById('editGeofenceForm');
                form.action = '/manager/geofences/' + geofenceId;

                document.getElementById('edit_asset_id').value = row.dataset.assetId || '';
                document.getElementById('edit_name').value = row.dataset.name || '';
                document.getElementById('edit_lat').value = row.dataset.lat || '';
                document.getElementById('edit_lng').value = row.dataset.lng || '';
                document.getElementById('edit_radius').value = row.dataset.radius || '';
                document.getElementById('edit_status').value = row.dataset.status || 'active';

                document.getElementById('editGeofenceModal').classList.add('is-open');
                document.body.classList.add('modal-open');
            });
        });
    });
</script>
@endpush