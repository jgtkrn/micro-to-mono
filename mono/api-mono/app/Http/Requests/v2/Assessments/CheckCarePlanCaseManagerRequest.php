<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CheckCarePlanCaseManagerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'manager_id' => 'nullable|integer',
            'case_id' => 'nullable|integer',
            'case_manager' => 'nullable|string',

        ];
    }
}
