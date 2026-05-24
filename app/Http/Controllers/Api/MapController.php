<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;

class MapController extends Controller
{
    /**
     * Get assets for map display
     * GET /api/map/assets
     */
    public function getAssetsForMap(): JsonResponse
    {
        $assets = Asset::with('latestLocation', 'department')
            ->whereHas('latestLocation')
            ->get()
            ->map(function (Asset $asset) {
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'type' => $asset->asset_type,
                    'status' => $asset->status,
                    'latitude' => $asset->latestLocation->latitude,
                    'longitude' => $asset->latestLocation->longitude,
                    'last_updated' => $asset->latestLocation->last_recorded_at,
                    'department' => $asset->department->name,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $assets,
            'maps_api_key' => config('integrations.google_maps_api_key'),
        ]);
    }

    /**
     * Get single asset location for map
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

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $asset->id,
                'name' => $asset->name,
                'latitude' => $asset->latestLocation->latitude,
                'longitude' => $asset->latestLocation->longitude,
                'speed' => $asset->latestLocation->last_motion_detected ? 'Moving' : 'Stationary',
                'last_updated' => $asset->latestLocation->last_recorded_at,
            ],
            'maps_api_key' => config('integrations.google_maps_api_key'),
        ]);
    }
}
