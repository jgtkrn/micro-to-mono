<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class AutocompleteElderRequest extends FormRequest
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
            'uid' => 'nullable|string',
            'ids' => 'nullable|string',
            'search' => 'nullable|string',
            'case_type' => 'nullable|string',
        ];
    }
}
