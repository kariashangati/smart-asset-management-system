<?php

namespace Tests\Unit\Services;

use App\Models\Asset;
use App\Models\AssetValue;
use App\Models\Department;
use App\Models\TrackerDevice;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardService $service;
    protected Department $department;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DashboardService::class);
        $this->department = Department::factory()->create();
    }

    public function test_get_metrics_returns_correct_counts()
    {
        Asset::factory()->count(5)->create(['department_id' => $this->department->id, 'status' => 'active']);
        Asset::factory()->count(2)->create(['department_id' => $this->department->id, 'status' => 'maintenance']);

        $metrics = $this->service->getMetrics();

        $this->assertEquals(7, $metrics['total_assets']);
        $this->assertEquals(5, $metrics['active_assets']);
        $this->assertEquals(2, $metrics['assets_in_maintenance']);
    }

    public function test_get_chart_data_returns_grouped_data()
    {
        Asset::factory()->count(3)->create(['department_id' => $this->department->id, 'asset_type' => 'vehicle']);
        Asset::factory()->count(2)->create(['department_id' => $this->department->id, 'asset_type' => 'equipment']);

        $charts = $this->service->getChartData();

        $this->assertNotEmpty($charts['assets_by_type']);
        $this->assertNotEmpty($charts['assets_by_department']);
    }

    public function test_get_asset_health_summary()
    {
        Asset::factory()->count(3)->create(['department_id' => $this->department->id, 'status' => 'active']);

        $health = $this->service->getAssetHealthSummary();

        $this->assertEquals(3, $health['total']);
        $this->assertIsInt($health['health_percentage']);
    }
}
