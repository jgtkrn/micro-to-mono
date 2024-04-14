<?php

namespace App\Http\Requests\v2\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserReportsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sort_by' => 'nullable|string',
            'sort_dir' => 'nullable|string',
            'size' => 'nullable|integer',
            'page' => 'nullable|integer',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'all' => 'nullable|boolean',
            'search' => 'nullable|string',
            'ids' => 'nullable|string',
        ];
    }
}
