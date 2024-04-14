<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CrossDisciplinaryDestroyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'is_hcw' => 'nullable|boolean',
            'is_other' => 'nullable|boolean',
            'access_role' => 'nullable|string',
            'id' => 'nullable|integer',
        ];
    }
}
