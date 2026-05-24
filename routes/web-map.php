<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\MapController;

// Map Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/map', [MapController::class, 'index'])->name('map.index');
    Route::get('/map/asset/{asset}', [MapController::class, 'showAsset'])->name('map.asset');
    Route::get('/map/geofence/{geofence}', [MapController::class, 'showGeofence'])->name('map.geofence');
});
