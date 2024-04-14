<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CrossDisciplinaryIndexRequest extends FormRequest
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
            'case_id' => 'nullable|integer',
            'sort_by' => 'nullable|string',
            'sort_dir' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer',
        ];
    }
}
