<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\LocationLog;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Get current location for an asset
     * GET /api/assets/{asset_id}/location
     */
    public function getCurrentLocation(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        $location = $this->locationService->getLatestLocationForAsset($asset);

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'No location data available for this asset',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'asset_id' => $asset->id,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'last_recorded_at' => $location->last_recorded_at,
                'last_motion_detected' => $location->last_motion_detected,
            ],
        ]);
    }

    /**
     * Get location history for an asset
     * GET /api/assets/{asset_id}/location-history
     */
    public function getHistory(Request $request, Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        $request->validate([
            'limit' => 'nullable|integer|min:1|max:1000',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($request->has('per_page')) {
            $history = $this->locationService->getLocationHistoryPaginated(
                $asset->id,
                $request->per_page
            );

            return response()->json([
                'success' => true,
                'data' => $history->items(),
                'pagination' => [
                    'total' => $history->total(),
                    'per_page' => $history->perPage(),
                    'current_page' => $history->currentPage(),
                    'last_page' => $history->lastPage(),
                ],
            ]);
        }

        $limit = $request->limit ?? 100;
        $history = $this->locationService->getLocationHistory($asset->id, $limit);

        return response()->json([
            'success' => true,
            'data' => $history,
            'count' => $history->count(),
        ]);
    }

    /**
     * Get location statistics for an asset
     * GET /api/assets/{asset_id}/location-stats
     */
    public function getStatistics(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        $avgSpeed = $this->locationService->calculateAverageSpeed($asset->id);
        $totalDistance = $this->locationService->calculateTotalDistance($asset->id);

        return response()->json([
            'success' => true,
            'data' => [
                'asset_id' => $asset->id,
                'average_speed' => round($avgSpeed ?? 0, 2),
                'total_distance_km' => $totalDistance,
            ],
        ]);
    }

    /**
     * Store location log (from tracking device)
     * POST /api/location-logs
     */
    public function storeLocationLog(Request $request): JsonResponse
    {
        $request->validate([
            'tracker_device_id' => 'required|integer|exists:tracker_devices,id',
            'asset_id' => 'nullable|integer|exists:assets,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
            'motion_detected' => 'nullable|boolean',
            'recorded_at' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $locationLog = $this->locationService->storeLocationLog($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Location logged successfully',
            'data' => $locationLog,
        ], 201);
    }

    /**
     * Batch store location logs
     * POST /api/location-logs/batch
     */
    public function batchStoreLocationLogs(Request $request): JsonResponse
    {
        $request->validate([
            'locations' => 'required|array|min:1|max:100',
            'locations.*.tracker_device_id' => 'required|integer|exists:tracker_devices,id',
            'locations.*.asset_id' => 'nullable|integer|exists:assets,id',
            'locations.*.latitude' => 'required|numeric|between:-90,90',
            'locations.*.longitude' => 'required|numeric|between:-180,180',
            'locations.*.speed' => 'nullable|numeric|min:0',
            'locations.*.motion_detected' => 'nullable|boolean',
            'locations.*.recorded_at' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $stored = [];
        foreach ($request->locations as $location) {
            $stored[] = $this->locationService->storeLocationLog($location);
        }

        return response()->json([
            'success' => true,
            'message' => 'Locations logged successfully',
            'count' => count($stored),
            'data' => $stored,
        ], 201);
    }

    /**
     * Get location logs by date range
     * GET /api/assets/{asset_id}/location-range
     */
    public function getLocationByDateRange(Request $request, Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        $request->validate([
            'from' => 'required|date_format:Y-m-d H:i:s',
            'to' => 'required|date_format:Y-m-d H:i:s|after:from',
        ]);

        $logs = LocationLog::where('asset_id', $asset->id)
            ->whereBetween('recorded_at', [$request->from, $request->to])
            ->orderBy('recorded_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $logs,
            'count' => $logs->count(),
            'date_range' => [
                'from' => $request->from,
                'to' => $request->to,
            ],
        ]);
    }
}
