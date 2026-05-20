@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Manage Assets</h3>
        <a href="{{ route('admin.assets.create') }}" class="btn btn-primary">Add Asset</a>
    </div>
    <div class="card-body">
        <table class="table table-bordered" id="assetsTable">
            <thead>
                <tr><th>Image</th><th>Asset Code</th><th>Name</th><th>Category</th><th>Department</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($assets as $asset)
                <tr>
                    <td>
                        @if($asset->image)
                            <img src="{{ Storage::url($asset->image) }}" width="50" height="50" style="object-fit: cover;">
                        @else
                            <span>No Image</span>
                        @endif
                    </td>
                    <td>{{ $asset->asset_code }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category->name ?? '—' }}</td>
                    <td>{{ $asset->department->name ?? '—' }}</td>
                    <td>
                        <span class="badge bg-{{ $asset->status == 'active' ? 'success' : ($asset->status == 'inactive' ? 'warning' : 'danger') }}">
                            {{ ucfirst($asset->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.assets.show', $asset) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('admin.assets.edit', $asset) }}" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger delete-asset" data-id="{{ $asset->id }}">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header"><h5>Confirm Delete</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">Are you sure you want to delete this asset?</div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Delete</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#assetsTable').DataTable();

        $('.delete-asset').click(function() {
            let assetId = $(this).data('id');
            let form = $('#deleteForm');
            form.attr('action', '/admin/assets/' + assetId);
            $('#deleteModal').modal('show');
        });
    });
</script>
@endpush