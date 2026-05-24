<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_can_get_asset_summary_report()
    {
        \App\Models\Asset::factory()->count(5)->create();

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/reports/assets');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['total_assets', 'active_assets']]);
    }

    public function test_can_get_alerts_report()
    {
        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/reports/alerts');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
