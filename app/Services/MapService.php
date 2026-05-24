<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\LocationLog;
use App\Models\Geofence;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;

class MapService
{
    /**
     * Get all assets with current locations for map display
     */
    public function getAllAssetsForMap(array $filters = []): Collection
    {
        $query = Asset::with('latestLocation', 'department');

        // Filter by department
        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        // Filter by status
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by asset type
        if (isset($filters['asset_type'])) {
            $query->where('asset_type', $filters['asset_type']);
        }

        return $query->whereHas('latestLocation')
            ->get()
            ->map(function (Asset $asset) {
                return $this->formatAssetForMap($asset);
            });
    }

    /**
     * Get single asset with details for map
     */
    public function getAssetForMap(Asset $asset): array
    {
        return $this->formatAssetForMap($asset);
    }

    /**
     * Format asset data for map display
     */
    private function formatAssetForMap(Asset $asset): array
    {
        $location = $asset->latestLocation;

        return [
            'id' => $asset->id,
            'name' => $asset->name,
            'type' => $asset->asset_type,
            'status' => $asset->status,
            'serial_number' => $asset->serial_number,
            'department' => $asset->department?->name,
            'location' => $location ? [
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'speed' => (float) ($location->speed ?? 0),
                'motion_detected' => (bool) $location->motion_detected,
                'last_recorded_at' => $location->created_at->toIso8601String(),
            ] : null,
            'icon' => $this->getAssetIcon($asset->asset_type),
            'color' => $this->getStatusColor($asset->status),
            'has_alerts' => $asset->alerts()->where('status', '!=', 'resolved')->exists(),
        ];
    }

    /**
     * Get location trail for asset (history)
     */
    public function getAssetLocationTrail(Asset $asset, int $limit = 100): array
    {
        $locations = $asset->locationLogs()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(function (LocationLog $log) {
                return [
                    'latitude' => (float) $log->latitude,
                    'longitude' => (float) $log->longitude,
                    'speed' => (float) ($log->speed ?? 0),
                    'timestamp' => $log->created_at->toIso8601String(),
                ];
            })
            ->values()
            ->toArray();

        return [
            'asset_id' => $asset->id,
            'asset_name' => $asset->name,
            'trail_points' => $locations,
            'total_points' => count($locations),
        ];
    }

    /**
     * Get geofence data for map display
     */
    public function getGeofencesForMap(array $filters = []): Collection
    {
        $query = Geofence::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get()->map(function (Geofence $geofence) {
            return [
                'id' => $geofence->id,
                'name' => $geofence->name,
                'center' => [
                    'latitude' => (float) $geofence->center_latitude,
                    'longitude' => (float) $geofence->center_longitude,
                ],
                'radius' => $geofence->radius_meters,
                'status' => $geofence->status,
                'alert_on_breach' => $geofence->alert_on_breach,
                'assets_count' => $geofence->assets()->count(),
            ];
        });
    }

    /**
     * Get assets with violations for specific geofence
     */
    public function getGeofenceViolations(Geofence $geofence): array
    {
        $violations = [];

        $geofence->assets()->with('latestLocation')->each(function (Asset $asset) use ($geofence, &$violations) {
            if ($asset->latestLocation && !$this->isPointInsideGeofence($geofence, $asset->latestLocation)) {
                $violations[] = [
                    'asset_id' => $asset->id,
                    'asset_name' => $asset->name,
                    'latitude' => (float) $asset->latestLocation->latitude,
                    'longitude' => (float) $asset->latestLocation->longitude,
                    'distance_from_center' => $this->calculateDistance(
                        $geofence->center_latitude,
                        $geofence->center_longitude,
                        $asset->latestLocation->latitude,
                        $asset->latestLocation->longitude
                    ),
                ];
            }
        });

        return [
            'geofence_id' => $geofence->id,
            'geofence_name' => $geofence->name,
            'violations' => $violations,
            'violation_count' => count($violations),
        ];
    }

    /**
     * Check if point is inside geofence
     */
    private function isPointInsideGeofence(Geofence $geofence, LocationLog $location): bool
    {
        $distance = $this->calculateDistance(
            $geofence->center_latitude,
            $geofence->center_longitude,
            $location->latitude,
            $location->longitude
        );

        return $distance <= $geofence->radius_meters;
    }

    /**
     * Calculate distance between two coordinates (Haversine formula)
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }

    /**
     * Get asset icon based on type
     */
    private function getAssetIcon(string $type): string
    {
        return match ($type) {
            'vehicle' => 'vehicle',
            'equipment' => 'tools',
            'device' => 'smartphone',
            default => 'marker',
        };
    }

    /**
     * Get color based on status
     */
    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'active' => '#10b981',
            'inactive' => '#6b7280',
            'maintenance' => '#f59e0b',
            'retired' => '#ef4444',
            default => '#3b82f6',
        };
    }
}
