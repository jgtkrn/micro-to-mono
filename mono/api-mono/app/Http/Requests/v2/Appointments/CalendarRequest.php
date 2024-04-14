<?php

namespace App\Http\Requests\v2\Appointments;

use Illuminate\Foundation\Http\FormRequest;

class CalendarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date', 'after_or_equal:start'],
            'category_id' => ['nullable', 'string'],
            'user_ids' => ['nullable', 'string'],
            'search' => ['nullable', 'string'],
            'case_type' => ['nullable', 'string'],
            'team_ids' => ['nullable', 'string'],
        ];
    }
}
