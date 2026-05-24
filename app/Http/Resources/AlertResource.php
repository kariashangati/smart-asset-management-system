<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlertResource extends JsonResource
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
            'asset' => [
                'id' => $this->asset?->id,
                'name' => $this->asset?->name,
            ],
            'tracker_device' => [
                'id' => $this->trackerDevice?->id,
                'name' => $this->trackerDevice?->name,
            ],
            'alert_type' => $this->alert_type,
            'severity' => $this->severity,
            'title' => $this->title,
            'message' => $this->message,
            'status' => $this->status,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'triggered_at' => $this->triggered_at,
            'resolved_at' => $this->resolved_at,
            'resolution_notes' => $this->resolution_notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
