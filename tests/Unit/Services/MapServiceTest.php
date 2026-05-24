<?php

namespace Tests\Unit\Services;

use App\Models\Asset;
use App\Models\Geofence;
use App\Models\LocationLog;
use App\Models\Department;
use App\Models\TrackerDevice;
use App\Services\MapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MapService $mapService;
    protected Asset $asset;
    protected Geofence $geofence;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapService = app(MapService::class);

        $department = Department::factory()->create();
        $trackerDevice = TrackerDevice::factory()->create();
        
        $this->asset = Asset::factory()->create([
            'department_id' => $department->id,
            'tracker_device_id' => $trackerDevice->id,
        ]);

        $this->geofence = Geofence::factory()->create([
            'center_latitude' => 40.7128,
            'center_longitude' => -74.0060,
            'radius_meters' => 5000,
        ]);
    }

    public function test_can_get_all_assets_for_map()
    {
        LocationLog::factory()->create(['asset_id' => $this->asset->id]);

        $assets = $this->mapService->getAllAssetsForMap();

        $this->assertCount(1, $assets);
        $this->assertEquals($this->asset->name, $assets->first()['name']);
    }

    public function test_can_get_asset_for_map()
    {
        LocationLog::factory()->create([
            'asset_id' => $this->asset->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $assetData = $this->mapService->getAssetForMap($this->asset);

        $this->assertEquals($this->asset->id, $assetData['id']);
        $this->assertNotNull($assetData['location']);
    }

    public function test_can_get_asset_location_trail()
    {
        LocationLog::factory()->count(10)->create(['asset_id' => $this->asset->id]);

        $trail = $this->mapService->getAssetLocationTrail($this->asset, 10);

        $this->assertEquals(10, $trail['total_points']);
        $this->assertCount(10, $trail['trail_points']);
    }

    public function test_can_get_geofences_for_map()
    {
        $geofences = $this->mapService->getGeofencesForMap();

        $this->assertCount(1, $geofences);
        $this->assertEquals($this->geofence->name, $geofences->first()['name']);
    }
}
