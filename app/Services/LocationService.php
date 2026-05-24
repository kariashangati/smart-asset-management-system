<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetLatestLocation;
use App\Models\LocationLog;
use Illuminate\Support\Facades\Log;

class LocationService
{
    /**
     * Store a location log from API data
     * Also updates latest location for the asset
     */
    public function storeLocationLog(array $data): LocationLog
    {
        $log = LocationLog::create([
            'tracker_device_id' => $data['tracker_device_id'],
            'asset_id' => $data['asset_id'] ?? null,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'speed' => $data['speed'] ?? 0,
            'motion_detected' => $data['motion_detected'] ?? false,
            'recorded_at' => $data['recorded_at'] ?? now(),
            'received_at' => now(),
        ]);

        // Update latest location for the asset (if asset_id is provided)
        if ($data['asset_id'] ?? false) {
            $this->updateLatestLocation($data['asset_id'], $data['tracker_device_id'], $data);
        }

        Log::debug('Location log stored', [
            'location_log_id' => $log->id,
            'asset_id' => $data['asset_id'] ?? null,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
        ]);

        return $log;
    }

    /**
     * Update or create the latest location record for an asset
     */
    private function updateLatestLocation(int $assetId, int $trackerDeviceId, array $data): void
    {
        AssetLatestLocation::updateOrCreate(
            ['asset_id' => $assetId],
            [
                'tracker_device_id' => $trackerDeviceId,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'last_motion_detected' => $data['motion_detected'] ?? false,
                'last_recorded_at' => $data['recorded_at'] ?? now(),
            ]
        );
    }

    /**
     * Get the latest location for an asset
     */
    public function getLatestLocationForAsset(Asset $asset): ?AssetLatestLocation
    {
        return AssetLatestLocation::where('asset_id', $asset->id)->first();
    }

    /**
     * Get location history for an asset
     */
    public function getLocationHistory(int $assetId, int $limit = 100)
    {
        return LocationLog::where('asset_id', $assetId)
            ->orderBy('recorded_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get location history with pagination
     */
    public function getLocationHistoryPaginated(int $assetId, int $perPage = 50)
    {
        return LocationLog::where('asset_id', $assetId)
            ->orderBy('recorded_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Calculate average speed from location logs
     */
    public function calculateAverageSpeed(int $assetId, ?int $limit = null)
    {
        $query = LocationLog::where('asset_id', $assetId)
            ->where('speed', '>', 0);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->avg('speed');
    }

    /**
     * Get total distance traveled (approximation)
     */
    public function calculateTotalDistance(int $assetId)
    {
        $logs = LocationLog::where('asset_id', $assetId)
            ->orderBy('recorded_at', 'asc')
            ->get(['latitude', 'longitude', 'recorded_at']);

        if ($logs->count() < 2) {
            return 0;
        }

        $totalDistance = 0;
        $prevLog = null;

        foreach ($logs as $log) {
            if ($prevLog) {
                $distance = $this->calculateDistance(
                    $prevLog->latitude,
                    $prevLog->longitude,
                    $log->latitude,
                    $log->longitude
                );
                $totalDistance += $distance;
            }
            $prevLog = $log;
        }

        return round($totalDistance / 1000, 2); // Convert to km
    }

    /**
     * Calculate distance between two coordinates (Haversine formula)
     * Returns distance in meters
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
