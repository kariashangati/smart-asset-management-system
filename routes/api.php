<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\GeofenceController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\WebhookController;
use App\Http\Controllers\Api\AlertRuleController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserManagementController;

// Public webhook endpoints
Route::post('webhooks/location', [WebhookController::class, 'handleLocationWebhook']);
Route::post('webhooks/alert', [WebhookController::class, 'handleAlertWebhook']);
Route::get('webhooks/health', [WebhookController::class, 'health']);

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
     * Alert Rule Routes
     */
    Route::apiResource('alert-rules', AlertRuleController::class)->except('show');

    /**
     * Map Routes
     */
    Route::get('map/assets', [MapController::class, 'getAssetsForMap']);
    Route::get('map/assets/{asset}', [MapController::class, 'getAssetLocation']);

    /**
     * Report Routes
     */
    Route::get('reports/dashboard', [ReportController::class, 'getDashboardMetrics']);
    Route::get('reports/asset-values', [ReportController::class, 'getAssetValueReport']);
    Route::get('reports/alerts', [ReportController::class, 'getAlertsReport']);
    Route::get('reports/export/pdf', [ReportController::class, 'exportToPdf']);
    Route::get('reports/export/csv', [ReportController::class, 'exportToCsv']);

    /**
     * User Management Routes
     */
    Route::post('users/create-with-credentials', [UserManagementController::class, 'createWithCredentials']);
    Route::post('users/{user}/reset-password', [UserManagementController::class, 'resetPassword']);
    Route::post('users/bulk-import', [UserManagementController::class, 'bulkImport']);
    Route::get('users/bulk-import-template', [UserManagementController::class, 'getBulkImportTemplate']);
});
