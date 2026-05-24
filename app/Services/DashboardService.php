<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Alert;
use App\Models\Department;
use App\Models\AssetValue;
use App\Models\LocationLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * Get dashboard metrics
     */
    public function getMetrics(array $filters = []): array
    {
        return [
            'total_assets' => $this->getTotalAssets($filters),
            'active_assets' => $this->getActiveAssets($filters),
            'assets_in_maintenance' => $this->getMaintenanceAssets($filters),
            'total_asset_value' => $this->getTotalAssetValue($filters),
            'active_alerts' => $this->getActiveAlerts($filters),
            'resolved_alerts_today' => $this->getResolvedAlertsToday($filters),
            'departments' => $this->getDepartmentCount(),
            'unread_alerts' => $this->getUnreadAlerts($filters),
        ];
    }

    /**
     * Get total number of assets
     */
    private function getTotalAssets(array $filters = []): int
    {
        $query = Asset::query();

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        return $query->count();
    }

    /**
     * Get active assets count
     */
    private function getActiveAssets(array $filters = []): int
    {
        $query = Asset::where('status', 'active');

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        return $query->count();
    }

    /**
     * Get assets in maintenance
     */
    private function getMaintenanceAssets(array $filters = []): int
    {
        $query = Asset::where('status', 'maintenance');

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        return $query->count();
    }

    /**
     * Get total asset value
     */
    private function getTotalAssetValue(array $filters = []): float
    {
        $query = AssetValue::query();

        if (isset($filters['department_id'])) {
            $query->whereHas('asset', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        return (float) $query->sum('current_value');
    }

    /**
     * Get active alerts count
     */
    private function getActiveAlerts(array $filters = []): int
    {
        $query = Alert::whereIn('status', ['open', 'acknowledged']);

        if (isset($filters['department_id'])) {
            $query->whereHas('asset', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        return $query->count();
    }

    /**
     * Get alerts resolved today
     */
    private function getResolvedAlertsToday(array $filters = []): int
    {
        $query = Alert::where('status', 'resolved')
            ->whereDate('resolved_at', today());

        if (isset($filters['department_id'])) {
            $query->whereHas('asset', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        return $query->count();
    }

    /**
     * Get department count
     */
    private function getDepartmentCount(): int
    {
        return Department::count();
    }

    /**
     * Get unread alerts
     */
    private function getUnreadAlerts(array $filters = []): int
    {
        $query = Alert::where('read_at', null);

        if (isset($filters['department_id'])) {
            $query->whereHas('asset', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        return $query->count();
    }

    /**
     * Get chart data for dashboard
     */
    public function getChartData(array $filters = []): array
    {
        return [
            'assets_by_status' => $this->getAssetsByStatus($filters),
            'assets_by_type' => $this->getAssetsByType($filters),
            'alerts_by_severity' => $this->getAlertsBySeverity($filters),
            'assets_by_department' => $this->getAssetsByDepartment(),
            'asset_value_trend' => $this->getAssetValueTrend($filters),
            'alert_trend' => $this->getAlertTrend($filters),
        ];
    }

    /**
     * Get assets grouped by status
     */
    private function getAssetsByStatus(array $filters = []): array
    {
        $query = Asset::query();

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        $data = $query->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return $data->map(function ($item) {
            return [
                'status' => ucfirst($item->status),
                'count' => $item->count,
            ];
        })->toArray();
    }

    /**
     * Get assets grouped by type
     */
    private function getAssetsByType(array $filters = []): array
    {
        $query = Asset::query();

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        $data = $query->selectRaw('asset_type, COUNT(*) as count')
            ->groupBy('asset_type')
            ->get();

        return $data->map(function ($item) {
            return [
                'type' => ucfirst($item->asset_type),
                'count' => $item->count,
            ];
        })->toArray();
    }

    /**
     * Get alerts grouped by severity
     */
    private function getAlertsBySeverity(array $filters = []): array
    {
        $query = Alert::query();

        if (isset($filters['department_id'])) {
            $query->whereHas('asset', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        $data = $query->selectRaw('severity, COUNT(*) as count')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->groupBy('severity')
            ->get();

        return $data->map(function ($item) {
            return [
                'severity' => strtoupper($item->severity),
                'count' => $item->count,
            ];
        })->toArray();
    }

    /**
     * Get assets grouped by department
     */
    private function getAssetsByDepartment(): array
    {
        $data = Department::withCount('assets')->get();

        return $data->map(function ($dept) {
            return [
                'department' => $dept->name,
                'count' => $dept->assets_count,
            ];
        })->toArray();
    }

    /**
     * Get asset value trend (last 12 months)
     */
    private function getAssetValueTrend(array $filters = []): array
    {
        $trend = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');

            $query = AssetValue::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year);

            if (isset($filters['department_id'])) {
                $query->whereHas('asset', function ($q) use ($filters) {
                    $q->where('department_id', $filters['department_id']);
                });
            }

            $value = (float) $query->sum('current_value');
            $trend[] = [
                'month' => $month,
                'value' => $value,
            ];
        }

        return $trend;
    }

    /**
     * Get alert trend (last 30 days)
     */
    private function getAlertTrend(array $filters = []): array
    {
        $trend = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();

            $query = Alert::whereDate('created_at', $date);

            if (isset($filters['department_id'])) {
                $query->whereHas('asset', function ($q) use ($filters) {
                    $q->where('department_id', $filters['department_id']);
                });
            }

            $count = $query->count();
            $trend[] = [
                'date' => $date,
                'count' => $count,
            ];
        }

        return $trend;
    }

    /**
     * Get health summary for assets
     */
    public function getAssetHealthSummary(array $filters = []): array
    {
        $query = Asset::query();

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        $assets = $query->with(['latestLocation', 'alerts'])->get();

        return [
            'total' => $assets->count(),
            'with_active_alerts' => $assets->filter(fn ($a) => $a->alerts()->where('status', '!=', 'resolved')->exists())->count(),
            'with_locations' => $assets->filter(fn ($a) => $a->latestLocation)->count(),
            'maintenance_due' => $assets->filter(fn ($a) => $a->status === 'maintenance')->count(),
            'health_percentage' => $this->calculateHealthPercentage($assets),
        ];
    }

    /**
     * Calculate overall health percentage
     */
    private function calculateHealthPercentage(Collection $assets): int
    {
        if ($assets->isEmpty()) {
            return 100;
        }

        $healthy = $assets->filter(function ($asset) {
            $hasNoAlerts = !$asset->alerts()->where('status', '!=', 'resolved')->exists();
            $isActive = $asset->status === 'active';
            return $hasNoAlerts && $isActive;
        })->count();

        return (int) (($healthy / $assets->count()) * 100);
    }
}
