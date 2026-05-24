<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlertRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlertRuleController extends Controller
{
    /**
     * Get all alert rules
     * GET /api/alert-rules
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', AlertRule::class);

        $rules = AlertRule::query()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $rules,
        ]);
    }

    /**
     * Store new alert rule
     * POST /api/alert-rules
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', AlertRule::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'asset_type' => 'required|string',
            'condition_type' => 'required|in:equals,greater_than,less_than,greater_or_equal,less_or_equal,not_equal,contains,not_contains,in_range,regex',
            'condition_value' => 'required|string',
            'threshold_unit' => 'nullable|string',
            'severity' => 'required|in:info,warning,critical',
            'action_type' => 'nullable|string',
            'notification_channels' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $rule = AlertRule::create([
            ...validated,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alert rule created successfully',
            'data' => $rule,
        ], 201);
    }

    /**
     * Get specific alert rule
     * GET /api/alert-rules/{id}
     */
    public function show(AlertRule $rule): JsonResponse
    {
        $this->authorize('view', $rule);

        return response()->json([
            'success' => true,
            'data' => $rule,
        ]);
    }

    /**
     * Update alert rule
     * PUT /api/alert-rules/{id}
     */
    public function update(Request $request, AlertRule $rule): JsonResponse
    {
        $this->authorize('update', $rule);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'condition_type' => 'in:equals,greater_than,less_than,greater_or_equal,less_or_equal,not_equal,contains,not_contains,in_range,regex',
            'condition_value' => 'string',
            'severity' => 'in:info,warning,critical',
            'notification_channels' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $rule->update($validated);

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
        $this->authorize('delete', $rule);

        $rule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alert rule deleted successfully',
        ]);
    }

    /**
     * Toggle alert rule active status
     * PATCH /api/alert-rules/{id}/toggle
     */
    public function toggle(AlertRule $rule): JsonResponse
    {
        $this->authorize('update', $rule);

        $rule->toggle();

        return response()->json([
            'success' => true,
            'message' => 'Alert rule toggled successfully',
            'data' => $rule,
        ]);
    }
}
