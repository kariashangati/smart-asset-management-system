@extends('layouts.admin')

@php
    if (!isset($categories)) {
        $categories = App\Models\AssetCategory::all();
    }
    if (!isset($departments)) {
        $departments = App\Models\Department::all();
    }
@endphp

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Asset registry</p>
        <h1>Assets</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
            <span>Assets</span>
        </p>
    </div>
    <div class="button-row">
        <button type="button" class="btn btn-primary" data-modal-open="createAssetModal">
            + New Asset
        </button>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Institutional assets</h2>
            <p>Manage all movable assets, assign trackers, and monitor status.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="app-table" data-datatable="true" id="assets-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Asset Code</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $asset)
                <tr data-asset-id="{{ $asset->id }}"
                    data-asset-code="{{ $asset->asset_code }}"
                    data-name="{{ $asset->name }}"
                    data-serial-number="{{ $asset->serial_number }}"
                    data-category-id="{{ $asset->asset_category_id }}"
                    data-department-id="{{ $asset->department_id }}"
                    data-purchase-date="{{ $asset->purchase_date?->format('Y-m-d') }}"
                    data-asset-value="{{ $asset->asset_value }}"
                    data-status="{{ $asset->status }}"
                    data-description="{{ $asset->description }}"
                    data-image-preview="{{ $asset->image ? Storage::url($asset->image) : '' }}">
                    <td class="asset-image-cell">
                        @if($asset->image)
                            <img src="{{ Storage::url($asset->image) }}" width="45" height="45" style="object-fit: cover; border-radius: 8px;">
                        @else
                            <span class="badge badge-soft">No image</span>
                        @endif
                    </td>
                    <td>{{ $asset->asset_code }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category->name ?? '—' }}</td>
                    <td>{{ $asset->department->name ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $asset->status === 'active' ? 'badge-success' : ($asset->status === 'inactive' ? 'badge-warning' : 'badge-soft') }}">
                            {{ ucfirst($asset->status) }}
                        </span>
                    </td>
                    <td class="inline-actions">
                        <button type="button" class="btn btn-outline edit-asset-btn" data-asset-id="{{ $asset->id }}">Edit</button>
                        <form method="POST" action="{{ route('admin.assets.destroy', $asset) }}" class="js-confirm-delete" data-title="Delete asset" data-text="Asset {{ $asset->asset_code }} will be permanently removed." style="display:inline;">
                            @csrf
                            @method('DELETE')
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
<div id="createAssetModal" class="app-modal">
    <div class="modal-panel modal-wide">
        <form method="POST" action="{{ route('admin.assets.store') }}" enctype="multipart/form-data" id="createAssetForm">
            @csrf
            <div class="modal-header">
                <div>
                    <h2>Create asset</h2>
                    <p>Fill the form below to register a new movable asset.</p>
                </div>
                <button type="button" class="icon-button" data-modal-close>✕</button>
            </div>
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Asset code *</label>
                        <input type="text" name="asset_code" required>
                    </div>
                    <div class="form-group">
                        <label>Asset name *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Serial number</label>
                        <input type="text" name="serial_number">
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="asset_category_id" required>
                            <option value="">Select</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Department *</label>
                        <select name="department_id" required>
                            <option value="">Select</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Purchase date</label>
                        <input type="date" name="purchase_date">
                    </div>
                    <div class="form-group">
                        <label>Asset value (TZS)</label>
                        <input type="number"
                               name="asset_value"
                               min="0"
                               step="0.01"
                               placeholder="e.g. 5000000">
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="missing">Missing</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Asset image</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <div class="form-group field-span-2">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Save asset</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div id="editAssetModal" class="app-modal">
    <div class="modal-panel modal-wide">
        <form method="POST" enctype="multipart/form-data" id="editAssetForm">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <div>
                    <h2>Edit asset</h2>
                    <p>Update asset details.</p>
                </div>
                <button type="button" class="icon-button" data-modal-close>✕</button>
            </div>
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Asset code *</label>
                        <input type="text" name="asset_code" id="edit_asset_code" required>
                    </div>
                    <div class="form-group">
                        <label>Asset name *</label>
                        <input type="text" name="name" id="edit_name" required>
                    </div>
                    <div class="form-group">
                        <label>Serial number</label>
                        <input type="text" name="serial_number" id="edit_serial_number">
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="asset_category_id" id="edit_asset_category_id" required>
                            <option value="">Select</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Department *</label>
                        <select name="department_id" id="edit_department_id" required>
                            <option value="">Select</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Purchase date</label>
                        <input type="date" name="purchase_date" id="edit_purchase_date">
                    </div>
                    <div class="form-group">
                        <label>Asset value (TZS)</label>
                        <input type="number"
                               name="asset_value"
                               id="edit_asset_value"
                               min="0"
                               step="0.01"
                               placeholder="e.g. 5000000">
                    </div>
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" id="edit_status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="missing">Missing</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Asset image</label>
                        <input type="file" name="image" accept="image/*" id="edit_image">
                        <div id="current_image_preview" style="margin-top: 8px;"></div>
                    </div>
                    <div class="form-group field-span-2">
                        <label>Description</label>
                        <textarea name="description" id="edit_description" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Update asset</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.edit-asset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const assetId = row.dataset.assetId;
                const form = document.getElementById('editAssetForm');
                form.action = '/admin/assets/' + assetId;

                document.getElementById('edit_asset_code').value = row.dataset.assetCode || '';
                document.getElementById('edit_name').value = row.dataset.name || '';
                document.getElementById('edit_serial_number').value = row.dataset.serialNumber || '';
                document.getElementById('edit_asset_category_id').value = row.dataset.categoryId || '';
                document.getElementById('edit_department_id').value = row.dataset.departmentId || '';
                document.getElementById('edit_purchase_date').value = row.dataset.purchaseDate || '';
                document.getElementById('edit_asset_value').value = row.dataset.assetValue || '';
                document.getElementById('edit_status').value = row.dataset.status || 'active';
                document.getElementById('edit_description').value = row.dataset.description || '';

                const previewDiv = document.getElementById('current_image_preview');
                const currentImage = row.dataset.imagePreview;
                if (currentImage) {
                    previewDiv.innerHTML = '<img src="' + currentImage + '" width="80" style="border-radius: 8px;"><br><small>Current image</small>';
                } else {
                    previewDiv.innerHTML = '';
                }

                const modal = document.getElementById('editAssetModal');
                modal.classList.add('is-open');
                document.body.classList.add('modal-open');
            });
        });
    });
</script>
@endpush
