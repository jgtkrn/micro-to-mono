<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class ExportPatientReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'search' => 'nullable|string',
            'size' => 'nullable|integer',
            'page' => 'nullable|integer',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'case_status' => 'nullable|string',
        ];
    }
}
