<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CgaCareTargetStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'care_plan_id' => 'nullable|exists:care_plans,id,deleted_at,NULL',
            'user_id' => 'nullable',
            'target' => 'nullable',
            'health_vision' => 'nullable',
            'long_term_goal' => 'nullable',
            'motivation' => 'nullable',
            'short_term_goal' => 'nullable',
            'early_change_stage' => 'nullable',
            'later_change_stage' => 'nullable',
        ];
    }
}
