<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CommunityDownloadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'form_name' => 'nullable',
            'notes_type' => 'nullable|in:bzn,cga',
        ];
    }
}
