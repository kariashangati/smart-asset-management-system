<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssetsExport;
use App\Exports\AlertsExport;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService)
    {
    }

    /**
     * Get asset summary report
     * GET /api/reports/assets
     */
    public function assetSummary(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Asset::class);

        $filters = $request->only('department_id', 'status', 'asset_type');
        $report = $this->reportService->getAssetSummaryReport($filters);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Get alerts report
     * GET /api/reports/alerts
     */
    public function alertsReport(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Alert::class);

        $filters = $request->only('date_from', 'date_to', 'severity', 'status', 'department_id');
        $report = $this->reportService->getAlertsReport($filters);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Get tracking report
     * GET /api/reports/tracking
     */
    public function trackingReport(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Asset::class);

        $filters = $request->only('department_id', 'date_from', 'date_to');
        $report = $this->reportService->getTrackingReport($filters);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Get asset values report
     * GET /api/reports/asset-values
     */
    public function assetValuesReport(Request $request): JsonResponse
    {
        $this->authorize('viewAny', \App\Models\AssetValue::class);

        $filters = $request->only('department_id');
        $report = $this->reportService->getAssetValuesReport($filters);

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Export assets to PDF
     * GET /api/reports/export/pdf
     */
    public function exportAssetsPdf(Request $request): Response
    {
        $this->authorize('viewAny', \App\Models\Asset::class);

        $filters = $request->only('department_id', 'status');
        $report = $this->reportService->getAssetSummaryReport($filters);

        $pdf = Pdf::loadView('reports.assets-pdf', ['report' => $report]);
        return $pdf->download('assets-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export assets to CSV
     * GET /api/reports/export/csv
     */
    public function exportAssetsCsv(Request $request): Response
    {
        $this->authorize('viewAny', \App\Models\Asset::class);

        $filters = $request->only('department_id', 'status');

        return Excel::download(new AssetsExport($filters), 'assets-report-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Export alerts to PDF
     * GET /api/reports/export/alerts-pdf
     */
    public function exportAlertsPdf(Request $request): Response
    {
        $this->authorize('viewAny', \App\Models\Alert::class);

        $filters = $request->only('date_from', 'date_to', 'severity');
        $report = $this->reportService->getAlertsReport($filters);

        $pdf = Pdf::loadView('reports.alerts-pdf', ['report' => $report]);
        return $pdf->download('alerts-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
