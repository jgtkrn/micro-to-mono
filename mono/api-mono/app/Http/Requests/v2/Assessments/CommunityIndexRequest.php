<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CommunityIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'per_page' => 'nullable|integer',
            'sort_by' => 'nullable|in:id,name,url,created_at,updated_at',
            'sort_dir' => 'nullable|in:asc,desc',
            'search' => 'nullable|string',
        ];
    }
}
