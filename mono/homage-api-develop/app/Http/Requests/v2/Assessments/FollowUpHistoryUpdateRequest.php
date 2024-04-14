<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class FollowUpHistoryUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'time' => ['nullable', 'date'],
            'appointment_other_text' => ['nullable', 'string'],
            'appointment_id' => ['nullable', 'integer'],
        ];
    }
}
