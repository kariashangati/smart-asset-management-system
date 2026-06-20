<?php

namespace App\Listeners;

use App\Events\AssetLocationUpdated;
use App\Services\GeofenceService;
use App\Services\AlertRuleEngine;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CheckGeofenceBreach implements ShouldQueue
{
    use InteractsWithQueue;

    protected GeofenceService $geofenceService;
    protected AlertRuleEngine $alertRuleEngine;

    /**
     * Create the event listener.
     */
    public function __construct(GeofenceService $geofenceService, AlertRuleEngine $alertRuleEngine)
    {
        $this->geofenceService = $geofenceService;
        $this->alertRuleEngine = $alertRuleEngine;
    }

    /**
     * Handle the event.
     *
     * FIX (audit #3): $event->asset->trackerDevice used to resolve to null for
     * any asset assigned via the real admin UI (pivot-based). The event now
     * carries trackerDeviceId explicitly, so we look the model up directly
     * instead of trusting a relation that's empty in the common case.
     *
     * FIX (bonus, found while patching this file): this previously called
     * $this->alertRuleEngine->processRules(...), a method that does not exist
     * on AlertRuleEngine — that class only defines evaluateRulesForAsset().
     * Calling this listener would have thrown a fatal "Call to undefined
     * method" error on every single location update, which (since this
     * listener implements ShouldQueue) would have silently failed in the
     * queue and landed in failed_jobs rather than surfacing to anyone.
     */
    public function handle(AssetLocationUpdated $event): void
    {
        $trackerDevice = \App\Models\TrackerDevice::find($event->trackerDeviceId);

        if ($trackerDevice) {
            // Check geofences and create alerts
            $this->geofenceService->checkAndCreateAlerts(
                asset: $event->asset,
                latitude: $event->latitude,
                longitude: $event->longitude,
                trackerDevice: $trackerDevice,
                speed: $event->speed,
                motionDetected: $event->motionDetected
            );
        }

        // Process custom alert rules (method name corrected to match AlertRuleEngine)
        $this->alertRuleEngine->evaluateRulesForAsset($event->asset->id, [
            'speed' => $event->speed,
            'motion_detected' => $event->motionDetected,
            'latitude' => $event->latitude,
            'longitude' => $event->longitude,
        ]);
    }
}
