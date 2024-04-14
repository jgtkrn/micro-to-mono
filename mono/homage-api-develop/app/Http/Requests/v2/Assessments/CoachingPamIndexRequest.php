<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CoachingPamIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'care_plan_id' => ['nullable', 'Integer', 'exists:care_plans,id,deleted_at,NULL'],
        ];
    }
}
