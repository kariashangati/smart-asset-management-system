<?php

namespace Tests\Unit\Services;

use App\Models\Asset;
use App\Models\AssetValue;
use App\Models\Department;
use App\Models\TrackerDevice;
use App\Services\AssetValueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AssetValueServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AssetValueService $service;
    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AssetValueService::class);
        $department = Department::factory()->create();
        $trackerDevice = TrackerDevice::factory()->create();
        $this->asset = Asset::factory()->create([
            'department_id' => $department->id,
            'tracker_device_id' => $trackerDevice->id,
        ]);
    }

    public function test_create_asset_value()
    {
        $assetValue = $this->service->createAssetValue($this->asset, [
            'purchase_price' => 10000,
            'useful_life_years' => 5,
        ]);

        $this->assertNotNull($assetValue->id);
        $this->assertEquals(10000, $assetValue->purchase_price);
    }

    public function test_calculate_depreciation()
    {
        $assetValue = AssetValue::factory()->create([
            'asset_id' => $this->asset->id,
            'purchase_price' => 10000,
            'useful_life_years' => 5,
            'purchase_date' => now()->subYears(2),
        ]);

        $depreciation = $this->service->calculateDepreciation($assetValue);

        $this->assertArrayHasKey('total_depreciation', $depreciation);
        $this->assertArrayHasKey('current_value', $depreciation);
        $this->assertGreaterThan(0, $depreciation['total_depreciation']);
    }

    public function test_get_depreciation_schedule()
    {
        $assetValue = AssetValue::factory()->create([
            'asset_id' => $this->asset->id,
            'purchase_price' => 10000,
            'useful_life_years' => 5,
        ]);

        $schedule = $this->service->getDepreciationSchedule($assetValue);

        $this->assertCount(5, $schedule);
        $this->assertArrayHasKey('year', $schedule[0]);
        $this->assertArrayHasKey('book_value', $schedule[0]);
    }

    public function test_cannot_create_with_invalid_data()
    {
        $this->expectException(ValidationException::class);

        $this->service->createAssetValue($this->asset, [
            'purchase_price' => -1000, // Invalid negative price
            'useful_life_years' => 5,
        ]);
    }
}
