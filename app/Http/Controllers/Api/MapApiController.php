<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Geofence;
use App\Services\MapService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapApiController extends Controller
{
    protected MapService $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    /**
     * Get all assets with locations for map
     * GET /api/map/assets
     */
    public function getAssetsForMap(Request $request): JsonResponse
    {
        $filters = $request->only('department_id', 'status', 'asset_type');
        $assets = $this->mapService->getAllAssetsForMap($filters);

        return response()->json([
            'success' => true,
            'message' => 'Assets retrieved for map',
            'data' => $assets,
            'count' => $assets->count(),
            'maps_api_key' => config('integrations.google_maps_api_key'),
        ]);
    }

    /**
     * Get single asset location
     * GET /api/map/assets/{id}
     */
    public function getAssetLocation(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        if (!$asset->latestLocation) {
            return response()->json([
                'success' => false,
                'message' => 'No location data available',
            ], 404);
        }

        $assetData = $this->mapService->getAssetForMap($asset);

        return response()->json([
            'success' => true,
            'data' => $assetData,
            'maps_api_key' => config('integrations.google_maps_api_key'),
        ]);
    }

    /**
     * Get asset location trail/history
     * GET /api/map/assets/{id}/location-trail
     */
    public function getAssetLocationTrail(Request $request, Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        $limit = $request->input('limit', 100);
        $trail = $this->mapService->getAssetLocationTrail($asset, $limit);

        return response()->json([
            'success' => true,
            'data' => $trail,
        ]);
    }

    /**
     * Get all geofences for map
     * GET /api/map/geofences
     */
    public function getGeofencesForMap(Request $request): JsonResponse
    {
        $filters = $request->only('status');
        $geofences = $this->mapService->getGeofencesForMap($filters);

        return response()->json([
            'success' => true,
            'message' => 'Geofences retrieved for map',
            'data' => $geofences,
            'count' => $geofences->count(),
        ]);
    }

    /**
     * Get geofence violations
     * GET /api/map/geofences/{id}/violations
     */
    public function getGeofenceViolations(Geofence $geofence): JsonResponse
    {
        $violations = $this->mapService->getGeofenceViolations($geofence);

        return response()->json([
            'success' => true,
            'data' => $violations,
        ]);
    }

    /**
     * Get map configuration
     * GET /api/map/config
     */
    public function getMapConfig(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'maps_api_key' => config('integrations.google_maps_api_key'),
                'default_center' => [
                    'latitude' => env('MAP_DEFAULT_LATITUDE', 0),
                    'longitude' => env('MAP_DEFAULT_LONGITUDE', 0),
                ],
                'default_zoom' => env('MAP_DEFAULT_ZOOM', 12),
                'map_type' => env('MAP_TYPE', 'roadmap'),
            ],
        ]);
    }
}
