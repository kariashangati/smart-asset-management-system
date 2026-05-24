<?php

namespace App\Events;

use App\Models\Geofence;
use App\Models\Asset;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GeofenceBreachDetected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Asset $asset;
    public Geofence $geofence;
    public float $latitude;
    public float $longitude;
    public float $distanceOutside;

    /**
     * Create a new event instance.
     */
    public function __construct(Asset $asset, Geofence $geofence, float $latitude, float $longitude, float $distanceOutside)
    {
        $this->asset = $asset;
        $this->geofence = $geofence;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->distanceOutside = $distanceOutside;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('department.' . $this->asset->department_id),
            new PrivateChannel('geofence.' . $this->geofence->id),
            new PrivateChannel('breaches'),
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
            'geofence_id' => $this->geofence->id,
            'geofence_name' => $this->geofence->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance_outside' => round($this->distanceOutside, 2),
            'detected_at' => now(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'geofence.breach_detected';
    }
}
