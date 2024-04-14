<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class StoreV2BznCareTargetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'care_plan_id' => 'nullable|integer|exists:care_plans,id,deleted_at,NULL',
            'user_id' => 'nullable|integer',
            'access_role' => 'nullable|string',
            'is_other' => 'nullable|boolean',
            'bzn_care_targets' => 'nullable|array',
            'bzn_care_targets.*.intervention' => 'nullable|string',
            'bzn_care_targets.*.target_type' => 'nullable|integer',
            'bzn_care_targets.*.plan' => 'nullable|string',
            'bzn_care_targets.*.ct_area' => 'nullable|string',
            'bzn_care_targets.*.ct_target' => 'nullable|string',
            'bzn_care_targets.*.ct_ssa' => 'nullable|string',
            'bzn_care_targets.*.ct_domain' => 'nullable|integer',
            'bzn_care_targets.*.ct_urgency' => 'nullable|integer',
            'bzn_care_targets.*.ct_category' => 'nullable|integer',
            'bzn_care_targets.*.ct_priority' => 'nullable|integer',
            'bzn_care_targets.*.ct_modifier' => 'nullable|integer',
            'bzn_care_targets.*.ct_knowledge' => 'nullable|integer',
            'bzn_care_targets.*.ct_behaviour' => 'nullable|integer',
            'bzn_care_targets.*.ct_status' => 'nullable|integer',
            'bzn_care_targets.*.omaha_s' => 'nullable|string',
        ];
    }
}
