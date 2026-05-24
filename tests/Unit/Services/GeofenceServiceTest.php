<?php

namespace Tests\Unit\Services;

use App\Models\Asset;
use App\Models\Geofence;
use App\Services\GeofenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeofenceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GeofenceService $geofenceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->geofenceService = app(GeofenceService::class);
    }

    public function test_can_calculate_distance_between_coordinates()
    {
        // New York to Los Angeles (approximately 3944 km)
        $distance = $this->geofenceService->calculateDistance(
            40.7128,  // NYC latitude
            -74.0060, // NYC longitude
            34.0522,  // LA latitude
            -118.2437 // LA longitude
        );

        // Distance should be around 3944 km (3944000 meters)
        $this->assertGreaterThan(3900000, $distance);
        $this->assertLessThan(3990000, $distance);
    }

    public function test_point_inside_geofence()
    {
        $geofence = Geofence::factory()->create([
            'center_latitude' => 40.7128,
            'center_longitude' => -74.0060,
            'radius_meters' => 5000, // 5 km
        ]);

        $isInside = $this->geofenceService->isPointInsideGeofence(
            $geofence,
            40.7128,  // Same as center
            -74.0060  // Same as center
        );

        $this->assertTrue($isInside);
    }

    public function test_point_outside_geofence()
    {
        $geofence = Geofence::factory()->create([
            'center_latitude' => 40.7128,
            'center_longitude' => -74.0060,
            'radius_meters' => 1000,
        ]);

        $isInside = $this->geofenceService->isPointInsideGeofence(
            $geofence,
            40.8200,  // About 9 km away
            -74.0100
        );

        $this->assertFalse($isInside);
    }
}
