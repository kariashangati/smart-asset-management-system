<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\CustomAlertRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetAlertRuleController extends Controller
{
    /**
     * Show all alert rules for a specific asset
     */
    public function index(Asset $asset): View
    {
        $this->authorize('view', $asset);

        $rules = $asset->customAlertRules()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.assets.alert-rules', compact('asset', 'rules'));
    }

    /**
     * Store a new alert rule for the asset
     */
    public function store(Request $request, Asset $asset): RedirectResponse
    {
        $this->authorize('update', $asset);

        $validated = $request->validate([
            'rule_name'            => 'required|string|max:255',
            'rule_type'            => 'required|in:speed_threshold,geofence_breach,inactivity,custom',
            'threshold_value'      => 'nullable|numeric',
            'action'               => 'required|in:email,sms,push,database',
            'recipient_emails_raw' => 'nullable|string',
            'recipient_phones_raw' => 'nullable|string',
        ]);

        // Parse comma-separated emails into array
        $emails = [];
        if (!empty($validated['recipient_emails_raw'])) {
            $emails = array_filter(
                array_map('trim', explode(',', $validated['recipient_emails_raw']))
            );
        }

        // Parse comma-separated phones into array
        $phones = [];
        if (!empty($validated['recipient_phones_raw'])) {
            $phones = array_filter(
                array_map('trim', explode(',', $validated['recipient_phones_raw']))
            );
        }

        $asset->customAlertRules()->create([
            'rule_name'        => $validated['rule_name'],
            'rule_type'        => $validated['rule_type'],
            'threshold_value'  => $validated['threshold_value'] ?? null,
            'action'           => $validated['action'],
            'recipient_emails' => array_values($emails),
            'recipient_phones' => array_values($phones),
            'is_active'        => true,
            'created_by'       => auth()->id(),
        ]);

        return redirect()
            ->route('admin.assets.rules.index', $asset)
            ->with('success', 'Alert rule created successfully.');
    }

    /**
     * Update an existing alert rule
     */
    public function update(Request $request, Asset $asset, CustomAlertRule $rule): RedirectResponse
    {
        $this->authorize('update', $asset);

        // Make sure rule belongs to this asset
        abort_if($rule->asset_id !== $asset->id, 404);

        $validated = $request->validate([
            'rule_name'            => 'required|string|max:255',
            'rule_type'            => 'required|in:speed_threshold,geofence_breach,inactivity,custom',
            'threshold_value'      => 'nullable|numeric',
            'action'               => 'required|in:email,sms,push,database',
            'recipient_emails_raw' => 'nullable|string',
            'recipient_phones_raw' => 'nullable|string',
        ]);

        // Parse comma-separated emails into array
        $emails = [];
        if (!empty($validated['recipient_emails_raw'])) {
            $emails = array_filter(
                array_map('trim', explode(',', $validated['recipient_emails_raw']))
            );
        }

        // Parse comma-separated phones into array
        $phones = [];
        if (!empty($validated['recipient_phones_raw'])) {
            $phones = array_filter(
                array_map('trim', explode(',', $validated['recipient_phones_raw']))
            );
        }

        $rule->update([
            'rule_name'        => $validated['rule_name'],
            'rule_type'        => $validated['rule_type'],
            'threshold_value'  => $validated['threshold_value'] ?? null,
            'action'           => $validated['action'],
            'recipient_emails' => array_values($emails),
            'recipient_phones' => array_values($phones),
        ]);

        return redirect()
            ->route('admin.assets.rules.index', $asset)
            ->with('success', 'Alert rule updated successfully.');
    }

    /**
     * Toggle active/inactive status of a rule
     */
    public function toggle(Asset $asset, CustomAlertRule $rule): RedirectResponse
    {
        $this->authorize('update', $asset);

        abort_if($rule->asset_id !== $asset->id, 404);

        $rule->update(['is_active' => !$rule->is_active]);

        $status = $rule->is_active ? 'enabled' : 'disabled';

        return redirect()
            ->route('admin.assets.rules.index', $asset)
            ->with('success', "Rule {$status} successfully.");
    }

    /**
     * Delete an alert rule
     */
    public function destroy(Asset $asset, CustomAlertRule $rule): RedirectResponse
    {
        $this->authorize('delete', $asset);

        abort_if($rule->asset_id !== $asset->id, 404);

        $rule->delete();

        return redirect()
            ->route('admin.assets.rules.index', $asset)
            ->with('success', 'Alert rule deleted successfully.');
    }
}
