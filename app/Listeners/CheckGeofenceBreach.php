<?php

namespace App\Listeners;

use App\Events\AssetLocationUpdated;
use App\Services\GeofenceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckGeofenceBreach implements ShouldQueue
{
    use InteractsWithQueue;

    protected GeofenceService $geofenceService;

    /**
     * Create the event listener.
     */
    public function __construct(GeofenceService $geofenceService)
    {
        $this->geofenceService = $geofenceService;
    }

    /**
     * Handle the event.
     */
    public function handle(AssetLocationUpdated $event): void
    {
        // Check geofences and create alerts
        $this->geofenceService->checkAndCreateAlerts(
            asset: $event->asset,
            latitude: $event->latitude,
            longitude: $event->longitude,
            trackerDevice: $event->asset->trackerDevice,
            speed: $event->speed,
            motionDetected: $event->motionDetected
        );
    }
}
