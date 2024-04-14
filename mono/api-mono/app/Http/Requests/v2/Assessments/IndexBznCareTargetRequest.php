<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class IndexBznCareTargetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'per_page' => 'nullable|integer',
            'sort_by' => 'nullable|in:id,created_at,updated_at',
            'sort_dir' => 'nullable|in:asc,desc',
            'care_plan_id' => 'nullable|integer|exists:care_plans,id,deleted_at,NULL',
            'is_other' => 'nullable|boolean',
            'access_role' => 'nullable|string',
        ];
    }
}
