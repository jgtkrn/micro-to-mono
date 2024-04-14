<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CgaCareTargetStoreV2Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'nullable|integer',
            'care_plan_id' => 'sometimes|integer|exists:care_plans,id,deleted_at,NULL',
            'cga_care_targets' => 'array|nullable',
            'cga_care_targets.*.target' => 'nullable|string',
            'cga_care_targets.*.health_vision' => 'nullable|string',
            'cga_care_targets.*.long_term_goal' => 'nullable|string',
            'cga_care_targets.*.short_term_goal' => 'nullable|string',
            'cga_care_targets.*.motivation' => 'nullable|integer',
            'cga_care_targets.*.early_change_stage' => 'nullable|integer',
            'cga_care_targets.*.later_change_stage' => 'nullable|integer',
        ];
    }
}
