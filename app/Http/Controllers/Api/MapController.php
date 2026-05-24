<?php

namespace App\Http\Controllers\Api;

use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Get assets for map view
     * GET /api/map/assets
     */
    public function getAssetsForMap(Request $request): JsonResponse
    {
        $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'nullable|string|in:active,inactive,maintenance,retired',
        ]);

        $query = Asset::with(['latestLocation', 'department'])
            ->whereNotNull('latestLocation');

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $assets = $query->get()->map(function ($asset) {
            return [
                'id' => $asset->id,
                'name' => $asset->name,
                'asset_type' => $asset->asset_type,
                'status' => $asset->status,
                'latitude' => $asset->latestLocation->latitude,
                'longitude' => $asset->latestLocation->longitude,
                'last_updated' => $asset->latestLocation->last_recorded_at,
                'department' => $asset->department?->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $assets,
            'map_config' => [
                'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
                'default_center' => [
                    'lat' => $assets->first()?->latitude ?? 40.7128,
                    'lng' => $assets->first()?->longitude ?? -74.0060,
                ],
                'default_zoom' => 12,
            ],
        ]);
    }

    /**
     * Get single asset location with history
     * GET /api/map/assets/{id}/track
     */
    public function getAssetTrack(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        $locations = $asset->locationLogs()
            ->orderBy('recorded_at', 'asc')
            ->limit(100)
            ->get()
            ->map(function ($log) {
                return [
                    'lat' => $log->latitude,
                    'lng' => $log->longitude,
                    'speed' => $log->speed,
                    'timestamp' => $log->recorded_at,
                ];
            });

        return response()->json([
            'success' => true,
            'asset' => [
                'id' => $asset->id,
                'name' => $asset->name,
                'type' => $asset->asset_type,
            ],
            'track' => $locations,
        ]);
    }

    /**
     * Get geofences for map
     * GET /api/map/geofences
     */
    public function getGeofencesForMap(): JsonResponse
    {
        $geofences = \App\Models\Geofence::where('status', 'active')->get()->map(function ($geofence) {
            return [
                'id' => $geofence->id,
                'name' => $geofence->name,
                'center' => [
                    'lat' => $geofence->center_latitude,
                    'lng' => $geofence->center_longitude,
                ],
                'radius' => $geofence->radius_meters,
                'alert_on_breach' => $geofence->alert_on_breach,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $geofences,
        ]);
    }
}
