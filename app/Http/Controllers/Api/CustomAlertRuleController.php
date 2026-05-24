<?php

namespace App\Http\Controllers\Api;

use App\Models\CustomAlertRule;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomAlertRuleController extends Controller
{
    /**
     * List custom alert rules for asset
     * GET /api/assets/{asset}/custom-rules
     */
    public function index(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        $rules = $asset->customAlertRules()->get();

        return response()->json([
            'success' => true,
            'data' => $rules,
            'count' => $rules->count(),
        ]);
    }

    /**
     * Create custom alert rule
     * POST /api/assets/{asset}/custom-rules
     */
    public function store(Request $request, Asset $asset): JsonResponse
    {
        $this->authorize('update', $asset);

        $request->validate([
            'rule_name' => 'required|string|max:255',
            'rule_type' => 'required|string|in:speed_threshold,geofence_breach,inactivity,custom',
            'threshold_value' => 'nullable|numeric',
            'action' => 'required|string|in:email,sms,push,database',
            'recipient_emails' => 'nullable|array',
            'recipient_emails.*' => 'email',
            'recipient_phones' => 'nullable|array',
            'condition' => 'nullable|array',
        ]);

        $rule = $asset->customAlertRules()->create([
            'rule_name' => $request->rule_name,
            'rule_type' => $request->rule_type,
            'threshold_value' => $request->threshold_value,
            'action' => $request->action,
            'recipient_emails' => $request->recipient_emails ?? [],
            'recipient_phones' => $request->recipient_phones ?? [],
            'condition' => $request->condition ?? [],
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alert rule created successfully',
            'data' => $rule,
        ], 201);
    }

    /**
     * Update custom alert rule
     * PUT /api/custom-rules/{rule}
     */
    public function update(Request $request, CustomAlertRule $rule): JsonResponse
    {
        $this->authorize('update', $rule->asset);

        $request->validate([
            'rule_name' => 'sometimes|required|string|max:255',
            'threshold_value' => 'nullable|numeric',
            'action' => 'sometimes|required|string|in:email,sms,push,database',
            'recipient_emails' => 'nullable|array',
            'recipient_phones' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $rule->update($request->only(['rule_name', 'threshold_value', 'action', 'recipient_emails', 'recipient_phones', 'is_active']));

        return response()->json([
            'success' => true,
            'message' => 'Alert rule updated successfully',
            'data' => $rule,
        ]);
    }

    /**
     * Delete custom alert rule
     * DELETE /api/custom-rules/{rule}
     */
    public function destroy(CustomAlertRule $rule): JsonResponse
    {
        $this->authorize('update', $rule->asset);

        $rule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Alert rule deleted successfully',
        ]);
    }

    /**
     * Toggle rule active status
     * PATCH /api/custom-rules/{rule}/toggle
     */
    public function toggle(CustomAlertRule $rule): JsonResponse
    {
        $this->authorize('update', $rule->asset);

        $rule->update(['is_active' => !$rule->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Rule status toggled',
            'is_active' => $rule->is_active,
        ]);
    }
}
