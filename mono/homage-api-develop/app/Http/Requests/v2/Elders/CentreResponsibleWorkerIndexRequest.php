<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class CentreResponsibleWorkerIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'per_page' => 'nullable|integer',
            'sort_by' => 'nullable|in:name,code,created_at,updated_at',
            'sort_dir' => 'nullable|in:asc,desc',
        ];
    }
}
