<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssessmentsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'case_id' => 'nullable|integer',
            'first_assessor' => 'nullable|integer',
            'second_assessor' => 'nullable|integer',
            'assessment_date' => 'nullable|date',
            'priority_level' => 'nullable|integer',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'status' => 'nullable|string',
            'is_other' => 'nullable|boolean',
            'access_role' => 'nullable|string',

        ];
    }
}
