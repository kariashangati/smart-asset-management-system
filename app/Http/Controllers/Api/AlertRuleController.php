<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AlertRule;
use App\Http\Requests\StoreAlertRuleRequest;
use App\Http\Requests\UpdateAlertRuleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertRuleController extends Controller
{
    /**
     * List alert rules
     * GET /api/alert-rules
     */
    public function index(Request $request): JsonResponse
    {
        $query = AlertRule::query();

        if ($request->has('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $rules = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $rules->items(),
            'pagination' => [
                'total' => $rules->total(),
                'per_page' => $rules->perPage(),
                'current_page' => $rules->currentPage(),
            ],
        ]);
    }

    /**
     * Create alert rule
     * POST /api/alert-rules
     */
    public function store(StoreAlertRuleRequest $request): JsonResponse
    {
        $rule = AlertRule::create([
            ...$request->validated(),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alert rule created successfully',
            'data' => $rule,
        ], 201);
    }

    /**
     * Update alert rule
     * PUT /api/alert-rules/{id}
     */
    public function update(UpdateAlertRuleRequest $request, AlertRule $rule): JsonResponse
    {
        $rule->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Alert rule updated successfully',
            'data' => $rule,
        ]);
    }

    /**
     * Delete alert rule
     * DELETE /api/alert-rules/{id}
     */
    public function destroy(AlertRule $rule): JsonResponse
    {
        $rule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alert rule deleted successfully',
        ]);
    }
}
