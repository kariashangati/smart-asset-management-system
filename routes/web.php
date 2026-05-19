<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
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