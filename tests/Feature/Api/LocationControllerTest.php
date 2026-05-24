<?php

namespace Tests\Feature\Api;

use App\Models\Asset;
use App\Models\LocationLog;
use App\Models\Department;
use App\Models\TrackerDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected TrackerDevice $trackerDevice;
    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $department = Department::factory()->create();
        $this->trackerDevice = TrackerDevice::factory()->create();
        $this->asset = Asset::factory()->create([
            'department_id' => $department->id,
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

        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/location-logs', $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('location_logs', [
            'asset_id' => $this->asset->id,
            'latitude' => 40.7128,
        ]);
    }

    public function test_can_batch_store_location_logs()
    {
        $data = [
            'locations' => [
                [
                    'tracker_device_id' => $this->trackerDevice->id,
                    'asset_id' => $this->asset->id,
                    'latitude' => 40.7128,
                    'longitude' => -74.0060,
                    'speed' => 50,
                ],
                [
                    'tracker_device_id' => $this->trackerDevice->id,
                    'asset_id' => $this->asset->id,
                    'latitude' => 40.7150,
                    'longitude' => -74.0080,
                    'speed' => 55,
                ],
            ],
        ];

        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/location-logs/batch', $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true, 'count' => 2]);
    }

    public function test_can_get_current_location()
    {
        LocationLog::factory()->create([
            'asset_id' => $this->asset->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/assets/{$this->asset->id}/location");

        $response->assertStatus(200)
            ->assertJsonPath('data.latitude', 40.7128);
    }

    public function test_can_get_location_history()
    {
        LocationLog::factory()->count(5)->create(['asset_id' => $this->asset->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/assets/{$this->asset->id}/location-history");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(5, 'data');
    }

    public function test_can_get_location_statistics()
    {
        LocationLog::factory()->count(3)->create([
            'asset_id' => $this->asset->id,
            'speed' => 50,
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/assets/{$this->asset->id}/location-stats");

        $response->assertStatus(200)
            ->assertJsonPath('data.asset_id', $this->asset->id);
    }
}
