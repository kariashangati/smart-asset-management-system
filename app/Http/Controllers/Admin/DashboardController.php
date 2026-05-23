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
        $user = auth()->user();
        $isDepartmentManager = $user->isDepartmentManager();

        // Build asset queries with department filter if user is a manager
        $assetsQuery = Asset::query();
        $activeAssetsQuery = Asset::query();
        $missingAssetsQuery = Asset::query();
        $maintenanceAssetsQuery = Asset::query();
        $recentAssetsQuery = Asset::query();

        if ($isDepartmentManager) {
            $departmentId = $user->department_id;
            $assetsQuery->where('department_id', $departmentId);
            $activeAssetsQuery->where('department_id', $departmentId);
            $missingAssetsQuery->where('department_id', $departmentId);
            $maintenanceAssetsQuery->where('department_id', $departmentId);
            $recentAssetsQuery->where('department_id', $departmentId);
        }

        // Build alert queries with department filter if user is a manager
        $alertsQuery = Alert::query();
        $recentAlertsQuery = Alert::query();

        if ($isDepartmentManager) {
            $departmentId = $user->department_id;
            $alertsQuery->whereHas('asset', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
            $recentAlertsQuery->whereHas('asset', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });
        }

        return view('admin.dashboard', [
            'totalAssets' => $assetsQuery->count(),
            'totalDevices' => TrackerDevice::count(), // All devices visible to all users
            'totalDepartments' => $isDepartmentManager ? 1 : Department::count(),
            'totalCategories' => AssetCategory::count(), // All categories visible to all users
            'totalUsers' => User::count(), // All users visible to all users
            'activeAlerts' => $alertsQuery->where('status', 'unread')->count(),

            'activeAssets' => $activeAssetsQuery->where('status', 'active')->count(),
            'missingAssets' => $missingAssetsQuery->where('status', 'missing')->count(),
            'maintenanceAssets' => $maintenanceAssetsQuery->where('status', 'maintenance')->count(),

            'recentAssets' => $recentAssetsQuery->latest()->take(5)->get(),
            'recentUsers' => User::latest()->take(5)->get(), // All recent users visible
            'recentAlerts' => $recentAlertsQuery->latest()->take(5)->get(),

            // Additional data for managers
            'isDepartmentManager' => $isDepartmentManager,
            'userDepartment' => $isDepartmentManager ? $user->department : null,
        ]);
    }
}
