<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class ElderAutocompleteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'per_page' => 'sometimes|integer|min:1',
            'name' => 'sometimes|string',
            'uid' => 'sometimes|string',
            'ids' => 'sometimes|string',
            'search' => 'sometimes|string',
            'case_type' => 'sometimes|string',
        ];
    }
}
