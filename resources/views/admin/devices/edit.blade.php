@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>Edit Device: {{ $trackerDevice->device_name }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.devices.update', $trackerDevice) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="device_code">Device Code</label>
                    <input type="text" name="device_code" id="device_code" class="form-control @error('device_code') is-invalid @enderror" value="{{ old('device_code', $trackerDevice->device_code) }}" required>
                    @error('device_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="device_name">Device Name</label>
                    <input type="text" name="device_name" id="device_name" class="form-control @error('device_name') is-invalid @enderror" value="{{ old('device_name', $trackerDevice->device_name) }}" required>
                    @error('device_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="imei">IMEI</label>
                    <input type="text" name="imei" id="imei" class="form-control @error('imei') is-invalid @enderror" value="{{ old('imei', $trackerDevice->imei) }}">
                    @error('imei')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="sim_number">SIM Number</label>
                    <input type="text" name="sim_number" id="sim_number" class="form-control @error('sim_number') is-invalid @enderror" value="{{ old('sim_number', $trackerDevice->sim_number) }}">
                    @error('sim_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $trackerDevice->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $trackerDevice->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="faulty" {{ old('status', $trackerDevice->status) == 'faulty' ? 'selected' : '' }}>Faulty</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="battery_level">Battery Level (%)</label>
                    <input type="number" name="battery_level" id="battery_level" class="form-control @error('battery_level') is-invalid @enderror" value="{{ old('battery_level', $trackerDevice->battery_level) }}" min="0" max="100">
                    @error('battery_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="firmware_version">Firmware Version</label>
                    <input type="text" name="firmware_version" id="firmware_version" class="form-control @error('firmware_version') is-invalid @enderror" value="{{ old('firmware_version', $trackerDevice->firmware_version) }}">
                    @error('firmware_version')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Device</button>
            <a href="{{ route('admin.devices.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection