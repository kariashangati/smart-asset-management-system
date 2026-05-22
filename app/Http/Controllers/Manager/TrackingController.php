<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Services\LocationService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function liveMap()
    {
        // Manager sees only assets in their department? For simplicity, all assets.
        $assets = Asset::with('activeAssignment.trackerDevice', 'latestLocation')->get();
        return view('manager.tracking.live-map', compact('assets'));
    }

    public function history()
    {
        $assets = Asset::has('locationLogs')->get();
        return view('manager.tracking.history', compact('assets'));
    }

    public function assetHistory(Asset $asset)
    {
        $logs = $this->locationService->getLocationHistory($asset->id);
        return view('manager.tracking.asset-history', compact('asset', 'logs'));
    }
}