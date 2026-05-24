<?php

namespace Tests\Feature\Api;

use App\Models\Asset;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->department = Department::factory()->create();
        $this->manager = User::factory()->create(['department_id' => $this->department->id]);
        $this->manager->assignRole('asset_manager');
    }

    public function test_can_list_assets()
    {
        Asset::factory()->count(3)->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/assets');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_asset()
    {
        $data = [
            'name' => 'Test Vehicle',
            'asset_type' => 'vehicle',
            'serial_number' => 'VH-001',
            'department_id' => $this->department->id,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->admin, 'api')
            ->postJson('/api/assets', $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.name', 'Test Vehicle');

        $this->assertDatabaseHas('assets', ['serial_number' => 'VH-001']);
    }

    public function test_can_view_asset()
    {
        $asset = Asset::factory()->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/assets/{$asset->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $asset->id);
    }

    public function test_can_update_asset()
    {
        $asset = Asset::factory()->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->putJson("/api/assets/{$asset->id}", ['name' => 'Updated Name']);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name');
    }

    public function test_can_delete_asset()
    {
        $asset = Asset::factory()->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->deleteJson("/api/assets/{$asset->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
    }

    public function test_manager_can_only_view_own_department_assets()
    {
        $ownAsset = Asset::factory()->create(['department_id' => $this->department->id]);
        $otherAsset = Asset::factory()->create();

        $response = $this->actingAs($this->manager, 'api')
            ->getJson("/api/assets/{$ownAsset->id}");
        $response->assertStatus(200);

        $response = $this->actingAs($this->manager, 'api')
            ->getJson("/api/assets/{$otherAsset->id}");
        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_assets()
    {
        $response = $this->getJson('/api/assets');
        $response->assertStatus(401);
    }
}
