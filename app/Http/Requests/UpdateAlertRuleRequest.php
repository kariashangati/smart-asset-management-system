<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlertRuleRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'rule_type' => 'sometimes|required|string|in:speed_threshold,temperature,motion,custom',
            'condition' => 'sometimes|required|array',
            'condition.field' => 'required|string',
            'condition.operator' => 'required|string|in:>,<,==,!=,>=,<=,contains',
            'threshold_value' => 'sometimes|required|numeric',
            'action' => 'sometimes|required|string|in:create_alert,notify,trigger_event',
            'status' => 'sometimes|required|string|in:active,inactive',
        ];
    }
}
