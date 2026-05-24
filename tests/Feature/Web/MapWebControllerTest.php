<?php

namespace Tests\Feature\Web;

use App\Models\Asset;
use App\Models\Department;
use App\Models\TrackerDevice;
use App\Models\LocationLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapWebControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');

        $department = Department::factory()->create();
        $trackerDevice = TrackerDevice::factory()->create();
        
        $this->asset = Asset::factory()->create([
            'department_id' => $department->id,
            'tracker_device_id' => $trackerDevice->id,
        ]);

        LocationLog::factory()->create([
            'asset_id' => $this->asset->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);
    }

    public function test_can_view_map_index()
    {
        $response = $this->actingAs($this->user)
            ->get('/map');

        $response->assertStatus(200)
            ->assertViewIs('map.index');
    }

    public function test_can_view_asset_on_map()
    {
        $response = $this->actingAs($this->user)
            ->get("/map/asset/{$this->asset->id}");

        $response->assertStatus(200)
            ->assertViewIs('map.asset-details')
            ->assertViewHas('asset', $this->asset);
    }

    public function test_unauthenticated_user_cannot_view_map()
    {
        $response = $this->get('/map');

        $response->assertRedirect('/login');
    }
}
