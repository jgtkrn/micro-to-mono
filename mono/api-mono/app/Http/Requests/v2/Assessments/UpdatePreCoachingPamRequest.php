<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreCoachingPamRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'care_plan_id' => ['nullable', 'exists:care_plans,id'],
            'section' => ['nullable', 'integer'],
            'intervention_group' => ['nullable', 'integer'],
            'gender' => ['nullable', 'integer'],
            'health_manage' => ['nullable', 'integer'],
            'active_role' => ['nullable', 'integer'],
            'self_confidence' => ['nullable', 'integer'],
            'drug_knowledge' => ['nullable', 'integer'],
            'self_understanding' => ['nullable', 'integer'],
            'self_health' => ['nullable', 'integer'],
            'self_discipline' => ['nullable', 'integer'],
            'issue_knowledge' => ['nullable', 'integer'],
            'other_treatment' => ['nullable', 'integer'],
            'change_treatment' => ['nullable', 'integer'],
            'issue_prevention' => ['nullable', 'integer'],
            'find_solutions' => ['nullable', 'integer'],
            'able_maintain' => ['nullable', 'integer'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
