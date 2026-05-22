<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Asset;
use App\Models\TrackerDevice;
use Illuminate\Support\Facades\Auth;

class AlertService
{
    public function createAlert(array $data): Alert
    {
        $data['triggered_at'] = $data['triggered_at'] ?? now();
        $data['status'] = 'unread';
        return Alert::create($data);
    }

    public function markAsRead(Alert $alert): void
    {
        if ($alert->status === 'unread') {
            $alert->update([
                'status' => 'read',
                'read_by' => Auth::id(),
                'read_at' => now(),
            ]);
        }
    }

    public function markAsResolved(Alert $alert): void
    {
        $alert->update(['status' => 'resolved']);
    }

    public function getUnreadCount(): int
    {
        return Alert::where('status', 'unread')->count();
    }

    public function getRecentAlerts($limit = 5)
    {
        return Alert::with(['asset', 'trackerDevice'])
            ->orderBy('triggered_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function generateGeofenceAlert(Asset $asset, $latitude, $longitude)
    {
        $activeGeofence = $asset->geofences()->where('status', 'active')->first();
        if ($activeGeofence) {
            $geofenceService = new GeofenceService();
            $inside = $geofenceService->isPointInsideGeofence($activeGeofence, $latitude, $longitude);
            if (!$inside) {
                $this->createAlert([
                    'asset_id' => $asset->id,
                    'tracker_device_id' => $asset->activeAssignment->tracker_device_id ?? null,
                    'alert_type' => 'outside_geofence',
                    'severity' => 'high',
                    'title' => 'Asset outside geofence',
                    'message' => "Asset {$asset->name} left its designated perimeter at {$latitude}, {$longitude}",
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);
            }
        }
    }
}