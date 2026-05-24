<?php

namespace Tests\Unit\Services;

use App\Models\Asset;
use App\Models\Alert;
use App\Models\AssetValue;
use App\Models\Department;
use App\Models\TrackerDevice;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReportService $service;
    protected Department $department;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ReportService::class);
        $this->department = Department::factory()->create();
    }

    public function test_get_asset_summary_report()
    {
        Asset::factory()->count(5)->create(['department_id' => $this->department->id]);

        $report = $this->service->getAssetSummaryReport();

        $this->assertEquals(5, $report['total_assets']);
        $this->assertArrayHasKey('by_status', $report);
        $this->assertArrayHasKey('by_type', $report);
    }

    public function test_get_alerts_report()
    {
        $asset = Asset::factory()->create(['department_id' => $this->department->id]);
        Alert::factory()->count(5)->create(['asset_id' => $asset->id]);

        $report = $this->service->getAlertsReport();

        $this->assertEquals(5, $report['total_alerts']);
        $this->assertArrayHasKey('by_severity', $report);
    }

    public function test_get_asset_values_report()
    {
        $asset = Asset::factory()->create(['department_id' => $this->department->id]);
        AssetValue::factory()->create(['asset_id' => $asset->id, 'purchase_price' => 1000]);

        $report = $this->service->getAssetValuesReport();

        $this->assertEquals(1, $report['total_assets']);
        $this->assertGreaterThan(0, $report['total_purchase_value']);
    }
}
