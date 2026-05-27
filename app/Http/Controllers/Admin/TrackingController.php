<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Department;
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
        $query = Asset::with(['activeAssignment.trackerDevice', 'latestLocation', 'category', 'department']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
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
        $departments = Department::all();
        $categories = AssetCategory::all();

        return view('admin.tracking.live-map', compact('assets', 'departments', 'categories'));
    }

    public function history()
    {
        $assets = Asset::has('locationLogs')->get();
        return view('admin.tracking.history', compact('assets'));
    }

    public function assetHistory(Asset $asset)
    {
        $logs = $this->locationService->getLocationHistory($asset->id);
        return view('admin.tracking.asset-history', compact('asset', 'logs'));
    }
}