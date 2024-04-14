<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class IndexCgaCareTargetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'care_plan_id' => ['nullable', 'integer', 'exists:care_plans,id,deleted_at,NULL'],
        ];
    }
}
