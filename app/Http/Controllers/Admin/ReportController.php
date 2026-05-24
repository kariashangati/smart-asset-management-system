<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Alert;
use App\Models\LocationLog;
use App\Services\AlertService;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display assets report
     */
    public function assets()
    {
        $assets = Asset::with(['category', 'department', 'activeAssignment.trackerDevice'])->get();
        return view('admin.reports.assets', compact('assets'));
    }

    /**
     * Export assets report as PDF
     */
    public function assetsPdf(Request $request)
    {
        $this->authorize('viewAny', Asset::class);

        $assets = Asset::with(['category', 'department', 'activeAssignment.trackerDevice'])
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->get();

        $html = View::make('reports.assets-pdf', [
            'assets' => $assets,
            'generatedAt' => now(),
        ])->render();

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('assets-report-' . now()->format('Y-m-d-His') . '.pdf');
    }

    /**
     * Display tracking report
     */
    public function tracking(Request $request)
    {
        $assetId = $request->get('asset_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $logs = LocationLog::with('asset', 'trackerDevice')
            ->when($assetId, fn($q) => $q->where('asset_id', $assetId))
            ->when($dateFrom, fn($q) => $q->whereDate('recorded_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('recorded_at', '<=', $dateTo))
            ->orderBy('recorded_at', 'desc')
            ->paginate(50);

        $assets = Asset::has('locationLogs')->get();
        
        return view('admin.reports.tracking', compact('logs', 'assets', 'assetId', 'dateFrom', 'dateTo'));
    }

    /**
     * Export tracking report as PDF
     */
    public function trackingPdf(Request $request)
    {
        $this->authorize('viewAny', Asset::class);

        $assetId = $request->get('asset_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $logs = LocationLog::with('asset', 'trackerDevice')
            ->when($assetId, fn($q) => $q->where('asset_id', $assetId))
            ->when($dateFrom, fn($q) => $q->whereDate('recorded_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('recorded_at', '<=', $dateTo))
            ->orderBy('recorded_at', 'desc')
            ->limit(1000)
            ->get();

        $html = View::make('reports.tracking-pdf', [
            'logs' => $logs,
            'generatedAt' => now(),
        ])->render();

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('tracking-report-' . now()->format('Y-m-d-His') . '.pdf');
    }

    /**
     * Display alerts report
     */
    public function alerts(Request $request)
    {
        $severity = $request->get('severity');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $alerts = Alert::with('asset')
            ->when($severity, fn($q) => $q->where('severity', $severity))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($dateFrom, fn($q) => $q->whereDate('triggered_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('triggered_at', '<=', $dateTo))
            ->orderBy('triggered_at', 'desc')
            ->paginate(50);

        return view('admin.reports.alerts', compact('alerts', 'severity', 'status', 'dateFrom', 'dateTo'));
    }

    /**
     * Export alerts report as PDF
     */
    public function alertsPdf(Request $request)
    {
        $this->authorize('viewAny', Alert::class);

        $severity = $request->get('severity');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $alerts = Alert::with('asset')
            ->when($severity, fn($q) => $q->where('severity', $severity))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($dateFrom, fn($q) => $q->whereDate('triggered_at', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('triggered_at', '<=', $dateTo))
            ->orderBy('triggered_at', 'desc')
            ->limit(1000)
            ->get();

        $html = View::make('reports.alerts-pdf', [
            'alerts' => $alerts,
            'generatedAt' => now(),
        ])->render();

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('alerts-report-' . now()->format('Y-m-d-His') . '.pdf');
    }
}
