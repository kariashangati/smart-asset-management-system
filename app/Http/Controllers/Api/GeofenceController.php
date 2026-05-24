<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGeofenceRequest;
use App\Http\Requests\UpdateGeofenceRequest;
use App\Http\Resources\GeofenceResource;
use App\Models\Asset;
use App\Models\Geofence;
use App\Services\GeofenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeofenceController extends Controller
{
    protected GeofenceService $geofenceService;

    public function __construct(GeofenceService $geofenceService)
    {
        $this->geofenceService = $geofenceService;
    }

    /**
     * Display a listing of geofences
     * GET /api/geofences
     */
    public function index(Request $request): JsonResponse
    {
        $query = Geofence::with(['assets', 'createdBy']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by asset
        if ($request->has('asset_id')) {
            $query->whereHas('assets', function ($q) {
                $q->where('assets.id', request('asset_id'));
            });
        }

        // Search by name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $geofences = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'message' => 'Geofences retrieved successfully',
            'data' => GeofenceResource::collection($geofences->items()),
            'pagination' => [
                'total' => $geofences->total(),
                'per_page' => $geofences->perPage(),
                'current_page' => $geofences->currentPage(),
                'last_page' => $geofences->lastPage(),
            ],
        ]);
    }

    /**
     * Store a newly created geofence
     * POST /api/geofences
     */
    public function store(StoreGeofenceRequest $request): JsonResponse
    {
        $geofence = $this->geofenceService->createGeofence(
            array_merge($request->validated(), ['created_by' => auth()->id()])
        );

        return response()->json([
            'success' => true,
            'message' => 'Geofence created successfully',
            'data' => new GeofenceResource($geofence->load(['assets', 'createdBy'])),
        ], 201);
    }

    /**
     * Display the specified geofence
     * GET /api/geofences/{id}
     */
    public function show(Geofence $geofence): JsonResponse
    {
        $geofence->load(['assets', 'createdBy']);

        return response()->json([
            'success' => true,
            'data' => new GeofenceResource($geofence),
        ]);
    }

    /**
     * Update the specified geofence
     * PUT /api/geofences/{id}
     */
    public function update(UpdateGeofenceRequest $request, Geofence $geofence): JsonResponse
    {
        $updated = $this->geofenceService->updateGeofence($geofence, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Geofence updated successfully',
            'data' => new GeofenceResource($updated->load(['assets', 'createdBy'])),
        ]);
    }

    /**
     * Delete the specified geofence
     * DELETE /api/geofences/{id}
     */
    public function destroy(Geofence $geofence): JsonResponse
    {
        $this->geofenceService->deleteGeofence($geofence);

        return response()->json([
            'success' => true,
            'message' => 'Geofence deleted successfully',
        ]);
    }

    /**
     * Get assets outside a geofence
     * GET /api/geofences/{id}/violations
     */
    public function getViolations(Geofence $geofence): JsonResponse
    {
        $assetsOutside = $this->geofenceService->getAssetsOutsideGeofence($geofence);

        return response()->json([
            'success' => true,
            'message' => 'Assets outside geofence retrieved',
            'data' => $assetsOutside,
            'count' => $assetsOutside->count(),
        ]);
    }

    /**
     * Check if an asset is inside a geofence
     * POST /api/geofences/{id}/check-asset
     */
    public function checkAssetInside(Request $request, Geofence $geofence): JsonResponse
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
        ]);

        $asset = Asset::findOrFail($request->asset_id);
        $isInside = $this->geofenceService->isAssetInsideGeofence($asset, $geofence);

        return response()->json([
            'success' => true,
            'asset_id' => $asset->id,
            'geofence_id' => $geofence->id,
            'is_inside' => $isInside,
            'message' => $isInside ? 'Asset is inside geofence' : 'Asset is outside geofence',
        ]);
    }

    /**
     * Assign assets to geofence
     * POST /api/geofences/{id}/assign-assets
     */
    public function assignAssets(Request $request, Geofence $geofence): JsonResponse
    {
        $request->validate([
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'exists:assets,id',
        ]);

        $geofence->assets()->sync($request->asset_ids);

        return response()->json([
            'success' => true,
            'message' => 'Assets assigned to geofence successfully',
            'geofence_id' => $geofence->id,
            'assigned_assets' => $request->asset_ids,
        ]);
    }
}
