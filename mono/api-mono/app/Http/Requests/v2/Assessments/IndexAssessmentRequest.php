<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class IndexAssessmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'case_id' => 'nullable|integer',
            'is_other' => 'nullable|boolean',
            'access_role' => 'nullable|string',
        ];
    }
}
