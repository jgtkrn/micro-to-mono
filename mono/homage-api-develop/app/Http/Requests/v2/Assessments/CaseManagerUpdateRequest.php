<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CaseManagerUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'care_plan_id' => 'nullable|integer|exists:care_plans,id,deleted_at,NULL',
            'case_managers' => 'nullable|array',
            'case_managers.*.manager_id' => 'nullable|integer',
            'case_managers.*.manager_name' => 'nullable|string',
        ];
    }
}
