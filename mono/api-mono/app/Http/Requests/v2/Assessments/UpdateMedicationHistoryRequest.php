<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicationHistoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'case_id' => ['nullable', 'integer'],
            'medication_name' => ['nullable', 'string'],
            'dosage' => ['nullable', 'string'],
            'frequency' => ['nullable'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'route' => ['nullable', 'string'],
            'prn' => ['nullable', 'boolean'],
            'purpose' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'created_by' => ['nullable', 'integer'],
            'updated_by' => ['nullable', 'integer'],
            'created_by_name' => ['nullable', 'string'],
            'updated_by_name' => ['nullable', 'string'],
        ];
    }
}
