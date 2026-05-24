<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlertResource;
use App\Models\Alert;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Display a listing of alerts
     * GET /api/alerts
     */
    public function index(Request $request): JsonResponse
    {
        $query = Alert::with(['asset', 'trackerDevice']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by severity
        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by alert type
        if ($request->has('alert_type')) {
            $query->where('alert_type', $request->alert_type);
        }

        // Filter by asset
        if ($request->has('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        // Order by most recent
        $query->orderBy('triggered_at', 'desc');

        $alerts = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'message' => 'Alerts retrieved successfully',
            'data' => AlertResource::collection($alerts->items()),
            'pagination' => [
                'total' => $alerts->total(),
                'per_page' => $alerts->perPage(),
                'current_page' => $alerts->currentPage(),
                'last_page' => $alerts->lastPage(),
            ],
        ]);
    }

    /**
     * Get alerts for specific asset
     * GET /api/assets/{asset_id}/alerts
     */
    public function getAssetAlerts(Request $request, Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        $query = $asset->alerts();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by severity
        if ($request->has('severity')) {
            $query->where('severity', $request->severity);
        }

        $alerts = $query->orderBy('triggered_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => AlertResource::collection($alerts->items()),
            'pagination' => [
                'total' => $alerts->total(),
                'per_page' => $alerts->perPage(),
                'current_page' => $alerts->currentPage(),
                'last_page' => $alerts->lastPage(),
            ],
        ]);
    }

    /**
     * Display the specified alert
     * GET /api/alerts/{id}
     */
    public function show(Alert $alert): JsonResponse
    {
        $alert->load(['asset', 'trackerDevice']);

        return response()->json([
            'success' => true,
            'data' => new AlertResource($alert),
        ]);
    }

    /**
     * Mark alert as read
     * PATCH /api/alerts/{id}/mark-read
     */
    public function markAsRead(Alert $alert): JsonResponse
    {
        $alert->update(['status' => 'read']);

        return response()->json([
            'success' => true,
            'message' => 'Alert marked as read',
            'data' => new AlertResource($alert),
        ]);
    }

    /**
     * Mark alert as resolved
     * PATCH /api/alerts/{id}/mark-resolved
     */
    public function markAsResolved(Request $request, Alert $alert): JsonResponse
    {
        $request->validate([
            'resolution_notes' => 'nullable|string|max:500',
        ]);

        $alert->update([
            'status' => 'resolved',
            'resolution_notes' => $request->resolution_notes,
            'resolved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alert marked as resolved',
            'data' => new AlertResource($alert),
        ]);
    }

    /**
     * Get unread alerts count
     * GET /api/alerts/count/unread
     */
    public function getUnreadCount(): JsonResponse
    {
        $count = Alert::where('status', 'unread')->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }

    /**
     * Get alerts summary (by severity and type)
     * GET /api/alerts/summary
     */
    public function getSummary(): JsonResponse
    {
        $bySeverity = Alert::where('status', '!=', 'resolved')
            ->selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->get();

        $byType = Alert::where('status', '!=', 'resolved')
            ->selectRaw('alert_type, COUNT(*) as count')
            ->groupBy('alert_type')
            ->get();

        return response()->json([
            'success' => true,
            'summary' => [
                'by_severity' => $bySeverity,
                'by_type' => $byType,
                'total_active' => Alert::where('status', '!=', 'resolved')->count(),
                'total_unread' => Alert::where('status', 'unread')->count(),
            ],
        ]);
    }

    /**
     * Delete alert
     * DELETE /api/alerts/{id}
     */
    public function destroy(Alert $alert): JsonResponse
    {
        $alert->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alert deleted successfully',
        ]);
    }
}
