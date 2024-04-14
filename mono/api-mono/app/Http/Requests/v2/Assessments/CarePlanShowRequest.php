<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CarePlanShowRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'nullable|exists:care_plans,id',
            'access_role' => 'nullable|in:admin,manager,user',
        ];
    }
}
