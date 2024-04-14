<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class ZoneIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'per_page' => 'nullable|integer|min:1',
            'sort_by' => 'nullable|string|in:name,code,created_at,updated_at,id',
            'sort_dir' => 'nullable|string|in:asc,desc',
        ];
    }
}
