@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Create New Geofence</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.geofences.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="asset_id">Asset</label>
                <select name="asset_id" id="asset_id" class="form-control @error('asset_id') is-invalid @enderror" required>
                    <option value="">Select Asset</option>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>{{ $asset->asset_code }} - {{ $asset->name }}</option>
                    @endforeach
                </select>
                @error('asset_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="name">Geofence Name</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="center_latitude">Center Latitude</label>
                    <input type="number" step="any" name="center_latitude" id="center_latitude" class="form-control @error('center_latitude') is-invalid @enderror" value="{{ old('center_latitude') }}" required>
                    @error('center_latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="center_longitude">Center Longitude</label>
                    <input type="number" step="any" name="center_longitude" id="center_longitude" class="form-control @error('center_longitude') is-invalid @enderror" value="{{ old('center_longitude') }}" required>
                    @error('center_longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="radius_meters">Radius (meters)</label>
                <input type="number" name="radius_meters" id="radius_meters" class="form-control @error('radius_meters') is-invalid @enderror" value="{{ old('radius_meters', 100) }}" required>
                @error('radius_meters')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Create Geofence</button>
            <a href="{{ route('admin.geofences.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection