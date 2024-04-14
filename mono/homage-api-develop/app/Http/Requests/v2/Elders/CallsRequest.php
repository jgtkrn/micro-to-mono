<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class CallsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'caller_id' => ['required', 'integer'],
            'cases_id' => ['required', 'integer', 'exists:cases,id'],
            'call_date' => ['required', 'date_format:Y-m-d'],
            'call_status' => ['required'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
