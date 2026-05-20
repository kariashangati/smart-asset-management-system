<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Department;
use App\Models\TrackerDevice;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalAssets' => Asset::count(),
            'totalDevices' => TrackerDevice::count(),
            'totalDepartments' => Department::count(),
            'totalCategories' => AssetCategory::count(),
            'totalUsers' => User::count(),
            'activeAlerts' => Alert::where('status', 'unread')->count(),

            'activeAssets' => Asset::where('status', 'active')->count(),
            'missingAssets' => Asset::where('status', 'missing')->count(),
            'maintenanceAssets' => Asset::where('status', 'maintenance')->count(),

            'recentAssets' => Asset::latest()->take(5)->get(),
            'recentUsers' => User::latest()->take(5)->get(),
            'recentAlerts' => Alert::latest()->take(5)->get(),
        ]);
    }
}