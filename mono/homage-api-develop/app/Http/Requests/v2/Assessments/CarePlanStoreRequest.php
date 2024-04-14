<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CarePlanStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'case_id' => 'nullable|integer',
            'case_type' => 'nullable|in:CGA,BZN',
            'manager_id' => 'nullable|integer',
            'handler_id' => 'nullable|integer',
        ];
    }
}
