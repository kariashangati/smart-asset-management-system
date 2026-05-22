<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetLatestLocation;
use App\Models\LocationLog;
use App\Models\TrackerDevice;

class LocationService
{
    public function storeLocationLog(array $data): LocationLog
    {
        $log = LocationLog::create($data);

        // Update latest location for the asset (if asset_id is provided)
        if ($data['asset_id'] ?? false) {
            $this->updateLatestLocation($data['asset_id'], $data['tracker_device_id'], $data);
        }

        return $log;
    }

    private function updateLatestLocation($assetId, $trackerDeviceId, array $data)
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

    public function getLatestLocationForAsset(Asset $asset)
    {
        return AssetLatestLocation::where('asset_id', $asset->id)->first();
    }

    public function getLocationHistory($assetId, $limit = 100)
    {
        return LocationLog::where('asset_id', $assetId)
            ->orderBy('recorded_at', 'desc')
            ->limit($limit)
            ->get();
    }
}