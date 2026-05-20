@extends('layouts.admin')

@php
    if (!isset($assets)) {
        $assets = App\Models\Asset::whereDoesntHave('activeAssignment')->get();
    }
    if (!isset($devices)) {
        $devices = App\Models\TrackerDevice::where('status', 'active')
            ->whereDoesntHave('activeAssignment')
            ->get();
    }
@endphp

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Device‑to‑asset binding</p>
        <h1>Assignments</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
            <span>Assignments</span>
        </p>
    </div>
    <div class="button-row">
        <button type="button" class="btn btn-primary" data-modal-open="createAssignmentModal">
            + Assign Device
        </button>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Assignment history</h2>
            <p>Active and past assignments between trackers and assets.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="app-table" data-datatable="true">
            <thead>
                <tr><th>Asset</th><th>Tracker device</th><th>Assigned by</th><th>Assigned at</th><th>Unassigned at</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($assignments as $assignment)
                <tr>
                    <td>{{ $assignment->asset->name ?? '—' }}</td>
                    <td>{{ $assignment->trackerDevice->device_name ?? '—' }}</td>
                    <td>{{ $assignment->assignedByUser->name ?? '—' }}</td>
                    <td>{{ $assignment->assigned_at->format('d M Y H:i') }}</td>
                    <td>{{ $assignment->unassigned_at ? $assignment->unassigned_at->format('d M Y H:i') : '—' }}</td>
                    <td><span class="badge {{ $assignment->is_active ? 'badge-success' : 'badge-soft' }}">{{ $assignment->is_active ? 'Active' : 'Ended' }}</span></td>
                    <td>
                        @if($assignment->is_active)
                        <form method="POST" action="{{ route('admin.assignments.destroy', $assignment) }}" class="js-confirm-delete" data-title="End assignment" data-text="This will detach the tracker from the asset.">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger">End</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- CREATE ASSIGNMENT MODAL --}}
<div id="createAssignmentModal" class="app-modal">
    <div class="modal-panel">
        <form method="POST" action="{{ route('admin.assignments.store') }}">
            @csrf
            <div class="modal-header">
                <div><h2>Assign tracker to asset</h2><p>Select an asset without active tracker and an unassigned device.</p></div>
                <button type="button" class="icon-button" data-modal-close>✕</button>
            </div>
            <div class="modal-body">
                <div class="form-stack">
                    <div class="form-group">
                        <label>Asset *</label>
                        <select name="asset_id" required>
                            <option value="">Choose asset</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->asset_code }} – {{ $asset->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tracker device *</label>
                        <select name="tracker_device_id" required>
                            <option value="">Choose device</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}">{{ $device->device_code }} – {{ $device->device_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Assignment date/time (optional)</label>
                        <input type="datetime-local" name="assigned_at">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Assign</button>
            </div>
        </form>
    </div>
</div>
@endsection