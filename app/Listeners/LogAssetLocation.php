<?php

namespace App\Listeners;

use App\Events\AssetLocationUpdated;
use App\Models\LocationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogAssetLocation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(AssetLocationUpdated $event): void
    {
        // Log the location
        LocationLog::create([
            'asset_id' => $event->asset->id,
            'tracker_device_id' => $event->asset->trackerDevice->id,
            'latitude' => $event->latitude,
            'longitude' => $event->longitude,
            'speed' => $event->speed,
            'motion_detected' => $event->motionDetected,
            'recorded_at' => now(),
        ]);
    }
}
