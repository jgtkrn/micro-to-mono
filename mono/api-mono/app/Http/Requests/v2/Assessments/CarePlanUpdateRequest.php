<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CarePlanUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'nullable',
            'access_role' => 'nullable|in:admin,manager,user',
            'case_type' => 'nullable|in:CGA,BZN',
            'manager_id' => 'nullable|exists:users,id',
            'handler_id' => 'nullable|exists:users,id',
        ];
    }
}
