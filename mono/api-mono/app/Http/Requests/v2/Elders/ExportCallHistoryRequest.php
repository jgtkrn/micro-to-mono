<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class ExportCallHistoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'by_name' => 'nullable|string',
            'sort_by' => 'nullable|string|in:id,call_date,call_status,created_at,updated_at',
            'sort_dir' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer',
            'size' => 'nullable|integer',
        ];
    }
}
