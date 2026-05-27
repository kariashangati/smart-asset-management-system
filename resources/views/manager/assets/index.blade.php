@extends('layouts.manager')

@section('title', 'Department Assets')
@section('portal_label', 'Asset Management')
@section('page_title', 'Departmental Assets')

@php
    if (!isset($categories)) {
        $categories = App\Models\AssetCategory::all();
    }
@endphp

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Asset Inventory</p>
        <h1>Assets</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('manager.dashboard') }}">Dashboard</a> /
            <span>My Department Assets</span>
        </p>
    </div>
    <div class="button-row">
        <button type="button" class="btn btn-primary" data-modal-open="createAssetModal">
            <i class="fas fa-plus"></i> Add New Asset
        </button>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Department Asset Inventory</h2>
            <p>Select an asset to view its full movement history.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="app-table" data-datatable="true" id="departmentAssetsTable">
            <thead>
                <tr>
                    <th>Asset Code</th>
                    <th>Asset Name</th>
                    <th>Category</th>
                    <th>Latest Location</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr data-asset-id="{{ $asset->id }}"
                    data-asset-code="{{ $asset->asset_code }}"
                    data-name="{{ $asset->name }}"
                    data-serial-number="{{ $asset->serial_number }}"
                    data-category-id="{{ $asset->asset_category_id }}"
                    data-purchase-date="{{ $asset->purchase_date?->format('Y-m-d') }}"
                    data-status="{{ $asset->status }}"
                    data-description="{{ $asset->description }}"
                    data-image-preview="{{ $asset->image ? Storage::url($asset->image) : '' }}">
                    <td><code class="text-primary font-weight-bold">{{ $asset->asset_code }}</code></td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category->name ?? '—' }}</td>
                    <td>
                        @if($asset->latestLocation)
                            <span class="badge badge-light p-2">
                                <i class="fas fa-map-marker-alt text-danger"></i> 
                                {{ $asset->latestLocation->location }}
                            </span>
                        @else
                            <span class="text-muted italic">No logs recorded</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $asset->status === 'active' ? 'badge-success' : ($asset->status === 'inactive' ? 'badge-warning' : 'badge-soft') }}">
                            {{ ucfirst($asset->status) }}
                        </span>
                    </td>
                    <td>{{ $asset->updated_at->diffForHumans() }}</td>
                    <td class="inline-actions">
                        <div class="btn-group">
                            <a href="{{ route('manager.tracking.asset-history', $asset->id) }}" 
                               class="btn btn-sm btn-outline-info" title="View Full History">
                                <i class="fas fa-history"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary view-asset-btn" 
                                    data-asset-id="{{ $asset->id }}" title="Quick Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary edit-asset-btn" 
                                    data-asset-id="{{ $asset->id }}" title="Edit Asset">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="{{ route('manager.assets.destroy', $asset) }}" 
                                  class="d-inline js-confirm-delete" 
                                  data-title="Delete asset" 
                                  data-text="Asset {{ $asset->asset_code }} will be permanently deleted.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No assets found. <a href="#" data-modal-open="createAssetModal">Create one now</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- CREATE ASSET MODAL --}}
<div id="createAssetModal" class="app-modal">
    <div class="modal-panel modal-wide">
        <form method="POST" action="{{ route('manager.assets.store') }}" enctype="multipart/form-data" id="createAssetForm">
            @csrf
            <div class="modal-header">
                <div>
                    <h2>Add New Asset</h2>
                    <p>Register a new asset to your department inventory.</p>
                </div>
                <button type="button" class="icon-button" data-modal-close>✕</button>
            </div>
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Asset Code <span class="text-danger">*</span></label>
                        <input type="text" name="asset_code" id="create_asset_code" required>
                    </div>
                    <div class="form-group">
                        <label>Asset Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="create_name" required>
                    </div>
                    <div class="form-group">
                        <label>Category <span class="text-danger">*</span></label>
                        <select name="asset_category_id" id="create_asset_category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Serial Number</label>
                        <input type="text" name="serial_number" id="create_serial_number">
                    </div>
                    <div class="form-group">
                        <label>Purchase Date</label>
                        <input type="date" name="purchase_date" id="create_purchase_date">
                    </div>
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" id="create_status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="missing">Missing</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Asset Image</label>
                        <input type="file" name="image" id="create_image" accept="image/*">
                    </div>
                    <div class="form-group field-span-2">
                        <label>Description</label>
                        <textarea name="description" id="create_description" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary">Save Asset</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT ASSET MODAL --}}
<div id="editAssetModal" class="app-modal">
    <div class="modal-panel modal-wide">
        <form method="POST" enctype="multipart/form-data" id="editAssetForm">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <div>
                    <h2>Edit Asset</h2>
                    <p>Update asset details.</p>
                </div>
                <button type="button" class="icon-button" data-modal-close>✕</button>
            </div>
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Asset Code <span class="text-danger">*</span></label>
                        <input type="text" name="asset_code" id="edit_asset_code" required>
                    </div>
                    <div class="form-group">
                        <label>Asset Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" required>
                    </div>
                    <div class="form-group">
                        <label>Category <span class="text-danger">*</span></label>
                        <select name="asset_category_id" id="edit_asset_category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Serial Number</label>
                        <input type="text" name="serial_number" id="edit_serial_number">
                    </div>
                    <div class="form-group">
                        <label>Purchase Date</label>
                        <input type="date" name="purchase_date" id="edit_purchase_date">
                    </div>
                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="status" id="edit_status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="missing">Missing</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Asset Image</label>
                        <input type="file" name="image" id="edit_image" accept="image/*">
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
                <button type="submit" class="btn btn-primary">Update Asset</button>
            </div>
        </form>
    </div>
</div>

{{-- QUICK VIEW MODAL --}}
<div id="viewAssetModal" class="app-modal">
    <div class="modal-panel">
        <div class="modal-header">
            <div>
                <h2 id="view_asset_name">Asset Details</h2>
                <p id="view_asset_code" class="text-muted"></p>
            </div>
            <button type="button" class="icon-button" data-modal-close>✕</button>
        </div>
        <div class="modal-body">
            <div id="view_details_content">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-modal-close>Close</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable
        const table = document.getElementById('departmentAssetsTable');
        if (table && !table.dataset.datatableInitialized) {
            new DataTable(table, {
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50],
                language: {
                    search: "Search Assets:",
                    emptyTable: "No data available in table",
                    zeroRecords: "No matching records found"
                },
                order: [[5, 'desc']], // Sort by Last Updated
                columnDefs: [
                    { orderable: false, targets: 6 }
                ]
            });
            table.dataset.datatableInitialized = 'true';
        }

        // Edit asset button handler
        document.querySelectorAll('.edit-asset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const assetId = row.dataset.assetId;
                const form = document.getElementById('editAssetForm');
                form.action = '/manager/assets/' + assetId;

                document.getElementById('edit_asset_code').value = row.dataset.assetCode || '';
                document.getElementById('edit_name').value = row.dataset.name || '';
                document.getElementById('edit_serial_number').value = row.dataset.serialNumber || '';
                document.getElementById('edit_asset_category_id').value = row.dataset.categoryId || '';
                document.getElementById('edit_purchase_date').value = row.dataset.purchaseDate || '';
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

        // View asset details button handler
        document.querySelectorAll('.view-asset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const assetId = row.dataset.assetId;
                const modal = document.getElementById('viewAssetModal');
                const contentDiv = document.getElementById('view_details_content');

                // Set header
                document.getElementById('view_asset_name').textContent = row.dataset.name;
                document.getElementById('view_asset_code').textContent = 'Code: ' + row.dataset.assetCode;

                // Fetch asset details via AJAX
                fetch('/manager/assets/' + assetId + '/details')
                    .then(response => response.json())
                    .then(data => {
                        let html = `
                            <div class="details-grid">
                                <div class="detail-item">
                                    <label>Serial Number</label>
                                    <p>${data.asset.serial_number || 'N/A'}</p>
                                </div>
                                <div class="detail-item">
                                    <label>Category</label>
                                    <p>${data.asset.category?.name || 'N/A'}</p>
                                </div>
                                <div class="detail-item">
                                    <label>Status</label>
                                    <p><span class="badge badge-${data.asset.status === 'active' ? 'success' : 'warning'}">${data.asset.status}</span></p>
                                </div>
                                <div class="detail-item">
                                    <label>Purchase Date</label>
                                    <p>${data.asset.purchase_date || 'N/A'}</p>
                                </div>
                            </div>
                        `;

                        if (data.latest_location) {
                            html += `
                                <div class="detail-section mt-3">
                                    <h4>Latest Location</h4>
                                    <p>${data.latest_location.location}</p>
                                    <small class="text-muted">${new Date(data.latest_location.created_at).toLocaleString()}</small>
                                </div>
                            `;
                        }

                        if (data.asset.description) {
                            html += `
                                <div class="detail-section mt-3">
                                    <h4>Description</h4>
                                    <p>${data.asset.description}</p>
                                </div>
                            `;
                        }

                        contentDiv.innerHTML = html;
                        modal.classList.add('is-open');
                        document.body.classList.add('modal-open');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        contentDiv.innerHTML = '<p class="text-danger">Failed to load asset details.</p>';
                    });
            });
        });
    });
</script>
@endpush
