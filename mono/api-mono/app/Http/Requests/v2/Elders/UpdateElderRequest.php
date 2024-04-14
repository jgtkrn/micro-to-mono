<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateElderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'birth_day' => 'nullable|min:1|max:31',
            'birth_month' => 'nullable|integer|min:1|max:12',
            'birth_year' => [
                'nullable',
                'digits:4',
                'integer',
                'min:1900',
                'max:' . date('Y') + 1,
            ],
        ];
    }
}
