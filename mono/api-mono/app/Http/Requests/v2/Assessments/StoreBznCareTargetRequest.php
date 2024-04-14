<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class StoreBznCareTargetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'care_plan_id' => 'nullable|integer|exists:care_plans,id,deleted_at,NULL',
            'target_type' => 'nullable|array',
            'target_type.*' => 'nullable|integer',
            'intervention.*' => 'nullable|string',
            'plan.*' => 'nullable|string',
            'ct_area.*' => 'nullable|string',
            'ct_target.*' => 'nullable|string',
            'ct_ssa.*' => 'nullable|string',
            'ct_domain.*' => 'nullable|integer',
            'ct_urgency.*' => 'nullable|integer',
            'ct_category.*' => 'nullable|integer',
            'ct_priority.*' => 'nullable|integer',
            'ct_modifier.*' => 'nullable|integer',
            'ct_knowledge.*' => 'nullable|integer',
            'ct_behaviour.*' => 'nullable|integer',
            'ct_status.*' => 'nullable|integer',
            'omaha_s.*' => 'nullable|string',
        ];
    }
}
