@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit Asset: {{ $asset->name }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="asset_code">Asset Code</label>
                    <input type="text" name="asset_code" id="asset_code" class="form-control @error('asset_code') is-invalid @enderror" value="{{ old('asset_code', $asset->asset_code) }}" required>
                    @error('asset_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="name">Asset Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $asset->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="serial_number">Serial Number</label>
                    <input type="text" name="serial_number" id="serial_number" class="form-control @error('serial_number') is-invalid @enderror" value="{{ old('serial_number', $asset->serial_number) }}">
                    @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="asset_category_id">Category</label>
                    <select name="asset_category_id" id="asset_category_id" class="form-control @error('asset_category_id') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('asset_category_id', $asset->asset_category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('asset_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="department_id">Department</label>
                    <select name="department_id" id="department_id" class="form-control @error('department_id') is-invalid @enderror" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $asset->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="purchase_date">Purchase Date</label>
                    <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}">
                    @error('purchase_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $asset->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $asset->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="missing" {{ old('status', $asset->status) == 'missing' ? 'selected' : '' }}>Missing</option>
                        <option value="maintenance" {{ old('status', $asset->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="image">Asset Image</label>
                    @if($asset->image)
                        <div class="mb-2">
                            <img src="{{ Storage::url($asset->image) }}" width="100" height="100" style="object-fit: cover;">
                        </div>
                    @endif
                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 mb-3">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $asset->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Asset</button>
            <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection