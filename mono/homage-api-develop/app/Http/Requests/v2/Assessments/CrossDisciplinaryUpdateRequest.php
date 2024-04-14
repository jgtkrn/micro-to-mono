<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrossDisciplinaryUpdateRequest extends FormRequest
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
            'user_id' => 'nullable|string',
            'id' => 'nullable|integer',
            'case_id' => 'nullable|integer',
            'role' => 'nullable|string',
            'comments' => 'nullable|string',
            'name' => 'nullable|string',
            'date' => 'nullable|date_format:Y-m-d',
        ];
    }
}
