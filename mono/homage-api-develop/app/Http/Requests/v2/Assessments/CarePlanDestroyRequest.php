<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CarePlanDestroyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'is_hcsw' => 'nullable|boolean',
            'is_hcw' => 'nullable|boolean',
            'access_role' => 'nullable|in:admin',
        ];
    }
}
