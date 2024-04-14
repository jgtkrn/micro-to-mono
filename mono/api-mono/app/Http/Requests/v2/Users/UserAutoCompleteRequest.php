<?php

namespace App\Http\Requests\v2\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserAutoCompleteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'per_page' => 'nullable|integer',
            'name' => 'nullable|string',
            'email' => 'nullable|string',
            'team_id' => 'nullable|string',
            'team_ids' => 'nullable|string',
            'ids' => 'nullable|string',
            'date' => 'nullable|date',
        ];
    }
}
