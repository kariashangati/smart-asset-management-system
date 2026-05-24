<?php

namespace Tests\Feature\Api;

use App\Models\Asset;
use App\Models\Department;
use App\Models\TrackerDevice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected TrackerDevice $trackerDevice;
    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $department = Department::factory()->create();
        $this->trackerDevice = TrackerDevice::factory()->create();
        $this->asset = Asset::factory()->create([
            'department_id' => $department->id,
            'tracker_device_id' => $this->trackerDevice->id,
        ]);
    }

    public function test_can_handle_location_webhook()
    {
        $data = [
            'tracker_device_id' => $this->trackerDevice->id,
            'asset_id' => $this->asset->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'speed' => 50,
            'motion_detected' => true,
        ];

        $response = $this->postJson('/api/webhooks/location', $data);

        $response->assertStatus(202)
            ->assertJson(['success' => true]);
    }

    public function test_can_handle_alert_webhook()
    {
        $data = [
            'tracker_device_id' => $this->trackerDevice->id,
            'asset_id' => $this->asset->id,
            'alert_type' => 'geofence_breach',
            'severity' => 'high',
            'message' => 'Asset outside geofence',
        ];

        $response = $this->postJson('/api/webhooks/alert', $data);

        $response->assertStatus(202)
            ->assertJson(['success' => true]);
    }

    public function test_health_check_endpoint()
    {
        $response = $this->getJson('/api/webhooks/health');

        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    public function test_webhook_requires_valid_data()
    {
        $response = $this->postJson('/api/webhooks/location', [
            'tracker_device_id' => 999,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);

        $response->assertStatus(422);
    }
}
