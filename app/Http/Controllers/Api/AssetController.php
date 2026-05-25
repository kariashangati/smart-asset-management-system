<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Display a listing of assets
     * GET /api/assets
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Asset::class);

        $query = Asset::with(['department', 'latestLocation', 'trackerDevice']);

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by asset type
        if ($request->filled('asset_type')) {
            $query->where('asset_type', $request->asset_type);
        }

        // Search by name or serial number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%");
            });
        }

        $assets = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Assets retrieved successfully',
            'data'    => AssetResource::collection($assets->items()),
            'pagination' => [
                'total'        => $assets->total(),
                'per_page'     => $assets->perPage(),
                'current_page' => $assets->currentPage(),
                'last_page'    => $assets->lastPage(),
            ],
        ]);
    }

    /**
     * Store a newly created asset
     * POST /api/assets
     */
    public function store(StoreAssetRequest $request): JsonResponse
    {
        $this->authorize('create', Asset::class);

        $asset = Asset::create(array_merge(
            $request->validated(),
            ['created_by' => auth()->id()]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Asset created successfully',
            'data'    => new AssetResource(
                $asset->load(['department', 'latestLocation', 'trackerDevice'])
            ),
        ], 201);
    }

    /**
     * Display the specified asset
     * GET /api/assets/{id}
     */
    public function show(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        $asset->load(['department', 'latestLocation', 'trackerDevice', 'geofences']);

        return response()->json([
            'success' => true,
            'data'    => new AssetResource($asset),
        ]);
    }

    /**
     * Update the specified asset
     * PUT /api/assets/{id}
     */
    public function update(UpdateAssetRequest $request, Asset $asset): JsonResponse
    {
        $this->authorize('update', $asset);

        $asset->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Asset updated successfully',
            'data'    => new AssetResource(
                $asset->load(['department', 'latestLocation', 'trackerDevice'])
            ),
        ]);
    }

    /**
     * Delete the specified asset
     * DELETE /api/assets/{id}
     */
    public function destroy(Asset $asset): JsonResponse
    {
        $this->authorize('delete', $asset);

        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asset deleted successfully',
        ]);
    }

    /**
     * Get assets by department
     * GET /api/assets/department/{department_id}
     */
    public function getByDepartment(Request $request, int $departmentId): JsonResponse
    {
        $assets = Asset::where('department_id', $departmentId)
            ->with(['department', 'latestLocation', 'trackerDevice'])
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => AssetResource::collection($assets->items()),
            'pagination' => [
                'total'        => $assets->total(),
                'per_page'     => $assets->perPage(),
                'current_page' => $assets->currentPage(),
                'last_page'    => $assets->lastPage(),
            ],
        ]);
    }

    /**
     * Get asset location history
     * GET /api/assets/{id}/location-history
     */
    public function getLocationHistory(Request $request, Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        $locations = $asset->locationLogs()
            ->orderBy('recorded_at', 'desc')
            ->paginate($request->input('per_page', 50));

        return response()->json([
            'success' => true,
            'data'    => $locations->items(),
            'pagination' => [
                'total'        => $locations->total(),
                'per_page'     => $locations->perPage(),
                'current_page' => $locations->currentPage(),
                'last_page'    => $locations->lastPage(),
            ],
        ]);
    }

    /**
     * Get alerts for a specific asset
     * GET /api/assets/{id}/alerts
     */
    public function getAlerts(Request $request, Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        $alerts = $asset->alerts()
            ->orderBy('triggered_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $alerts->items(),
            'pagination' => [
                'total'        => $alerts->total(),
                'per_page'     => $alerts->perPage(),
                'current_page' => $alerts->currentPage(),
                'last_page'    => $alerts->lastPage(),
            ],
        ]);
    }
}
