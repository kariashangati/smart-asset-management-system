<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Alert;
use App\Models\LocationLog;
use App\Services\AlertService;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function assets()
    {
        $assets = Asset::with(['category', 'department', 'activeAssignment.trackerDevice'])->get();
        return view('admin.reports.assets', compact('assets'));
    }

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
}