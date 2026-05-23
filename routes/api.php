<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;

Route::get('/health', [LocationController::class, 'health'])
    ->name('health');

/*
|--------------------------------------------------------------------------
| Location Tracking API Routes (For GPS Hardware Devices)
|--------------------------------------------------------------------------
| These endpoints are for embedded devices (ESP32/SIM800L) to send
| GPS location data to the system without authentication
|
| Device Authentication: API Token Hash (stored in tracker_devices table)
|
| Rate Limit: 60 requests per minute per device
*/
Route::prefix('tracker')
    ->name('tracker.')
    ->middleware(['api', 'throttle:60,1'])
    ->group(function () {
        /**
         * POST /api/tracker/location
         * 
         * Receives GPS location from hardware device and:
         * - Stores location log
         * - Updates device status
         * - Checks geofence violations
         * - Creates alerts automatically
         * 
         * Request JSON:
         * {
         *     "api_token_hash": "device_token_hash",
         *     "latitude": 37.7749,
         *     "longitude": -122.4194,
         *     "speed": 25.5,
         *     "motion_detected": true,
         *     "battery_level": 85
         * }
         */
        Route::post('/location', [LocationController::class, 'store'])
            ->name('location.store');
    });
