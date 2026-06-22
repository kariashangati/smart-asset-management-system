<?php

namespace App\Listeners;

use App\Events\AssetLocationUpdated;
use App\Models\TrackerDevice;
use App\Services\AlertRuleEngine;
use App\Services\GeofenceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckGeofenceBreach implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        protected GeofenceService $geofenceService,
        protected AlertRuleEngine $alertRuleEngine
    ) {
    }

    public function handle(AssetLocationUpdated $event): void
    {
        $trackerDevice = TrackerDevice::find($event->trackerDeviceId);

        if (!$trackerDevice) {
            return;
        }

        $this->geofenceService->checkAndCreateAlerts(
            asset: $event->asset,
            latitude: $event->latitude,
            longitude: $event->longitude,
            trackerDevice: $trackerDevice,
            speed: $event->speed,
            motionDetected: $event->motionDetected
        );

        $this->alertRuleEngine->evaluateRulesForAsset(
            $event->asset->id,
            [
                'speed' => $event->speed,
                'motion_detected' => $event->motionDetected,
                'latitude' => $event->latitude,
                'longitude' => $event->longitude,
            ]
        );
    }
}
