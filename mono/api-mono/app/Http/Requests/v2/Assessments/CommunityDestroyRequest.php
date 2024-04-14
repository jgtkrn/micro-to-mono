<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CommunityDestroyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'form_name' => 'nullable|string|in:bzn_signature,cga_signature,bzn_attachment,cga_attachment',
        ];
    }
}
