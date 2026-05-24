<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
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
            'description' => $this->description,
            'asset_type' => $this->asset_type,
            'serial_number' => $this->serial_number,
            'status' => $this->status,
            'department' => [
                'id' => $this->department?->id,
                'name' => $this->department?->name,
            ],
            'tracker_device' => [
                'id' => $this->trackerDevice?->id,
                'name' => $this->trackerDevice?->name,
            ],
            'latest_location' => [
                'latitude' => $this->latestLocation?->latitude,
                'longitude' => $this->latestLocation?->longitude,
                'last_recorded_at' => $this->latestLocation?->last_recorded_at,
            ],
            'purchase_date' => $this->purchase_date,
            'location' => $this->location,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
