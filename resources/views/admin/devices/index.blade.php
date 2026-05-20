@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Manage Tracker Devices</h3>
        <a href="{{ route('admin.devices.create') }}" class="btn btn-primary">Add Device</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="devicesTable">
            <thead>
                <tr><th>Device Code</th><th>Name</th><th>IMEI</th><th>Status</th><th>Last Seen</th><th>Assigned Asset</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($devices as $device)
                <tr>
                    <td>{{ $device->device_code }}</td>
                    <td>{{ $device->device_name }}</td>
                    <td>{{ $device->imei ?? '—' }}</td>
                    <td>
                        <span class="badge bg-{{ $device->status == 'active' ? 'success' : ($device->status == 'inactive' ? 'warning' : 'danger') }}">
                            {{ ucfirst($device->status) }}
                        </span>
                    </td>
                    <td>{{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Never' }}</td>
                    <td>{{ $device->activeAssignment->asset->name ?? 'Not assigned' }}</td>
                    <td>
                        <a href="{{ route('admin.devices.show', $device) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('admin.devices.edit', $device) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger delete-device" data-id="{{ $device->id }}">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf @method('DELETE')
                <div class="modal-header"><h5>Confirm Delete</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">Delete this device?</div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Delete</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#devicesTable').DataTable();

        $('.delete-device').click(function() {
            let deviceId = $(this).data('id');
            $('#deleteForm').attr('action', '/admin/devices/' + deviceId);
            $('#deleteModal').modal('show');
        });
    });
</script>
@endpush