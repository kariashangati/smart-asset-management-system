<?php

namespace Tests\Unit\Services;

use App\Models\Asset;
use App\Models\LocationLog;
use App\Models\TrackerDevice;
use App\Services\LocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LocationService $locationService;
    protected TrackerDevice $trackerDevice;
    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->locationService = app(LocationService::class);
        $this->trackerDevice = TrackerDevice::factory()->create();
        $this->asset = Asset::factory()->create([
            'tracker_device_id' => $this->trackerDevice->id,
        ]);
    }

    public function test_can_store_location_log()
    {
        $data = [
            'tracker_device_id' => $this->trackerDevice->id,
            'asset_id' => $this->asset->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'speed' => 50,
            'motion_detected' => true,
        ];

        $log = $this->locationService->storeLocationLog($data);

        $this->assertNotNull($log->id);
        $this->assertEquals(40.7128, $log->latitude);
        $this->assertEquals(50, $log->speed);
    }

    public function test_can_get_latest_location()
    {
        LocationLog::factory()->create([
            'asset_id' => $this->asset->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $location = $this->locationService->getLatestLocationForAsset($this->asset);

        $this->assertNotNull($location);
        $this->assertEquals(40.7128, $location->latitude);
    }

    public function test_can_get_location_history()
    {
        LocationLog::factory()->count(5)->create(['asset_id' => $this->asset->id]);

        $history = $this->locationService->getLocationHistory($this->asset->id);

        $this->assertCount(5, $history);
    }

    public function test_can_calculate_average_speed()
    {
        LocationLog::factory()->create(['asset_id' => $this->asset->id, 'speed' => 50]);
        LocationLog::factory()->create(['asset_id' => $this->asset->id, 'speed' => 60]);
        LocationLog::factory()->create(['asset_id' => $this->asset->id, 'speed' => 70]);

        $avgSpeed = $this->locationService->calculateAverageSpeed($this->asset->id);

        $this->assertEquals(60, $avgSpeed);
    }
}
