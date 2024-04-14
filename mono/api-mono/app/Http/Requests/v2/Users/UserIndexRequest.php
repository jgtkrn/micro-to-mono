<?php

namespace App\Http\Requests\v2\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
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
            'per_page' => 'nullable|integer',
            'ids' => 'nullable|string',
            'team_id' => 'nullable|integer',
            'search' => 'nullable|string',
        ];
    }
}
