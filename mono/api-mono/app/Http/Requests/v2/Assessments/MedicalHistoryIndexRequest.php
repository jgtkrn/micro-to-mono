<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class MedicalHistoryIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sort_by' => 'nullable|string|in:created_at',
            'sort_dir' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
        ];
    }
}
