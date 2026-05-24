<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Get dashboard metrics
     * GET /api/reports/dashboard
     */
    public function getDashboardMetrics(): JsonResponse
    {
        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 'active')->count();
        $assetsWithAlerts = Asset::whereHas('alerts')->count();
        $unreadAlerts = Alert::where('status', 'unread')->count();
        $highSeverityAlerts = Alert::where('severity', 'high')->where('status', '!=', 'resolved')->count();
        $totalUsers = User::count();
        $totalDepartments = \App\Models\Department::count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_assets' => $totalAssets,
                'active_assets' => $activeAssets,
                'inactive_assets' => $totalAssets - $activeAssets,
                'assets_with_alerts' => $assetsWithAlerts,
                'unread_alerts' => $unreadAlerts,
                'high_severity_alerts' => $highSeverityAlerts,
                'total_users' => $totalUsers,
                'total_departments' => $totalDepartments,
            ],
        ]);
    }

    /**
     * Get asset value report
     * GET /api/reports/asset-values
     */
    public function getAssetValueReport(): JsonResponse
    {
        $assets = Asset::with('assetValue')
            ->get()
            ->map(function (Asset $asset) {
                $value = $asset->assetValue;
                return [
                    'id' => $asset->id,
                    'name' => $asset->name,
                    'purchase_price' => $value->purchase_price ?? 0,
                    'current_value' => $value->current_value ?? 0,
                    'depreciation_rate' => $value->depreciation_rate ?? 0,
                ];
            });

        $totalPurchaseValue = $assets->sum('purchase_price');
        $totalCurrentValue = $assets->sum('current_value');
        $totalDepreciation = $totalPurchaseValue - $totalCurrentValue;

        return response()->json([
            'success' => true,
            'data' => $assets,
            'summary' => [
                'total_purchase_value' => $totalPurchaseValue,
                'total_current_value' => $totalCurrentValue,
                'total_depreciation' => $totalDepreciation,
                'depreciation_percentage' => ($totalPurchaseValue > 0) ? round(($totalDepreciation / $totalPurchaseValue) * 100, 2) : 0,
            ],
        ]);
    }

    /**
     * Get alerts report
     * GET /api/reports/alerts
     */
    public function getAlertsReport(): JsonResponse
    {
        $alerts = Alert::with('asset')
            ->get()
            ->groupBy(function ($alert) {
                return $alert->triggered_at->format('Y-m-d');
            });

        $bySeverity = Alert::groupBy('severity')
            ->selectRaw('severity, COUNT(*) as count')
            ->get();

        $byType = Alert::groupBy('alert_type')
            ->selectRaw('alert_type, COUNT(*) as count')
            ->get();

        return response()->json([
            'success' => true,
            'alerts_by_date' => $alerts->map(fn($items) => $items->count()),
            'by_severity' => $bySeverity,
            'by_type' => $byType,
        ]);
    }
}
