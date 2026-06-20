<?php

namespace App\Events;

use App\Models\Asset;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssetLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Asset $asset;
    public int $trackerDeviceId;
    public float $latitude;
    public float $longitude;
    public float $speed;
    public bool $motionDetected;

    /**
     * Create a new event instance.
     *
     * FIX (audit #3/#5): added trackerDeviceId as an explicit, required
     * property. Previously listeners pulled this from $asset->trackerDevice,
     * a relation that is null for assets assigned through the real admin UI
     * (which uses the AssetDeviceAssignment pivot, not a direct FK).
     */
    public function __construct(
        Asset $asset,
        int $trackerDeviceId,
        float $latitude,
        float $longitude,
        float $speed = 0,
        bool $motionDetected = false
    ) {
        $this->asset = $asset;
        $this->trackerDeviceId = $trackerDeviceId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->speed = $speed;
        $this->motionDetected = $motionDetected;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('asset.' . $this->asset->id),
            new PrivateChannel('department.' . $this->asset->department_id),
            new PrivateChannel('locations'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'asset_id' => $this->asset->id,
            'asset_name' => $this->asset->name,
            'tracker_device_id' => $this->trackerDeviceId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'speed' => $this->speed,
            'motion_detected' => $this->motionDetected,
            'updated_at' => now(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'asset.location_updated';
    }
}
