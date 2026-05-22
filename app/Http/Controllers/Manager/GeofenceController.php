<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGeofenceRequest;
use App\Http\Requests\UpdateGeofenceRequest;
use App\Models\Asset;
use App\Models\Geofence;
use App\Services\GeofenceService;
use Illuminate\Http\Request;

class GeofenceController extends Controller
{
    protected $geofenceService;

    public function __construct(GeofenceService $geofenceService)
    {
        $this->geofenceService = $geofenceService;
    }

    public function index()
    {
        $geofences = Geofence::with(['asset', 'createdByUser'])->get();
        $assets = Asset::all();
        return view('manager.geofences.index', compact('geofences', 'assets'));
    }

    public function store(StoreGeofenceRequest $request)
    {
        $this->geofenceService->createGeofence($request->validated());
        return redirect()->route('manager.geofences.index')->with('success', 'Geofence created successfully.');
    }

    public function update(UpdateGeofenceRequest $request, Geofence $geofence)
    {
        $this->geofenceService->updateGeofence($geofence, $request->validated());
        return redirect()->route('manager.geofences.index')->with('success', 'Geofence updated successfully.');
    }

    public function destroy(Geofence $geofence)
    {
        $this->geofenceService->deleteGeofence($geofence);
        return redirect()->route('manager.geofences.index')->with('success', 'Geofence deleted.');
    }
}