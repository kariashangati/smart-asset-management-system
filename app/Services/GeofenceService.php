<?php

namespace App\Services;

use App\Models\Geofence;

class GeofenceService
{
    public function createGeofence(array $data): Geofence
    {
        $data['created_by'] = auth()->id();
        return Geofence::create($data);
    }

    public function updateGeofence(Geofence $geofence, array $data): Geofence
    {
        $geofence->update($data);
        return $geofence;
    }

    public function deleteGeofence(Geofence $geofence): void
    {
        $geofence->delete();
    }

    public function isPointInsideGeofence(Geofence $geofence, $lat, $lng): bool
    {
        $distance = $this->haversineDistance($geofence->center_latitude, $geofence->center_longitude, $lat, $lng);
        return $distance <= $geofence->radius_meters;
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
}