<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    /**
     * Get dashboard metrics
     * GET /api/dashboard/metrics
     */
    public function metrics(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Asset::class);

        $filters = $request->only('department_id', 'date_from', 'date_to');
        $metrics = $this->dashboardService->getMetrics($filters);

        return response()->json([
            'success' => true,
            'data' => $metrics,
        ]);
    }

    /**
     * Get dashboard charts data
     * GET /api/dashboard/charts
     */
    public function charts(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Asset::class);

        $filters = $request->only('department_id');
        $charts = $this->dashboardService->getChartData($filters);

        return response()->json([
            'success' => true,
            'data' => $charts,
        ]);
    }

    /**
     * Get asset health summary
     * GET /api/dashboard/health
     */
    public function health(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Asset::class);

        $filters = $request->only('department_id');
        $health = $this->dashboardService->getAssetHealthSummary($filters);

        return response()->json([
            'success' => true,
            'data' => $health,
        ]);
    }
}
