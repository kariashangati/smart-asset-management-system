<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssignmentRequest;
use App\Models\Asset;
use App\Models\AssetDeviceAssignment;
use App\Models\TrackerDevice;
use Illuminate\Http\Request;

class DeviceAssignmentController extends Controller
{
    public function index()
    {
        $assignments = AssetDeviceAssignment::with(['asset', 'trackerDevice', 'assignedByUser'])
            ->latest()
            ->get();

        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $assets = Asset::whereDoesntHave('activeAssignment')->get();
        $devices = TrackerDevice::where('status', 'active')
            ->whereDoesntHave('activeAssignment')
            ->get();

        return view('admin.assignments.create', compact('assets', 'devices'));
    }

    public function store(StoreAssignmentRequest $request)
    {
        $data = $request->validated();
        $data['assigned_by'] = auth()->id();
        $data['assigned_at'] = $data['assigned_at'] ?? now();

        AssetDeviceAssignment::create($data);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Device assigned to asset successfully.');
    }

    public function destroy(AssetDeviceAssignment $assignment)
    {
        $assignment->update([
            'unassigned_at' => now(),
            'is_active' => false,
        ]);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Assignment ended successfully.');
    }
}