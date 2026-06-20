<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAssetLocationUpdate;
use App\Models\Asset;
use App\Models\AssetDeviceAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * Handle tracker device webhook for location updates
     * POST /api/webhooks/location
     */
    public function handleLocationWebhook(Request $request): JsonResponse
    {
        $request->validate([
            'tracker_device_id' => 'required|integer|exists:tracker_devices,id',
            'asset_id' => 'nullable|integer|exists:assets,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
            'motion_detected' => 'nullable|boolean',
            'timestamp' => 'nullable|integer',
        ]);

        $assetId = $request->asset_id;

        /**
         * FIX (audit #3): the old code did
         *   Asset::where('tracker_device_id', $request->tracker_device_id)->first();
         * which looks at a column the real admin UI never populates
         * (assignment happens through the AssetDeviceAssignment pivot via
         * DeviceAssignmentController, not a direct FK on assets).
         *
         * The correct, currently-active assignment for a device is found on
         * the pivot itself: asset_device_assignments where
         * tracker_device_id = X and is_active = true.
         */
        if (!$assetId) {
            $activeAssignment = AssetDeviceAssignment::where('tracker_device_id', $request->tracker_device_id)
                ->where('is_active', true)
                ->first();

            $assetId = $activeAssignment?->asset_id;
        }

        if (!$assetId) {
            return response()->json([
                'success' => false,
                'message' => 'No asset is currently assigned to this tracker device. Assign the device to an asset first.',
            ], 404);
        }

        // Queue the location update processing
        ProcessAssetLocationUpdate::dispatch(
            assetId: $assetId,
            trackerDeviceId: $request->tracker_device_id,
            latitude: (float) $request->latitude,
            longitude: (float) $request->longitude,
            speed: (float) ($request->speed ?? 0),
            motionDetected: (bool) ($request->motion_detected ?? false)
        );

        return response()->json([
            'success' => true,
            'message' => 'Location update received and queued for processing',
        ], 202);
    }

    /**
     * Handle tracker device webhook for alert notifications
     * POST /api/webhooks/alert
     */
    public function handleAlertWebhook(Request $request): JsonResponse
    {
        $request->validate([
            'tracker_device_id' => 'required|integer|exists:tracker_devices,id',
            'asset_id' => 'required|integer|exists:assets,id',
            'alert_type' => 'required|string',
            'severity' => 'required|string|in:low,medium,high',
            'message' => 'required|string|max:1000',
        ]);

        // Queue alert creation
        \App\Jobs\CreateAlertJob::dispatch([
            'asset_id' => $request->asset_id,
            'tracker_device_id' => $request->tracker_device_id,
            'alert_type' => $request->alert_type,
            'severity' => $request->severity,
            'title' => ucwords(str_replace('_', ' ', $request->alert_type)),
            'message' => $request->message,
            'triggered_at' => now(),
            'status' => 'unread',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alert received and queued for processing',
        ], 202);
    }

    /**
     * Health check endpoint
     * GET /api/webhooks/health
     */
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
        ]);
    }
}
