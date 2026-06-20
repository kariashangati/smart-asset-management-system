<?php

namespace App\Jobs;

use App\Events\AssetLocationUpdated;
use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAssetLocationUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $assetId;
    protected int $trackerDeviceId;
    protected float $latitude;
    protected float $longitude;
    protected float $speed;
    protected bool $motionDetected;

    /**
     * Create a new job instance.
     *
     * FIX (audit #3/#5): trackerDeviceId is now accepted explicitly instead of
     * being re-derived later from $asset->trackerDevice (a relation that's
     * empty for assets assigned through the real admin UI). The webhook
     * already knows which device sent the ping — we just pass it through.
     */
    public function __construct(
        int $assetId,
        int $trackerDeviceId,
        float $latitude,
        float $longitude,
        float $speed = 0,
        bool $motionDetected = false
    ) {
        $this->assetId = $assetId;
        $this->trackerDeviceId = $trackerDeviceId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->speed = $speed;
        $this->motionDetected = $motionDetected;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $asset = Asset::findOrFail($this->assetId);

        // Dispatch event to trigger listeners (logging, geofence check, etc.)
        AssetLocationUpdated::dispatch(
            asset: $asset,
            trackerDeviceId: $this->trackerDeviceId,
            latitude: $this->latitude,
            longitude: $this->longitude,
            speed: $this->speed,
            motionDetected: $this->motionDetected
        );
    }
}
