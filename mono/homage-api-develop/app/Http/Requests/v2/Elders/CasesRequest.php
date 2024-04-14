<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class CasesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // 'case_name' => ['required', 'max:20'],
            // 'caller_name' => ['required', 'max:120'],
            'case_number' => ['integer'],
            'case_status' => ['required'],
            'case_elder_remark' => ['nullable', 'string'],
            // 'elder_id' => ['required', 'exists:elders,id'],
        ];
    }
}
