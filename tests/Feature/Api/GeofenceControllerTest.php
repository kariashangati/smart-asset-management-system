<?php

namespace Tests\Feature\Api;

use App\Models\Geofence;
use App\Models\Asset;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeofenceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->department = Department::factory()->create();
    }

    public function test_can_list_geofences()
    {
        Geofence::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/geofences');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_geofence()
    {
        $data = [
            'name' => 'Test Geofence',
            'center_latitude' => 40.7128,
            'center_longitude' => -74.0060,
            'radius_meters' => 1000,
            'status' => 'active',
            'alert_on_breach' => true,
        ];

        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/geofences', $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.name', 'Test Geofence');

        $this->assertDatabaseHas('geofences', ['name' => 'Test Geofence']);
    }

    public function test_can_assign_assets_to_geofence()
    {
        $geofence = Geofence::factory()->create();
        $assets = Asset::factory()->count(2)->create();

        $response = $this->actingAs($this->admin, 'api')
            ->postJson("/api/geofences/{$geofence->id}/assign-assets", [
                'asset_ids' => $assets->pluck('id')->toArray(),
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertEquals(2, $geofence->assets()->count());
    }

    public function test_can_check_if_asset_inside_geofence()
    {
        $geofence = Geofence::factory()->create([
            'center_latitude' => 40.7128,
            'center_longitude' => -74.0060,
            'radius_meters' => 1000,
        ]);
        $asset = Asset::factory()->create();

        $response = $this->actingAs($this->admin, 'api')
            ->postJson("/api/geofences/{$geofence->id}/check-asset", [
                'asset_id' => $asset->id,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'asset_id' => $asset->id,
                'geofence_id' => $geofence->id,
            ]);
    }
}
