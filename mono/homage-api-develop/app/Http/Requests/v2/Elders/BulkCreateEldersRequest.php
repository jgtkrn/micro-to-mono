<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class BulkCreateEldersRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'elders' => 'required|array',
            'elders.*.name' => 'nullable|string',
            'elders.*.birth_day' => 'nullable|integer',
            'elders.*.birth_month' => 'nullable|integer',
            'elders.*.birth_year' => 'nullable|integer',
            'elders.*.contact_number' => 'nullable',
            'elders.*.gender' => 'nullable|string',
        ];
    }
}
