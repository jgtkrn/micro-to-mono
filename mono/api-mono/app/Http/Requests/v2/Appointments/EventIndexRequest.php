<?php

namespace App\Http\Requests\v2\Appointments;

use Illuminate\Foundation\Http\FormRequest;

class EventIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'search' => 'nullable|string',
            'category_id' => 'nullable|string',
            'user_ids' => 'nullable|string',
            'case_type' => 'nullable|string',
            'from' => 'nullable|string',
            'to' => 'nullable|string',
            'team_ids' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'sort_by' => 'nullable|string',
            'sort_dir' => 'nullable|string|in:asc,desc',
        ];
    }
}
