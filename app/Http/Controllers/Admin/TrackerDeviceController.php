<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTrackerDeviceRequest;
use App\Http\Requests\UpdateTrackerDeviceRequest;
use App\Models\TrackerDevice;
use Illuminate\Support\Str;

class TrackerDeviceController extends Controller
{
    public function index()
    {
        $devices = TrackerDevice::with('activeAssignment.asset.department')->get();
        return view('admin.devices.index', compact('devices'));
    }

    public function create()
    {
        return view('admin.devices.create');
    }

    public function store(StoreTrackerDeviceRequest $request)
    {
        $data = $request->validated();

        TrackerDevice::create($data);

        return redirect()->route('admin.devices.index')
            ->with('success', 'Tracker device created successfully.');
    }

    public function show(TrackerDevice $trackerDevice)
    {
        $trackerDevice->load(['assignments.asset', 'activeAssignment.asset']);
        return view('admin.devices.show', compact('trackerDevice'));
    }

    public function edit(TrackerDevice $trackerDevice)
    {
        return view('admin.devices.edit', compact('trackerDevice'));
    }

    public function update(UpdateTrackerDeviceRequest $request, TrackerDevice $trackerDevice)
    {
        $data = $request->validated();

        $trackerDevice->update($data);

        return redirect()->route('admin.devices.index')
            ->with('success', 'Tracker device updated successfully.');
    }

    public function destroy(TrackerDevice $trackerDevice)
    {
        $trackerDevice->delete();

        return redirect()->route('admin.devices.index')
            ->with('success', 'Tracker device deleted successfully.');
    }
}