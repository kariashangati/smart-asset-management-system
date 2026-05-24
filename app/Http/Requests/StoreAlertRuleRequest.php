<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlertRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isDepartmentManager());
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'rule_type' => 'required|string|in:speed_threshold,temperature,motion,custom',
            'condition' => 'required|array',
            'condition.field' => 'required|string',
            'condition.operator' => 'required|string|in:>,<,==,!=,>=,<=,contains',
            'threshold_value' => 'required|numeric',
            'action' => 'required|string|in:create_alert,notify,trigger_event',
            'status' => 'required|string|in:active,inactive',
            'asset_id' => 'nullable|exists:assets,id',
            'department_id' => 'nullable|exists:departments,id',
        ];
    }
}
