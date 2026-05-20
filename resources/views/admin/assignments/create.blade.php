@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Assign Device to Asset</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.assignments.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="asset_id">Asset (only those without active assignment)</label>
                <select name="asset_id" id="asset_id" class="form-control @error('asset_id') is-invalid @enderror" required>
                    <option value="">Select Asset</option>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>{{ $asset->asset_code }} - {{ $asset->name }}</option>
                    @endforeach
                </select>
                @error('asset_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="tracker_device_id">Tracker Device (only active and unassigned)</label>
                <select name="tracker_device_id" id="tracker_device_id" class="form-control @error('tracker_device_id') is-invalid @enderror" required>
                    <option value="">Select Device</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}" {{ old('tracker_device_id') == $device->id ? 'selected' : '' }}>{{ $device->device_code }} - {{ $device->device_name }}</option>
                    @endforeach
                </select>
                @error('tracker_device_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="assigned_at">Assigned At (optional, defaults to now)</label>
                <input type="datetime-local" name="assigned_at" id="assigned_at" class="form-control @error('assigned_at') is-invalid @enderror" value="{{ old('assigned_at') }}">
                @error('assigned_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Assign</button>
            <a href="{{ route('admin.assignments.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection