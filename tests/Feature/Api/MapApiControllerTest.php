<?php

namespace Tests\Feature\Api;

use App\Models\Asset;
use App\Models\Geofence;
use App\Models\LocationLog;
use App\Models\Department;
use App\Models\TrackerDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapApiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Department $department;
    protected TrackerDevice $trackerDevice;
    protected Asset $asset;
    protected Geofence $geofence;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->department = Department::factory()->create();
        $this->trackerDevice = TrackerDevice::factory()->create();
        
        $this->asset = Asset::factory()->create([
            'department_id' => $this->department->id,
            'tracker_device_id' => $this->trackerDevice->id,
        ]);

        $this->geofence = Geofence::factory()->create([
            'center_latitude' => 40.7128,
            'center_longitude' => -74.0060,
            'radius_meters' => 1000,
        ]);
    }

    public function test_can_get_assets_for_map()
    {
        LocationLog::factory()->create([
            'asset_id' => $this->asset->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/map/assets');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('count', 1);
    }

    public function test_can_get_single_asset_location()
    {
        LocationLog::factory()->create([
            'asset_id' => $this->asset->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/map/assets/{$this->asset->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $this->asset->id)
            ->assertJsonPath('data.location.latitude', 40.7128);
    }

    public function test_can_get_asset_location_trail()
    {
        LocationLog::factory()->count(5)->create([
            'asset_id' => $this->asset->id,
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/map/assets/{$this->asset->id}/location-trail");

        $response->assertStatus(200)
            ->assertJsonPath('data.total_points', 5);
    }

    public function test_can_get_geofences_for_map()
    {
        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/map/geofences');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_can_get_geofence_violations()
    {
        $this->geofence->assets()->attach($this->asset->id);

        LocationLog::factory()->create([
            'asset_id' => $this->asset->id,
            'latitude' => 40.8200,
            'longitude' => -74.0100,
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/map/geofences/{$this->geofence->id}/violations");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_can_get_map_config()
    {
        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/map/config');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.maps_api_key', config('integrations.google_maps_api_key'));
    }
}
