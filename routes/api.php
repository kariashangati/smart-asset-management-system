<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\GeofenceController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\CustomAlertRuleController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\UserManagementController;

// Public routes (no auth required)
Route::post('webhooks/location', [WebhookController::class, 'handleLocationWebhook']);
Route::post('webhooks/alert', [WebhookController::class, 'handleAlertWebhook']);
Route::get('webhooks/health', [WebhookController::class, 'health']);

// Password reset routes (no auth required)
Route::post('password/forgot', [PasswordResetController::class, 'sendResetLink']);
Route::post('password/reset', [PasswordResetController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {
    /**
     * Asset Routes
     */
    Route::apiResource('assets', AssetController::class);
    Route::get('assets/department/{departmentId}', [AssetController::class, 'getByDepartment']);
    Route::get('assets/{asset}/location-history', [AssetController::class, 'getLocationHistory']);
    Route::get('assets/{asset}/alerts', [AssetController::class, 'getAlerts']);

    /**
     * Location Routes
     */
    Route::get('assets/{asset}/location', [LocationController::class, 'getCurrentLocation']);
    Route::get('assets/{asset}/location-history', [LocationController::class, 'getHistory']);
    Route::get('assets/{asset}/location-stats', [LocationController::class, 'getStatistics']);
    Route::get('assets/{asset}/location-range', [LocationController::class, 'getLocationByDateRange']);
    Route::post('location-logs', [LocationController::class, 'storeLocationLog']);
    Route::post('location-logs/batch', [LocationController::class, 'batchStoreLocationLogs']);

    /**
     * Geofence Routes
     */
    Route::apiResource('geofences', GeofenceController::class);
    Route::get('geofences/{geofence}/violations', [GeofenceController::class, 'getViolations']);
    Route::post('geofences/{geofence}/check-asset', [GeofenceController::class, 'checkAssetInside']);
    Route::post('geofences/{geofence}/assign-assets', [GeofenceController::class, 'assignAssets']);

    /**
     * Alert Routes
     */
    Route::apiResource('alerts', AlertController::class)->only(['index', 'show', 'destroy']);
    Route::get('assets/{asset}/alerts', [AlertController::class, 'getAssetAlerts']);
    Route::patch('alerts/{alert}/mark-read', [AlertController::class, 'markAsRead']);
    Route::patch('alerts/{alert}/mark-resolved', [AlertController::class, 'markAsResolved']);
    Route::get('alerts/count/unread', [AlertController::class, 'getUnreadCount']);
    Route::get('alerts/summary', [AlertController::class, 'getSummary']);

    /**
     * Custom Alert Rules
     */
    Route::get('assets/{asset}/custom-rules', [CustomAlertRuleController::class, 'index']);
    Route::post('assets/{asset}/custom-rules', [CustomAlertRuleController::class, 'store']);
    Route::put('custom-rules/{rule}', [CustomAlertRuleController::class, 'update']);
    Route::delete('custom-rules/{rule}', [CustomAlertRuleController::class, 'destroy']);
    Route::patch('custom-rules/{rule}/toggle', [CustomAlertRuleController::class, 'toggle']);

    /**
     * Map Routes
     */
    Route::get('map/assets', [MapController::class, 'getAssetsForMap']);
    Route::get('map/assets/{asset}/track', [MapController::class, 'getAssetTrack']);
    Route::get('map/geofences', [MapController::class, 'getGeofencesForMap']);

    /**
     * Reports Routes
     */
    Route::get('reports/assets', [ReportController::class, 'assetSummary']);
    Route::get('reports/alerts', [ReportController::class, 'alertsReport']);
    Route::get('reports/tracking', [ReportController::class, 'trackingReport']);
    Route::get('reports/assets/export/pdf', [ReportController::class, 'exportAssetsPdf']);
    Route::get('reports/assets/export/csv', [ReportController::class, 'exportAssetsCsv']);
    Route::get('reports/alerts/export/pdf', [ReportController::class, 'exportAlertsPdf']);

    /**
     * Admin Dashboard Routes
     */
    Route::get('admin/dashboard/metrics', [AdminDashboardController::class, 'metrics']);
    Route::get('admin/dashboard/health', [AdminDashboardController::class, 'systemHealth']);

    /**
     * User Management Routes (Admin only)
     */
    Route::post('admin/users/create', [UserManagementController::class, 'createUser']);
    Route::post('admin/users/import', [UserManagementController::class, 'bulkImportUsers']);
    Route::post('admin/users/{user}/regenerate-password', [UserManagementController::class, 'regeneratePassword']);
});
