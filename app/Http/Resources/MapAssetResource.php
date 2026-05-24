<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapAssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->asset_type,
            'status' => $this->status,
            'serial_number' => $this->serial_number,
            'department' => $this->department?->name,
            'location' => [
                'latitude' => $this->latestLocation?->latitude,
                'longitude' => $this->latestLocation?->longitude,
                'last_updated' => $this->latestLocation?->created_at,
                'speed' => $this->latestLocation?->speed ?? 0,
                'motion_detected' => $this->latestLocation?->motion_detected ?? false,
            ],
            'geofences' => $this->geofences()->pluck('id'),
            'icon' => $this->getMapIcon(),
            'color' => $this->getStatusColor(),
        ];
    }

    /**
     * Get icon based on asset type
     */
    private function getMapIcon(): string
    {
        return match ($this->asset_type) {
            'vehicle' => '🚗',
            'equipment' => '⚙️',
            'device' => '📱',
            default => '📍',
        };
    }

    /**
     * Get color based on status
     */
    private function getStatusColor(): string
    {
        return match ($this->status) {
            'active' => '#10b981',
            'inactive' => '#6b7280',
            'maintenance' => '#f59e0b',
            'retired' => '#ef4444',
            default => '#3b82f6',
        };
    }
}
