<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\AlertRule;
use Illuminate\Support\Facades\Log;

class AlertRuleEngine
{
    /**
     * Evaluate all active rules for an asset
     */
    public function evaluateRulesForAsset(int $assetId, array $data): void
    {
        $rules = AlertRule::where('asset_id', $assetId)
            ->where('status', 'active')
            ->get();

        foreach ($rules as $rule) {
            if ($this->evaluateRule($rule, $data)) {
                $this->executeRuleAction($rule, $assetId, $data);
            }
        }
    }

    /**
     * Evaluate if rule condition is met
     */
    private function evaluateRule(AlertRule $rule, array $data): bool
    {
        $condition = $rule->condition;
        $value = $data[$condition['field']] ?? null;

        if ($value === null) {
            return false;
        }

        return match ($condition['operator']) {
            '>' => $value > $rule->threshold_value,
            '<' => $value < $rule->threshold_value,
            '==' => $value == $rule->threshold_value,
            '!=' => $value != $rule->threshold_value,
            '>=' => $value >= $rule->threshold_value,
            '<=' => $value <= $rule->threshold_value,
            'contains' => str_contains((string)$value, $rule->threshold_value),
            default => false,
        };
    }

    /**
     * Execute rule action
     */
    private function executeRuleAction(AlertRule $rule, int $assetId, array $data): void
    {
        match ($rule->action) {
            'create_alert' => $this->createAlert($rule, $assetId, $data),
            'notify' => $this->sendNotification($rule, $assetId, $data),
            'trigger_event' => $this->triggerEvent($rule, $assetId, $data),
            default => Log::warning('Unknown rule action: ' . $rule->action),
        };
    }

    /**
     * Create alert from rule
     */
    private function createAlert(AlertRule $rule, int $assetId, array $data): void
    {
        Alert::create([
            'asset_id' => $assetId,
            'alert_type' => $rule->rule_type,
            'severity' => $data['severity'] ?? 'medium',
            'title' => $rule->name,
            'message' => "Custom rule '{$rule->name}' triggered",
            'status' => 'unread',
            'triggered_at' => now(),
        ]);
    }

    /**
     * Send notification for rule
     */
    private function sendNotification(AlertRule $rule, int $assetId, array $data): void
    {
        Log::info("Rule notification: {$rule->name} for asset {$assetId}");
    }

    /**
     * Trigger custom event
     */
    private function triggerEvent(AlertRule $rule, int $assetId, array $data): void
    {
        Log::info("Rule event triggered: {$rule->name} for asset {$assetId}");
    }
}
