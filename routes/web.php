<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\AssetCategoryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/');
    })
        ->middleware('redirect.role')
        ->name('dashboard');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware([
            'role:admin',
            'permission:dashboard.admin.view',
        ])
        ->group(function () {
            Route::get('/dashboard', AdminDashboardController::class)
                ->name('dashboard');
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


        });

    Route::prefix('manager')
        ->name('manager.')
        ->middleware([
            'role:asset_manager',
            'permission:dashboard.manager.view',
        ])
        ->group(function () {
            Route::get('/dashboard', ManagerDashboardController::class)
                ->name('dashboard');
        });
});