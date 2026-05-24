<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Alert;
use App\Models\AssetValue;
use App\Models\LocationLog;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    /**
     * Get asset summary report
     */
    public function getAssetSummaryReport(array $filters = []): array
    {
        $query = $this->applyFilters(Asset::query(), $filters);
        $assets = $query->with(['department', 'assetValue'])->get();

        return [
            'total_assets' => $assets->count(),
            'by_status' => $this->groupByStatus($assets),
            'by_type' => $this->groupByType($assets),
            'by_department' => $this->groupByDepartment($assets),
            'total_value' => $this->calculateTotalValue($assets),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get alerts report
     */
    public function getAlertsReport(array $filters = []): array
    {
        $query = Alert::query();

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        if (isset($filters['severity'])) {
            $query->where('severity', $filters['severity']);
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['department_id'])) {
            $query->whereHas('asset', fn ($q) => $q->where('department_id', $filters['department_id']));
        }

        $alerts = $query->with('asset')->get();

        return [
            'total_alerts' => $alerts->count(),
            'by_severity' => $this->groupBySeverity($alerts),
            'by_status' => $alerts->groupBy('status')->map(fn ($group) => $group->count())->toArray(),
            'by_type' => $alerts->groupBy('alert_type')->map(fn ($group) => $group->count())->toArray(),
            'critical_alerts' => $alerts->where('severity', 'critical')->count(),
            'alerts_list' => $alerts->map(fn ($alert) => [
                'id' => $alert->id,
                'asset_name' => $alert->asset->name,
                'type' => $alert->alert_type,
                'severity' => $alert->severity,
                'status' => $alert->status,
                'created_at' => $alert->created_at,
                'resolved_at' => $alert->resolved_at,
            ])->toArray(),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get tracking report
     */
    public function getTrackingReport(array $filters = []): array
    {
        $query = $this->applyFilters(Asset::query(), $filters);
        $assets = $query->get();

        $trackingData = $assets->map(function ($asset) use ($filters) {
            $locationQuery = $asset->locationLogs();

            if (isset($filters['date_from'])) {
                $locationQuery->whereDate('created_at', '>=', $filters['date_from']);
            }
            if (isset($filters['date_to'])) {
                $locationQuery->whereDate('created_at', '<=', $filters['date_to']);
            }

            $locations = $locationQuery->get();

            return [
                'asset_id' => $asset->id,
                'asset_name' => $asset->name,
                'total_locations' => $locations->count(),
                'first_location' => $locations->first(),
                'last_location' => $locations->last(),
                'average_speed' => $locations->avg('speed') ?? 0,
                'max_speed' => $locations->max('speed') ?? 0,
                'motion_detected_count' => $locations->where('motion_detected', true)->count(),
            ];
        });

        return [
            'total_assets_tracked' => $trackingData->count(),
            'total_locations' => $trackingData->sum('total_locations'),
            'assets' => $trackingData->toArray(),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get asset values report
     */
    public function getAssetValuesReport(array $filters = []): array
    {
        $query = AssetValue::query();

        if (isset($filters['department_id'])) {
            $query->whereHas('asset', fn ($q) => $q->where('department_id', $filters['department_id']));
        }

        $values = $query->with('asset')->get();

        $values->each(fn ($v) => $v->updateCurrentValue());

        return [
            'total_assets' => $values->count(),
            'total_purchase_value' => (float) $values->sum('purchase_price'),
            'total_current_value' => (float) $values->sum('current_value'),
            'total_depreciation' => (float) $values->sum(fn ($v) => $v->purchase_price - $v->current_value),
            'by_department' => $this->groupValuesByDepartment($values),
            'by_depreciation_method' => $values->groupBy('depreciation_method')
                ->map(fn ($group) => [
                    'method' => $group->first()->depreciation_method,
                    'count' => $group->count(),
                    'total_value' => (float) $group->sum('current_value'),
                ])->values()->toArray(),
            'assets_list' => $values->map(fn ($v) => [
                'asset_id' => $v->asset_id,
                'asset_name' => $v->asset->name,
                'purchase_price' => (float) $v->purchase_price,
                'current_value' => (float) $v->current_value,
                'depreciation_amount' => (float) ($v->purchase_price - $v->current_value),
                'depreciation_percentage' => (float) (($v->purchase_price - $v->current_value) / $v->purchase_price * 100),
            ])->toArray(),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Apply common filters to query
     */
    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['asset_type'])) {
            $query->where('asset_type', $filters['asset_type']);
        }

        return $query;
    }

    /**
     * Group assets by status
     */
    private function groupByStatus(Collection $assets): array
    {
        return $assets->groupBy('status')
            ->map(fn ($group) => [
                'status' => $group->first()->status,
                'count' => $group->count(),
            ])->values()->toArray();
    }

    /**
     * Group assets by type
     */
    private function groupByType(Collection $assets): array
    {
        return $assets->groupBy('asset_type')
            ->map(fn ($group) => [
                'type' => $group->first()->asset_type,
                'count' => $group->count(),
            ])->values()->toArray();
    }

    /**
     * Group assets by department
     */
    private function groupByDepartment(Collection $assets): array
    {
        return $assets->groupBy(fn ($asset) => $asset->department?->id)
            ->map(fn ($group) => [
                'department' => $group->first()->department?->name ?? 'Unassigned',
                'count' => $group->count(),
            ])->values()->toArray();
    }

    /**
     * Group values by department
     */
    private function groupValuesByDepartment(Collection $values): array
    {
        return $values->groupBy(fn ($v) => $v->asset->department?->id)
            ->map(fn ($group) => [
                'department' => $group->first()->asset->department?->name ?? 'Unassigned',
                'count' => $group->count(),
                'total_value' => (float) $group->sum('current_value'),
            ])->values()->toArray();
    }

    /**
     * Group alerts by severity
     */
    private function groupBySeverity(Collection $alerts): array
    {
        return $alerts->groupBy('severity')
            ->map(fn ($group) => [
                'severity' => $group->first()->severity,
                'count' => $group->count(),
            ])->values()->toArray();
    }

    /**
     * Calculate total asset value
     */
    private function calculateTotalValue(Collection $assets): float
    {
        return (float) $assets->sum(fn ($asset) => $asset->assetValue?->current_value ?? 0);
    }
}
