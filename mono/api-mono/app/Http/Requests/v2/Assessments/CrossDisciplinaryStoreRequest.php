<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CrossDisciplinaryStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'case_id' => 'nullable|integer',
            'role' => 'nullable|string',
            'comments' => 'nullable|string',
            'name' => 'nullable|string',
            'date' => 'nullable|date',
        ];
    }
}
