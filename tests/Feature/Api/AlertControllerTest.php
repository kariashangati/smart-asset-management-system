<?php

namespace Tests\Feature\Api;

use App\Models\Alert;
use App\Models\Asset;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $department = Department::factory()->create();
        $this->asset = Asset::factory()->create(['department_id' => $department->id]);
    }

    public function test_can_list_alerts()
    {
        Alert::factory()->count(3)->create(['asset_id' => $this->asset->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/alerts');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_view_alert()
    {
        $alert = Alert::factory()->create(['asset_id' => $this->asset->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/alerts/{$alert->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $alert->id);
    }

    public function test_can_mark_alert_as_read()
    {
        $alert = Alert::factory()->create([
            'asset_id' => $this->asset->id,
            'status' => 'unread',
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->patchJson("/api/alerts/{$alert->id}/mark-read");

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'read');
    }

    public function test_can_mark_alert_as_resolved()
    {
        $alert = Alert::factory()->create([
            'asset_id' => $this->asset->id,
            'status' => 'unread',
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->patchJson("/api/alerts/{$alert->id}/mark-resolved", [
                'resolution_notes' => 'Issue resolved',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'resolved');
    }

    public function test_can_get_unread_count()
    {
        Alert::factory()->count(5)->create([
            'asset_id' => $this->asset->id,
            'status' => 'unread',
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/alerts/count/unread');

        $response->assertStatus(200)
            ->assertJsonPath('unread_count', 5);
    }

    public function test_can_get_alerts_summary()
    {
        Alert::factory()->count(2)->create([
            'asset_id' => $this->asset->id,
            'severity' => 'high',
            'status' => 'unread',
        ]);
        Alert::factory()->count(3)->create([
            'asset_id' => $this->asset->id,
            'severity' => 'medium',
            'status' => 'unread',
        ]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson('/api/alerts/summary');

        $response->assertStatus(200)
            ->assertJsonPath('summary.total_active', 5);
    }
}
