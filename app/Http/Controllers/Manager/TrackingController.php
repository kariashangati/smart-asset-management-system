<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Services\LocationService;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function liveMap(Request $request)
    {
        $departmentId = auth()->user()->department_id;
        
        $query = Asset::where('department_id', $departmentId)
            ->with(['activeAssignment.trackerDevice', 'latestLocation', 'category']);

        if ($request->filled('category_id')) {
            $query->where('asset_category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->boolean('actual')) {
            $query->has('latestLocation');
        }

        $assets = $query->get();
        $categories = AssetCategory::all();

        return view('manager.tracking.live-map', compact('assets', 'categories'));
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