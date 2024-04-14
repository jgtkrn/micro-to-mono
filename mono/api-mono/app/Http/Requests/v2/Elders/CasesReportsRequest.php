<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class CasesReportsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'search' => 'nullable|string',
            'size' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'case_status' => 'nullable|string',
        ];
    }
}
