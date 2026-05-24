<?php

namespace Tests\Feature\Api;

use App\Models\Asset;
use App\Models\Alert;
use App\Models\Department;
use App\Models\TrackerDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
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

    public function test_can_get_dashboard_metrics()
    {
        Asset::factory()->count(5)->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/dashboard/metrics');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.total_assets', 5);
    }

    public function test_can_get_dashboard_charts()
    {
        Asset::factory()->count(3)->create(['department_id' => $this->department->id, 'status' => 'active']);
        Asset::factory()->count(2)->create(['department_id' => $this->department->id, 'status' => 'maintenance']);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/dashboard/charts');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['assets_by_status', 'assets_by_type']]);
    }

    public function test_can_get_asset_health_summary()
    {
        Asset::factory()->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/dashboard/health');

        $response->assertStatus(200)
            ->assertJsonPath('data.total', 1);
    }

    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->getJson('/api/dashboard/metrics');

        $response->assertStatus(401);
    }
}
