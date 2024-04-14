<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class MedicationDrugImportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx', 'max:5000'],
        ];
    }
}
