<?php

namespace Tests\Feature\Api;

use App\Models\Asset;
use App\Models\CustomAlertRule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomAlertRuleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->asset = Asset::factory()->create();
    }

    public function test_can_create_custom_alert_rule()
    {
        $data = [
            'rule_name' => 'Speed Limit Alert',
            'rule_type' => 'speed_threshold',
            'threshold_value' => 100,
            'action' => 'email',
            'recipient_emails' => ['admin@example.com'],
        ];

        $response = $this->actingAs($this->admin, 'api')
            ->postJson("/api/assets/{$this->asset->id}/custom-rules", $data);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('custom_alert_rules', ['rule_name' => 'Speed Limit Alert']);
    }

    public function test_can_list_custom_alert_rules()
    {
        CustomAlertRule::factory()->count(3)->create(['asset_id' => $this->asset->id]);

        $response = $this->actingAs($this->admin, 'api')
            ->getJson("/api/assets/{$this->asset->id}/custom-rules");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
