<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AssetValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'purchase_price',
        'current_value',
        'depreciation_rate',
        'depreciation_method',
        'salvage_value',
        'useful_life_years',
        'purchase_date',
        'last_revalued_at',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'purchase_date' => 'datetime',
        'last_revalued_at' => 'datetime',
    ];

    /**
     * Get the asset that owns this value record
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Calculate current depreciation
     */
    public function calculateDepreciation(): float
    {
        if (!$this->purchase_date) {
            return 0;
        }

        $yearsUsed = $this->purchase_date->diffInYears(now());
        $usefulLife = $this->useful_life_years ?? 5;

        return match ($this->depreciation_method ?? 'straight_line') {
            'straight_line' => $this->straightLineDepreciation($yearsUsed, $usefulLife),
            'declining_balance' => $this->decliningBalanceDepreciation($yearsUsed, $usefulLife),
            'sum_of_years' => $this->sumOfYearsDepreciation($yearsUsed, $usefulLife),
            default => $this->straightLineDepreciation($yearsUsed, $usefulLife),
        };
    }

    /**
     * Straight-line depreciation
     */
    private function straightLineDepreciation(int $yearsUsed, int $usefulLife): float
    {
        if ($usefulLife <= 0) {
            return 0;
        }

        $depreciableAmount = $this->purchase_price - ($this->salvage_value ?? 0);
        $annualDepreciation = $depreciableAmount / $usefulLife;
        $totalDepreciation = min($annualDepreciation * $yearsUsed, $depreciableAmount);

        return (float) $totalDepreciation;
    }

    /**
     * Declining balance depreciation
     */
    private function decliningBalanceDepreciation(int $yearsUsed, int $usefulLife): float
    {
        $rate = 2 / $usefulLife;
        $value = $this->purchase_price;

        for ($i = 0; $i < $yearsUsed; $i++) {
            $value *= (1 - $rate);
        }

        return (float) ($this->purchase_price - $value);
    }

    /**
     * Sum of years depreciation
     */
    private function sumOfYearsDepreciation(int $yearsUsed, int $usefulLife): float
    {
        $sumOfYears = ($usefulLife * ($usefulLife + 1)) / 2;
        $depreciableAmount = $this->purchase_price - ($this->salvage_value ?? 0);
        $totalDepreciation = 0;

        for ($i = 0; $i < min($yearsUsed, $usefulLife); $i++) {
            $yearFraction = ($usefulLife - $i) / $sumOfYears;
            $totalDepreciation += $depreciableAmount * $yearFraction;
        }

        return (float) $totalDepreciation;
    }

    /**
     * Get current asset value after depreciation
     */
    public function getDepreciatedValue(): float
    {
        return (float) ($this->purchase_price - $this->calculateDepreciation());
    }

    /**
     * Update current value based on depreciation
     */
    public function updateCurrentValue(): void
    {
        $this->update([
            'current_value' => $this->getDepreciatedValue(),
            'last_revalued_at' => now(),
        ]);
    }

    /**
     * Get annual depreciation amount
     */
    public function getAnnualDepreciation(): float
    {
        if (!$this->useful_life_years || $this->useful_life_years <= 0) {
            return 0;
        }

        $depreciableAmount = $this->purchase_price - ($this->salvage_value ?? 0);
        return (float) ($depreciableAmount / $this->useful_life_years);
    }
}
