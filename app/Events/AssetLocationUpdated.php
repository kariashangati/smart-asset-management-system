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
    public float $latitude;
    public float $longitude;
    public float $speed;
    public bool $motionDetected;

    /**
     * Create a new event instance.
     */
    public function __construct(Asset $asset, float $latitude, float $longitude, float $speed = 0, bool $motionDetected = false)
    {
        $this->asset = $asset;
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
