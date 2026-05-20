<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGeofenceRequest;
use App\Http\Requests\UpdateGeofenceRequest;
use App\Models\Asset;
use App\Models\Geofence;
use Illuminate\Http\Request;

class GeofenceController extends Controller
{
    public function index()
    {
        $geofences = Geofence::with(['asset', 'createdByUser'])->get();
        return view('admin.geofences.index', compact('geofences'));
    }

    public function create()
    {
        $assets = Asset::all();
        return view('admin.geofences.create', compact('assets'));
    }

    public function store(StoreGeofenceRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        Geofence::create($data);

        return redirect()->route('admin.geofences.index')
            ->with('success', 'Geofence created successfully.');
    }

    public function edit(Geofence $geofence)
    {
        $assets = Asset::all();
        return view('admin.geofences.edit', compact('geofence', 'assets'));
    }

    public function update(UpdateGeofenceRequest $request, Geofence $geofence)
    {
        $geofence->update($request->validated());

        return redirect()->route('admin.geofences.index')
            ->with('success', 'Geofence updated successfully.');
    }

    public function destroy(Geofence $geofence)
    {
        $geofence->delete();

        return redirect()->route('admin.geofences.index')
            ->with('success', 'Geofence deleted successfully.');
    }
}