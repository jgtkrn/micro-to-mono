<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class CasesIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'per_page' => 'nullable|integer',
            'sort_by' => 'nullable|string',
            'sort_dir' => 'nullable|string',
            'elder_uids' => 'nullable|string',
            'district' => 'nullable|string',
            'search' => 'nullable|string',
            'user_type' => 'nullable|string',
            'case_status' => 'nullable|string',
            'exclude' => 'nullable|boolean',
        ];
    }
}
