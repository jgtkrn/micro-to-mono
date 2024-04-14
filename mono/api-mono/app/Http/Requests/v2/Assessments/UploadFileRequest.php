<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'form_name' => 'nullable',
            'file' => 'nullable|max:12288|mimes:jpg,jpeg,png,pdf',
            'id' => 'nullable|exists:assessment_cases,id',
        ];
    }
}
