<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Geofence;
use App\Services\MapService;
use Illuminate\View\View;
use Illuminate\Http\Request;

class MapController extends Controller
{
    protected MapService $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    /**
     * Display main map page
     * GET /map
     */
    public function index(Request $request): View
    {
        $filters = $request->only('department_id', 'status', 'asset_type');
        $assets = $this->mapService->getAllAssetsForMap($filters);
        $geofences = $this->mapService->getGeofencesForMap();

        return view('map.index', [
            'assets' => $assets,
            'geofences' => $geofences,
            'maps_api_key' => config('integrations.google_maps_api_key'),
            'departments' => auth()->user()->department ? [auth()->user()->department] : [],
        ]);
    }

    /**
     * Display map with specific asset
     * GET /map/asset/{id}
     */
    public function showAsset(Asset $asset): View
    {
        $this->authorize('view', $asset);

        $assetData = $this->mapService->getAssetForMap($asset);
        $trail = $this->mapService->getAssetLocationTrail($asset);
        $geofences = $this->mapService->getGeofencesForMap();

        return view('map.asset-details', [
            'asset' => $asset,
            'assetData' => $assetData,
            'trail' => $trail,
            'geofences' => $geofences,
            'maps_api_key' => config('integrations.google_maps_api_key'),
        ]);
    }

    /**
     * Display geofence details on map
     * GET /map/geofence/{id}
     */
    public function showGeofence(Geofence $geofence): View
    {
        $violations = $this->mapService->getGeofenceViolations($geofence);

        return view('map.geofence-info', [
            'geofence' => $geofence,
            'violations' => $violations,
            'maps_api_key' => config('integrations.google_maps_api_key'),
        ]);
    }
}
