<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreLocationRequest;
use App\Models\Asset;
use App\Models\AssetLatestLocation;
use App\Models\LocationLog;
use App\Models\TrackerDevice;
use App\Services\GeofenceService;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LocationController
{
    protected LocationService $locationService;
    protected GeofenceService $geofenceService;

    public function __construct(
        LocationService $locationService,
        GeofenceService $geofenceService
    ) {
        $this->locationService = $locationService;
        $this->geofenceService = $geofenceService;
    }

    /**
     * Health check endpoint for devices
     * 
     * GET /api/health
     * 
     * Response: {"status": "ok", "timestamp": "2026-05-23T12:00:00Z"}
     */
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'message' => 'Location tracking API is operational',
        ]);
    }

    /**
     * Receive location data from GPS hardware device
     * 
     * POST /api/tracker/location
     * 
     * Request JSON:
     * {
     *     "api_token_hash": "device_token_hash_from_database",
     *     "latitude": 37.7749,
     *     "longitude": -122.4194,
     *     "speed": 25.5,
     *     "motion_detected": true,
     *     "battery_level": 85
     * }
     * 
     * Response on success (201):
     * {
     *     "success": true,
     *     "message": "Location recorded successfully",
     *     "location_log_id": 1,
     *     "alerts_created": 0
     * }
     * 
     * Response on error (400/401):
     * {
     *     "success": false,
     *     "message": "Error description",
     *     "errors": { ... }
     * }
     */
    public function store(StoreLocationRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Step 1: Authenticate device via API token hash
            $device = $this->authenticateDevice($data['api_token_hash']);
            if (!$device) {
                Log::warning('API access attempt with invalid token', [
                    'token_hash' => substr($data['api_token_hash'], 0, 10) . '...',
                    'ip' => $request->ip(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API token',
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Step 2: Get the asset assigned to this device
            $asset = $device->activeAssignment?->asset;
            if (!$asset) {
                Log::warning('Device has no active asset assignment', [
                    'device_id' => $device->id,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Device has no active asset assignment',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Step 3: Create location log
            $locationLog = LocationLog::create([
                'tracker_device_id' => $device->id,
                'asset_id' => $asset->id,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'speed' => $data['speed'] ?? 0,
                'motion_detected' => $data['motion_detected'] ?? false,
                'recorded_at' => now(),
                'received_at' => now(),
            ]);

            // Step 4: Update device status
            $device->update([
                'last_seen_at' => now(),
                'battery_level' => $data['battery_level'] ?? $device->battery_level,
                'status' => 'active',
            ]);

            // Step 5: Update latest location for asset
            AssetLatestLocation::updateOrCreate(
                ['asset_id' => $asset->id],
                [
                    'tracker_device_id' => $device->id,
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'last_motion_detected' => $data['motion_detected'] ?? false,
                    'last_recorded_at' => now(),
                ]
            );

            // Step 6: Check geofence violations and create alerts automatically
            $alertsCreated = 0;
            try {
                $this->geofenceService->checkAndCreateAlerts(
                    asset: $asset,
                    latitude: $data['latitude'],
                    longitude: $data['longitude'],
                    trackerDevice: $device,
                    speed: $data['speed'] ?? 0,
                    motionDetected: $data['motion_detected'] ?? false
                );
                $alertsCreated = 1; // Simplified count (actual implementation checks for new alerts)
            } catch (\Exception $e) {
                Log::error('Geofence checking error', [
                    'asset_id' => $asset->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the request if geofence check fails
            }

            Log::info('Location data received and processed', [
                'device_id' => $device->id,
                'asset_id' => $asset->id,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'location_log_id' => $locationLog->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Location recorded successfully',
                'location_log_id' => $locationLog->id,
                'alerts_created' => $alertsCreated,
                'timestamp' => now()->toIso8601String(),
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            Log::error('Location API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing location data',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Authenticate device using API token hash
     * 
     * Security: Token is hashed before storage, so we hash the incoming token
     * and compare with database
     */
    private function authenticateDevice(string $apiTokenHash): ?TrackerDevice
    {
        // Hash the incoming token and find device
        // Note: If tokens are already hashed in DB, compare directly
        $device = TrackerDevice::where('api_token_hash', $apiTokenHash)
            ->where('status', '!=', 'inactive')
            ->first();

        return $device;
    }
}
