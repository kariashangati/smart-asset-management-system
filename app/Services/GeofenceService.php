<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Asset;
use App\Models\Geofence;
use App\Models\LocationLog;
use App\Models\TrackerDevice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GeofenceService
{
    /**
     * Earth's radius in meters
     */
    const EARTH_RADIUS_METERS = 6371000;

    /**
     * Check geofences and create alerts if violations detected
     * 
     * @param Asset $asset
     * @param float $latitude
     * @param float $longitude
     * @param TrackerDevice $trackerDevice
     * @param float $speed
     * @param bool $motionDetected
     */
    public function checkAndCreateAlerts(
        Asset $asset,
        float $latitude,
        float $longitude,
        TrackerDevice $trackerDevice,
        float $speed = 0,
        bool $motionDetected = false
    ): void {
        // Get active geofences for this asset
        $geofences = $asset->geofences()
            ->where('status', 'active')
            ->get();

        if ($geofences->isEmpty()) {
            return; // No geofences to check
        }

        foreach ($geofences as $geofence) {
            // Calculate distance from geofence center
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $geofence->center_latitude,
                $geofence->center_longitude
            );

            $isInsideGeofence = $distance <= $geofence->radius_meters;

            // Check if there's an existing unresolved breach alert for this geofence
            $existingAlert = Alert::where('asset_id', $asset->id)
                ->where('tracker_device_id', $trackerDevice->id)
                ->where('alert_type', 'geofence_breach')
                ->whereIn('status', ['unread', 'read'])
                ->orderBy('triggered_at', 'desc')
                ->first();

            if (!$isInsideGeofence) {
                // Asset is OUTSIDE geofence - this is a breach
                if (!$existingAlert) {
                    // Create new breach alert
                    $this->createGeofenceBreachAlert(
                        asset: $asset,
                        trackerDevice: $trackerDevice,
                        geofence: $geofence,
                        latitude: $latitude,
                        longitude: $longitude,
                        distance: $distance,
                        speed: $speed,
                        motionDetected: $motionDetected
                    );
                }
            } else {
                // Asset is INSIDE geofence
                if ($existingAlert && $existingAlert->status !== 'resolved') {
                    // Mark the breach alert as resolved
                    $existingAlert->update([
                        'status' => 'resolved',
                        'message' => "Asset returned to geofence: {$geofence->name}",
                    ]);
                }
            }
        }
    }

    /**
     * Create a geofence breach alert
     */
    protected function createGeofenceBreachAlert(
        Asset $asset,
        TrackerDevice $trackerDevice,
        Geofence $geofence,
        float $latitude,
        float $longitude,
        float $distance,
        float $speed,
        bool $motionDetected
    ): void {
        $distanceOutside = round($distance - $geofence->radius_meters, 2);

        // Determine severity based on how far outside geofence
        $severity = $this->determineSeverity($distanceOutside, $speed, $motionDetected);

        Alert::create([
            'asset_id' => $asset->id,
            'tracker_device_id' => $trackerDevice->id,
            'alert_type' => 'geofence_breach',
            'severity' => $severity,
            'title' => "Geofence Breach: {$asset->name}",
            'message' => "Asset '{$asset->name}' has left geofence '{$geofence->name}'. " .
                         "Distance outside: {$distanceOutside}m. " .
                         "Speed: {$speed} km/h. " .
                         "Motion: " . ($motionDetected ? 'Yes' : 'No'),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'triggered_at' => now(),
            'status' => 'unread',
        ]);

        Log::info('Geofence breach alert created', [
            'asset_id' => $asset->id,
            'geofence_id' => $geofence->id,
            'distance_outside' => $distanceOutside,
            'severity' => $severity,
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in meters
     */
    public function calculateDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = self::EARTH_RADIUS_METERS * $c;

        return $distance;
    }

    /**
     * Determine alert severity based on distance and motion
     */
    protected function determineSeverity(
        float $distanceOutside,
        float $speed,
        bool $motionDetected
    ): string {
        // High severity: far outside + moving fast + motion detected
        if ($distanceOutside > 500 && $speed > 50 && $motionDetected) {
            return 'high';
        }

        // Medium severity: moderate distance or active motion
        if ($distanceOutside > 100 || ($speed > 25 && $motionDetected)) {
            return 'medium';
        }

        // Low severity: just outside geofence
        return 'low';
    }

    /**
     * Get all geofence violations for an asset within a date range
     */
    public function getViolationHistory(
        Asset $asset,
        ?Carbon $from = null,
        ?Carbon $to = null
    ) {
        $query = Alert::where('asset_id', $asset->id)
            ->where('alert_type', 'geofence_breach')
            ->orderBy('triggered_at', 'desc');

        if ($from) {
            $query->where('triggered_at', '>=', $from);
        }

        if ($to) {
            $query->where('triggered_at', '<=', $to);
        }

        return $query->get();
    }

    /**
     * Check if asset is currently inside a specific geofence
     */
    public function isAssetInsideGeofence(
        Asset $asset,
        Geofence $geofence
    ): bool {
        $latestLocation = $asset->latestLocation;

        if (!$latestLocation) {
            return false;
        }

        $distance = $this->calculateDistance(
            $latestLocation->latitude,
            $latestLocation->longitude,
            $geofence->center_latitude,
            $geofence->center_longitude
        );

        return $distance <= $geofence->radius_meters;
    }

    /**
     * Get all assets currently outside a geofence
     */
    public function getAssetsOutsideGeofence(Geofence $geofence)
    {
        return $geofence->asset()
            ->with('latestLocation')
            ->get()
            ->filter(function ($asset) use ($geofence) {
                return !$this->isAssetInsideGeofence($asset, $geofence);
            });
    }

    /**
     * Create a geofence (CRUD method for compatibility)
     */
    public function createGeofence(array $data): Geofence
    {
        $data['created_by'] = auth()->id();
        return Geofence::create($data);
    }

    /**
     * Update a geofence (CRUD method for compatibility)
     */
    public function updateGeofence(Geofence $geofence, array $data): Geofence
    {
        $geofence->update($data);
        return $geofence;
    }

    /**
     * Delete a geofence (CRUD method for compatibility)
     */
    public function deleteGeofence(Geofence $geofence): void
    {
        $geofence->delete();
    }

    /**
     * Check if point is inside geofence (CRUD method for compatibility)
     */
    public function isPointInsideGeofence(Geofence $geofence, float $lat, float $lng): bool
    {
        $distance = $this->calculateDistance(
            $lat,
            $lng,
            $geofence->center_latitude,
            $geofence->center_longitude
        );
        return $distance <= $geofence->radius_meters;
    }
}
