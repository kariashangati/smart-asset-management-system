<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    /**
     * Get dashboard metrics
     * GET /api/admin/dashboard/metrics
     */
    public function metrics(): JsonResponse
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $totalAssets = \App\Models\Asset::count();
        $activeAssets = \App\Models\Asset::where('status', 'active')->count();
        $totalAlerts = \App\Models\Alert::count();
        $unresolvedAlerts = \App\Models\Alert::where('status', '!=', 'resolved')->count();
        $totalUsers = \App\Models\User::count();
        $totalDepartments = \App\Models\Department::count();
        $totalAssetValue = \App\Models\Asset::sum('asset_value');

        $alertsTrend = \App\Models\Alert::selectRaw('DATE(triggered_at) as date, COUNT(*) as count')
            ->where('triggered_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->get();

        $topAssets = \App\Models\Asset::withCount('alerts')
            ->orderBy('alerts_count', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'alerts_count']);

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_assets' => $totalAssets,
                    'active_assets' => $activeAssets,
                    'total_alerts' => $totalAlerts,
                    'unresolved_alerts' => $unresolvedAlerts,
                    'total_users' => $totalUsers,
                    'total_departments' => $totalDepartments,
                    'total_asset_value' => $totalAssetValue,
                ],
                'trends' => [
                    'alerts_30_days' => $alertsTrend,
                ],
                'top_assets_by_alerts' => $topAssets,
            ],
        ]);
    }

    /**
     * Get system health status
     * GET /api/admin/dashboard/health
     */
    public function systemHealth(): JsonResponse
    {
        if (!auth()->user()?->isAdmin()) {
            abort(403, 'Unauthorized');
        }

        $queueSize = \Illuminate\Support\Facades\DB::table('jobs')->count();
        $failedJobs = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
        $databaseStatus = 'connected';
        $redisStatus = 'connected';

        try {
            \Illuminate\Support\Facades\Cache::get('test');
        } catch (\Exception $e) {
            $redisStatus = 'disconnected';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'database' => $databaseStatus,
                'redis' => $redisStatus,
                'queue_jobs' => $queueSize,
                'failed_jobs' => $failedJobs,
                'timestamp' => now(),
            ],
        ]);
    }
}
