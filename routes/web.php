<?php

use Illuminate\Support\Facades\Route;

// Dashboard Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;

// Admin Controllers
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\AssetCategoryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\TrackerDeviceController;
use App\Http\Controllers\Admin\DeviceAssignmentController;
use App\Http\Controllers\Admin\GeofenceController as AdminGeofenceController;
use App\Http\Controllers\Admin\AlertController as AdminAlertController;

// Manager Controllers
use App\Http\Controllers\Manager\GeofenceController as ManagerGeofenceController;
use App\Http\Controllers\Manager\AlertController as ManagerAlertController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'active.user'])->group(function () {

    Route::get('/dashboard', function () {
        return redirect('/');
    })
        ->middleware('redirect.role')
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->name('admin.')
        ->middleware([
            'role:admin',
            'permission:dashboard.admin.view',
        ])
        ->group(function () {

            Route::get('/dashboard', AdminDashboardController::class)
                ->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | UI Test Routes
            |--------------------------------------------------------------------------
            */
            Route::post('/ui-test/flash', function () {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('success', 'SweetAlert2 success notifications are working correctly.');
            })->name('ui-test.flash');

            Route::delete('/ui-test/delete', function () {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('success', 'Delete confirmation modal worked. No real data was deleted.');
            })->name('ui-test.delete');

            /*
            |--------------------------------------------------------------------------
            | Departments
            |--------------------------------------------------------------------------
            */
            Route::get('/departments', [DepartmentController::class, 'index'])
                ->middleware('permission:departments.view')
                ->name('departments.index');

            Route::get('/departments/create', [DepartmentController::class, 'create'])
                ->middleware('permission:departments.create')
                ->name('departments.create');

            Route::post('/departments', [DepartmentController::class, 'store'])
                ->middleware('permission:departments.create')
                ->name('departments.store');

            Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])
                ->middleware('permission:departments.update')
                ->name('departments.edit');

            Route::put('/departments/{department}', [DepartmentController::class, 'update'])
                ->middleware('permission:departments.update')
                ->name('departments.update');

            Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])
                ->middleware('permission:departments.delete')
                ->name('departments.destroy');

            /*
            |--------------------------------------------------------------------------
            | Asset Categories
            |--------------------------------------------------------------------------
            */
            Route::get('/asset-categories', [AssetCategoryController::class, 'index'])
                ->middleware('permission:asset_categories.view')
                ->name('asset-categories.index');

            Route::get('/asset-categories/create', [AssetCategoryController::class, 'create'])
                ->middleware('permission:asset_categories.create')
                ->name('asset-categories.create');

            Route::post('/asset-categories', [AssetCategoryController::class, 'store'])
                ->middleware('permission:asset_categories.create')
                ->name('asset-categories.store');

            Route::get('/asset-categories/{assetCategory}/edit', [AssetCategoryController::class, 'edit'])
                ->middleware('permission:asset_categories.update')
                ->name('asset-categories.edit');

            Route::put('/asset-categories/{assetCategory}', [AssetCategoryController::class, 'update'])
                ->middleware('permission:asset_categories.update')
                ->name('asset-categories.update');

            Route::delete('/asset-categories/{assetCategory}', [AssetCategoryController::class, 'destroy'])
                ->middleware('permission:asset_categories.delete')
                ->name('asset-categories.destroy');

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */
            Route::get('/users', [UserController::class, 'index'])
                ->middleware('permission:users.view')
                ->name('users.index');

            Route::post('/users', [UserController::class, 'store'])
                ->middleware('permission:users.create')
                ->name('users.store');

            Route::get('/users/{user}', [UserController::class, 'show'])
                ->middleware('permission:users.view')
                ->name('users.show');

            Route::put('/users/{user}', [UserController::class, 'update'])
                ->middleware('permission:users.update')
                ->name('users.update');

            Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus'])
                ->middleware('permission:users.update')
                ->name('users.toggle-status');

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */
            Route::get('/roles', [RoleController::class, 'index'])
                ->middleware('permission:roles.view')
                ->name('roles.index');

            Route::post('/roles', [RoleController::class, 'store'])
                ->middleware('permission:roles.create')
                ->name('roles.store');

            Route::put('/roles/{role}', [RoleController::class, 'update'])
                ->middleware('permission:roles.update')
                ->name('roles.update');

            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
                ->middleware('permission:roles.delete')
                ->name('roles.destroy');

            /*
            |--------------------------------------------------------------------------
            | Assets
            |--------------------------------------------------------------------------
            */
            Route::get('/assets', [AssetController::class, 'index'])
                ->middleware('permission:assets.view')
                ->name('assets.index');

            Route::get('/assets/create', [AssetController::class, 'create'])
                ->middleware('permission:assets.create')
                ->name('assets.create');

            Route::post('/assets', [AssetController::class, 'store'])
                ->middleware('permission:assets.create')
                ->name('assets.store');

            Route::get('/assets/{asset}', [AssetController::class, 'show'])
                ->middleware('permission:assets.view')
                ->name('assets.show');

            Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])
                ->middleware('permission:assets.update')
                ->name('assets.edit');

            Route::put('/assets/{asset}', [AssetController::class, 'update'])
                ->middleware('permission:assets.update')
                ->name('assets.update');

            Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])
                ->middleware('permission:assets.delete')
                ->name('assets.destroy');

            /*
            |--------------------------------------------------------------------------
            | Devices
            |--------------------------------------------------------------------------
            */
            Route::get('/devices', [TrackerDeviceController::class, 'index'])
                ->middleware('permission:devices.view')
                ->name('devices.index');

            Route::get('/devices/create', [TrackerDeviceController::class, 'create'])
                ->middleware('permission:devices.create')
                ->name('devices.create');

            Route::post('/devices', [TrackerDeviceController::class, 'store'])
                ->middleware('permission:devices.create')
                ->name('devices.store');

            Route::get('/devices/{trackerDevice}', [TrackerDeviceController::class, 'show'])
                ->middleware('permission:devices.view')
                ->name('devices.show');

            Route::get('/devices/{trackerDevice}/edit', [TrackerDeviceController::class, 'edit'])
                ->middleware('permission:devices.update')
                ->name('devices.edit');

            Route::put('/devices/{trackerDevice}', [TrackerDeviceController::class, 'update'])
                ->middleware('permission:devices.update')
                ->name('devices.update');

            Route::delete('/devices/{trackerDevice}', [TrackerDeviceController::class, 'destroy'])
                ->middleware('permission:devices.delete')
                ->name('devices.destroy');

            /*
            |--------------------------------------------------------------------------
            | Assignments
            |--------------------------------------------------------------------------
            */
            Route::get('/assignments', [DeviceAssignmentController::class, 'index'])
                ->middleware('permission:assignments.view')
                ->name('assignments.index');

            Route::get('/assignments/create', [DeviceAssignmentController::class, 'create'])
                ->middleware('permission:assignments.create')
                ->name('assignments.create');

            Route::post('/assignments', [DeviceAssignmentController::class, 'store'])
                ->middleware('permission:assignments.create')
                ->name('assignments.store');

            Route::delete('/assignments/{assignment}', [DeviceAssignmentController::class, 'destroy'])
                ->middleware('permission:assignments.delete')
                ->name('assignments.destroy');

            /*
            |--------------------------------------------------------------------------
            | Geofences (Admin)
            |--------------------------------------------------------------------------
            */
            Route::get('/geofences', [AdminGeofenceController::class, 'index'])
                ->middleware('permission:geofences.view')
                ->name('geofences.index');

            Route::get('/geofences/create', [AdminGeofenceController::class, 'create'])
                ->middleware('permission:geofences.create')
                ->name('geofences.create');

            Route::post('/geofences', [AdminGeofenceController::class, 'store'])
                ->middleware('permission:geofences.create')
                ->name('geofences.store');

            Route::get('/geofences/{geofence}/edit', [AdminGeofenceController::class, 'edit'])
                ->middleware('permission:geofences.update')
                ->name('geofences.edit');

            Route::put('/geofences/{geofence}', [AdminGeofenceController::class, 'update'])
                ->middleware('permission:geofences.update')
                ->name('geofences.update');

            Route::delete('/geofences/{geofence}', [AdminGeofenceController::class, 'destroy'])
                ->middleware('permission:geofences.delete')
                ->name('geofences.destroy');

            /*
            |--------------------------------------------------------------------------
            | Alerts (Admin)
            |--------------------------------------------------------------------------
            */
            Route::prefix('alerts')->name('alerts.')->group(function () {

                Route::get('/', [AdminAlertController::class, 'index'])
                    ->name('index');

                Route::get('/{alert}', [AdminAlertController::class, 'show'])
                    ->name('show');

                Route::patch('/{alert}/mark-read', [AdminAlertController::class, 'markAsRead'])
                    ->name('mark-read');

                Route::patch('/{alert}/mark-resolved', [AdminAlertController::class, 'markAsResolved'])
                    ->name('mark-resolved');

                Route::delete('/{alert}', [AdminAlertController::class, 'destroy'])
                    ->name('destroy');
            });

            /*
            |--------------------------------------------------------------------------
            | Tracking (Admin)
            |--------------------------------------------------------------------------
            */
            Route::prefix('tracking')->name('tracking.')->group(function () {

                Route::get('/live-map', [App\Http\Controllers\Admin\TrackingController::class, 'liveMap'])
                    ->name('live-map');

                Route::get('/history', [App\Http\Controllers\Admin\TrackingController::class, 'history'])
                    ->name('history');

                Route::get('/asset-history/{asset}', [App\Http\Controllers\Admin\TrackingController::class, 'assetHistory'])
                    ->name('asset-history');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | Manager Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('manager')
        ->name('manager.')
        ->middleware([
            'role:asset_manager',
            'permission:dashboard.manager.view',
        ])
        ->group(function () {

            Route::get('/dashboard', ManagerDashboardController::class)
                ->name('dashboard');

            /*
            |--------------------------------------------------------------------------
            | Tracking (Manager)
            |--------------------------------------------------------------------------
            */
            Route::prefix('tracking')->name('tracking.')->group(function () {

                Route::get('/live-map', [App\Http\Controllers\Manager\TrackingController::class, 'liveMap'])
                    ->middleware('permission:tracking.live_map.view')
                    ->name('live-map');

                Route::get('/history', [App\Http\Controllers\Manager\TrackingController::class, 'history'])
                    ->middleware('permission:tracking.history.view')
                    ->name('history');

                Route::get('/asset-history/{asset}', [App\Http\Controllers\Manager\TrackingController::class, 'assetHistory'])
                    ->middleware('permission:tracking.history.view')
                    ->name('asset-history');
            });

            /*
            |--------------------------------------------------------------------------
            | Geofences (Manager)
            |--------------------------------------------------------------------------
            */
            Route::resource('geofences', ManagerGeofenceController::class)
                ->except(['show']);

            /*
            |--------------------------------------------------------------------------
            | Alerts (Manager)
            |--------------------------------------------------------------------------
            */
            Route::prefix('alerts')->name('alerts.')->group(function () {

                Route::get('/', [ManagerAlertController::class, 'index'])
                    ->name('index');

                Route::get('/{alert}', [ManagerAlertController::class, 'show'])
                    ->name('show');

                Route::patch('/{alert}/mark-read', [ManagerAlertController::class, 'markAsRead'])
                    ->name('mark-read');

                Route::patch('/{alert}/mark-resolved', [ManagerAlertController::class, 'markAsResolved'])
                    ->name('mark-resolved');

                Route::delete('/{alert}', [ManagerAlertController::class, 'destroy'])
                    ->name('destroy');
            });
        });
});