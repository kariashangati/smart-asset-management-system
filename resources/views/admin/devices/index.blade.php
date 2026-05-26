@extends('layouts.admin')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Tracker hardware</p>
        <h1>Tracker Devices</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
            <span>Devices</span>
        </p>
    </div>
    <div class="button-row">
        <button type="button" class="btn btn-primary" data-modal-open="createDeviceModal">
            + Register Device
        </button>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>GPS tracking devices</h2>
            <p>Manage hardware trackers, IMEI, API tokens, and assignment status.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="app-table" data-datatable="true" id="devices-table">
            <thead>
                <tr><th>Device Code</th><th>Name</th><th>IMEI</th><th>Status</th><th>Last seen</th><th>Assigned to</th><th>Department</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($devices as $device)
                <tr data-device-id="{{ $device->id }}"
                    data-device-code="{{ $device->device_code }}"
                    data-device-name="{{ $device->device_name }}"
                    data-imei="{{ $device->imei }}"
                    data-sim-number="{{ $device->sim_number }}"
                    data-api-token="{{ $device->api_token_hash }}"
                    data-status="{{ $device->status }}"
                    data-battery="{{ $device->battery_level }}"
                    data-firmware="{{ $device->firmware_version }}">
                    <td>{{ $device->device_code }}</td>
                    <td>{{ $device->device_name }}</td>
                    <td>{{ $device->imei ?? '—' }}</td>
                    <td><span class="badge {{ $device->status === 'active' ? 'badge-success' : ($device->status === 'inactive' ? 'badge-warning' : 'badge-soft') }}">{{ ucfirst($device->status) }}</span></td>
                    <td>{{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never' }}</td>
                    <td>{{ $device->activeAssignment->asset->name ?? 'Not assigned' }}</td>
                    <td>{{ $device->activeAssignment->asset->department->name ?? '—' }}</td>
                    <td class="inline-actions">
                        <button type="button" class="btn btn-outline edit-device-btn" data-device-id="{{ $device->id }}">Edit</button>
                        <form method="POST" action="{{ route('admin.devices.destroy', $device) }}" class="js-confirm-delete" data-title="Delete device" data-text="Device {{ $device->device_code }} will be removed." style="display: inline;">
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

{{-- CREATE MODAL --}}
<div id="createDeviceModal" class="app-modal">
    <div class="modal-panel modal-wide">
        <form method="POST" action="{{ route('admin.devices.store') }}">
            @csrf
            <div class="modal-header">
                <div><h2>Register tracker device</h2><p>Enter hardware details.</p></div>
                <button type="button" class="icon-button" data-modal-close>✕</button>
            </div>
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group"><label>Device code *</label><input type="text" name="device_code" required></div>
                    <div class="form-group"><label>Device name *</label><input type="text" name="device_name" required></div>
                    <div class="form-group"><label>IMEI</label><input type="text" name="imei"></div>
                    <div class="form-group"><label>SIM number</label><input type="text" name="sim_number"></div>
                    <div class="form-group"><label>API Token Hash *</label><input type="text" name="api_token_hash" required></div>
                    <div class="form-group"><label>Status</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option><option value="faulty">Faulty</option></select></div>
                    <div class="form-group"><label>Battery level (%)</label><input type="number" name="battery_level" min="0" max="100"></div>
                    <div class="form-group"><label>Firmware version</label><input type="text" name="firmware_version"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div id="editDeviceModal" class="app-modal">
    <div class="modal-panel modal-wide">
        <form method="POST" id="editDeviceForm">
            @csrf @method('PUT')
            <div class="modal-header">
                <div><h2>Edit device</h2><p>Update tracker information.</p></div>
                <button type="button" class="icon-button" data-modal-close>✕</button>
            </div>
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group"><label>Device code *</label><input type="text" name="device_code" id="edit_device_code" required></div>
                    <div class="form-group"><label>Device name *</label><input type="text" name="device_name" id="edit_device_name" required></div>
                    <div class="form-group"><label>IMEI</label><input type="text" name="imei" id="edit_imei"></div>
                    <div class="form-group"><label>SIM number</label><input type="text" name="sim_number" id="edit_sim_number"></div>
                    <div class="form-group"><label>Status</label><select name="status" id="edit_status"><option value="active">Active</option><option value="inactive">Inactive</option><option value="faulty">Faulty</option></select></div>
                    <div class="form-group"><label>Battery level (%)</label><input type="number" name="battery_level" id="edit_battery_level" min="0" max="100"></div>
                    <div class="form-group"><label>Firmware version</label><input type="text" name="firmware_version" id="edit_firmware_version"></div>
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
        document.querySelectorAll('.edit-device-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const row = btn.closest('tr');
                const deviceId = row.dataset.deviceId;
                const form = document.getElementById('editDeviceForm');
                form.action = '/admin/devices/' + deviceId;
                document.getElementById('edit_device_code').value = row.dataset.deviceCode || '';
                document.getElementById('edit_device_name').value = row.dataset.deviceName || '';
                document.getElementById('edit_imei').value = row.dataset.imei || '';
                document.getElementById('edit_sim_number').value = row.dataset.simNumber || '';
                document.getElementById('edit_status').value = row.dataset.status || 'active';
                document.getElementById('edit_battery_level').value = row.dataset.battery || '';
                document.getElementById('edit_firmware_version').value = row.dataset.firmware || '';
                document.getElementById('editDeviceModal').classList.add('is-open');
                document.body.classList.add('modal-open');
            });
        });
    });
</script>
@endpush