<?php

namespace App\Http\Controllers\Api;

use App\Models\Asset;
use App\Models\Alert;
use App\Models\LocationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Get asset summary report
     * GET /api/reports/assets
     */
    public function assetSummary(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Asset::class);

        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 'active')->count();
        $totalValue = Asset::sum('asset_value');
        $assetsByType = Asset::groupBy('asset_type')->selectRaw('asset_type, COUNT(*) as count')->get();
        $assetsByStatus = Asset::groupBy('status')->selectRaw('status, COUNT(*) as count')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_assets' => $totalAssets,
                'active_assets' => $activeAssets,
                'total_asset_value' => $totalValue,
                'by_type' => $assetsByType,
                'by_status' => $assetsByStatus,
            ],
        ]);
    }

    /**
     * Get alerts report
     * GET /api/reports/alerts
     */
    public function alertsReport(Request $request): JsonResponse
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'asset_id' => 'nullable|exists:assets,id',
        ]);

        $query = Alert::query();

        if ($request->from) {
            $query->where('triggered_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->where('triggered_at', '<=', $request->to);
        }
        if ($request->asset_id) {
            $query->where('asset_id', $request->asset_id);
        }

        $totalAlerts = $query->count();
        $unresolvedAlerts = $query->where('status', '!=', 'resolved')->count();
        $alertsBySeverity = $query->selectRaw('severity, COUNT(*) as count')->groupBy('severity')->get();
        $alertsByType = $query->selectRaw('alert_type, COUNT(*) as count')->groupBy('alert_type')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_alerts' => $totalAlerts,
                'unresolved_alerts' => $unresolvedAlerts,
                'by_severity' => $alertsBySeverity,
                'by_type' => $alertsByType,
            ],
        ]);
    }

    /**
     * Get location tracking report
     * GET /api/reports/tracking
     */
    public function trackingReport(Request $request): JsonResponse
    {
        $request->validate([
            'asset_id' => 'nullable|exists:assets,id',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
        ]);

        $query = LocationLog::query();

        if ($request->asset_id) {
            $query->where('asset_id', $request->asset_id);
        }
        if ($request->from) {
            $query->where('recorded_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->where('recorded_at', '<=', $request->to);
        }

        $totalLogs = $query->count();
        $avgSpeed = $query->avg('speed');
        $motionDetected = $query->where('motion_detected', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_location_logs' => $totalLogs,
                'average_speed' => round($avgSpeed ?? 0, 2),
                'motion_detected_count' => $motionDetected,
            ],
        ]);
    }

    /**
     * Export assets report as PDF
     * GET /api/reports/assets/export/pdf
     */
    public function exportAssetsPdf(): \Illuminate\Http\Response
    {
        $this->authorize('viewAny', Asset::class);

        $assets = Asset::with('department', 'trackerDevice')->get();

        $pdf = Pdf::loadView('reports.assets', ['assets' => $assets]);

        return $pdf->download('assets-report.pdf');
    }

    /**
     * Export assets report as CSV
     * GET /api/reports/assets/export/csv
     */
    public function exportAssetsCsv()
    {
        $this->authorize('viewAny', Asset::class);

        return Excel::download(
            new \App\Exports\AssetsExport(),
            'assets-report.csv'
        );
    }

    /**
     * Export alerts report as PDF
     * GET /api/reports/alerts/export/pdf
     */
    public function exportAlertsPdf(Request $request): \Illuminate\Http\Response
    {
        $query = Alert::with('asset');

        if ($request->from) {
            $query->where('triggered_at', '>=', $request->from);
        }
        if ($request->to) {
            $query->where('triggered_at', '<=', $request->to);
        }

        $alerts = $query->get();

        $pdf = Pdf::loadView('reports.alerts', ['alerts' => $alerts]);

        return $pdf->download('alerts-report.pdf');
    }
}
