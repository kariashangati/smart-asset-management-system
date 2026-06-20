<?php

namespace App\Listeners;

use App\Events\AssetLocationUpdated;
use App\Services\LocationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogAssetLocation implements ShouldQueue
{
    use InteractsWithQueue;

    protected LocationService $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Handle the event.
     *
     * FIX (audit #5/#6): previously this called
     *   LocationLog::create([..., 'tracker_device_id' => $event->asset->trackerDevice->id, ...])
     * which (a) threw on assets with no direct tracker_device_id FK set (the
     * normal case in this app), and (b) wrote only to location_logs, never
     * touching asset_latest_locations — which is what the live map, asset
     * show page, and dashboard widgets actually read from.
     *
     * Routing through LocationService::storeLocationLog() does both: writes
     * the LocationLog row AND upserts AssetLatestLocation in one place, using
     * the trackerDeviceId carried explicitly on the event instead of a
     * relation that's empty for pivot-assigned devices.
     */
    public function handle(AssetLocationUpdated $event): void
    {
        $this->locationService->storeLocationLog([
            'tracker_device_id' => $event->trackerDeviceId,
            'asset_id' => $event->asset->id,
            'latitude' => $event->latitude,
            'longitude' => $event->longitude,
            'speed' => $event->speed,
            'motion_detected' => $event->motionDetected,
            'recorded_at' => now(),
        ]);
    }
}
