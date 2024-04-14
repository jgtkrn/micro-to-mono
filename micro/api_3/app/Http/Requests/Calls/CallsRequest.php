<?php

namespace App\Http\Requests\Calls;

use App\Models\Cases;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CallsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
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
