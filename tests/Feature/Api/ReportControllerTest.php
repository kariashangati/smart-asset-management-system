<?php

namespace Tests\Feature\Api;

use App\Models\Asset;
use App\Models\Alert;
use App\Models\AssetValue;
use App\Models\Department;
use App\Models\TrackerDevice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
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

    public function test_can_get_asset_summary_report()
    {
        Asset::factory()->count(3)->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/reports/assets');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.total_assets', 3);
    }

    public function test_can_get_alerts_report()
    {
        $asset = Asset::factory()->create(['department_id' => $this->department->id]);
        Alert::factory()->count(5)->create(['asset_id' => $asset->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/reports/alerts');

        $response->assertStatus(200)
            ->assertJsonPath('data.total_alerts', 5);
    }

    public function test_can_get_asset_values_report()
    {
        $asset = Asset::factory()->create(['department_id' => $this->department->id]);
        AssetValue::factory()->create(['asset_id' => $asset->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/reports/asset-values');

        $response->assertStatus(200)
            ->assertJsonPath('data.total_assets', 1);
    }
}
