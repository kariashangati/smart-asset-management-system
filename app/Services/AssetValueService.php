<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetValue;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class AssetValueService
{
    /**
     * Create asset value record
     */
    public function createAssetValue(Asset $asset, array $data): AssetValue
    {
        $this->validateAssetValueData($data);

        $assetValue = AssetValue::create([
            'asset_id' => $asset->id,
            'purchase_price' => $data['purchase_price'],
            'current_value' => $data['purchase_price'],
            'depreciation_rate' => $data['depreciation_rate'] ?? 0,
            'depreciation_method' => $data['depreciation_method'] ?? 'straight_line',
            'salvage_value' => $data['salvage_value'] ?? 0,
            'useful_life_years' => $data['useful_life_years'] ?? 5,
            'purchase_date' => $data['purchase_date'] ?? now(),
            'last_revalued_at' => now(),
        ]);

        return $assetValue;
    }

    /**
     * Update asset value record
     */
    public function updateAssetValue(AssetValue $assetValue, array $data): AssetValue
    {
        $this->validateAssetValueData($data);

        $assetValue->update(array_merge($data, [
            'last_revalued_at' => now(),
        ]));

        // Recalculate current value
        $assetValue->updateCurrentValue();

        return $assetValue;
    }

    /**
     * Revalue asset based on current market
     */
    public function revalueAsset(AssetValue $assetValue, float $newValue): AssetValue
    {
        $assetValue->update([
            'current_value' => $newValue,
            'last_revalued_at' => now(),
        ]);

        return $assetValue;
    }

    /**
     * Calculate depreciation for asset
     */
    public function calculateDepreciation(AssetValue $assetValue): array
    {
        $depreciation = $assetValue->calculateDepreciation();
        $currentValue = $assetValue->getDepreciatedValue();
        $percentageDepreciated = ($depreciation / $assetValue->purchase_price) * 100;

        return [
            'total_depreciation' => (float) $depreciation,
            'current_value' => (float) $currentValue,
            'annual_depreciation' => (float) $assetValue->getAnnualDepreciation(),
            'percentage_depreciated' => (float) $percentageDepreciated,
            'remaining_useful_life' => $this->calculateRemainingUsefulLife($assetValue),
        ];
    }

    /**
     * Calculate remaining useful life
     */
    private function calculateRemainingUsefulLife(AssetValue $assetValue): float
    {
        if (!$assetValue->purchase_date || !$assetValue->useful_life_years) {
            return 0;
        }

        $yearsUsed = $assetValue->purchase_date->diffInYears(now());
        $remaining = $assetValue->useful_life_years - $yearsUsed;

        return max(0, (float) $remaining);
    }

    /**
     * Get depreciation schedule
     */
    public function getDepreciationSchedule(AssetValue $assetValue): array
    {
        $schedule = [];
        $purchaseDate = $assetValue->purchase_date ?? now();
        $usefulLife = $assetValue->useful_life_years ?? 5;
        $annualDepreciation = $assetValue->getAnnualDepreciation();

        for ($year = 1; $year <= $usefulLife; $year++) {
            $date = $purchaseDate->copy()->addYears($year);
            $accumulatedDepreciation = $annualDepreciation * $year;
            $bookValue = $assetValue->purchase_price - $accumulatedDepreciation;

            $schedule[] = [
                'year' => $year,
                'date' => $date->toDateString(),
                'annual_depreciation' => (float) $annualDepreciation,
                'accumulated_depreciation' => (float) $accumulatedDepreciation,
                'book_value' => (float) max(0, $bookValue),
            ];
        }

        return $schedule;
    }

    /**
     * Validate asset value data
     */
    private function validateAssetValueData(array $data): void
    {
        $errors = [];

        if (isset($data['purchase_price']) && $data['purchase_price'] < 0) {
            $errors['purchase_price'] = 'Purchase price must be positive';
        }

        if (isset($data['salvage_value']) && isset($data['purchase_price'])) {
            if ($data['salvage_value'] > $data['purchase_price']) {
                $errors['salvage_value'] = 'Salvage value cannot exceed purchase price';
            }
        }

        if (isset($data['useful_life_years']) && $data['useful_life_years'] <= 0) {
            $errors['useful_life_years'] = 'Useful life must be greater than 0';
        }

        if (isset($data['depreciation_method'])) {
            $validMethods = ['straight_line', 'declining_balance', 'sum_of_years'];
            if (!in_array($data['depreciation_method'], $validMethods)) {
                $errors['depreciation_method'] = 'Invalid depreciation method';
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Get assets expiring soon (end of useful life)
     */
    public function getExpiringAssets(int $daysUntil = 90): array
    {
        $expiringDate = now()->addDays($daysUntil);

        $values = AssetValue::whereHas('asset')
            ->get()
            ->filter(function ($value) use ($expiringDate) {
                if (!$value->purchase_date || !$value->useful_life_years) {
                    return false;
                }
                $endDate = $value->purchase_date->copy()->addYears($value->useful_life_years);
                return $endDate <= $expiringDate && $endDate > now();
            });

        return $values->map(fn ($v) => [
            'asset_id' => $v->asset_id,
            'asset_name' => $v->asset->name,
            'expiry_date' => $v->purchase_date->copy()->addYears($v->useful_life_years),
            'days_until_expiry' => now()->diffInDays($v->purchase_date->copy()->addYears($v->useful_life_years)),
            'current_value' => (float) $v->current_value,
        ])->toArray();
    }
}
