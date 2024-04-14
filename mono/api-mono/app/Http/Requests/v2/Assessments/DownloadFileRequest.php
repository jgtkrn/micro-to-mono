<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class DownloadFileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'form_name' => 'nullable',
            'id' => 'nullable|exists:assessment_cases,id',
        ];
    }
}
