<?php

namespace App\Http\Requests\v2\Appointments;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'nullable',
            'day_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'category_id' => 'nullable|integer',
            'case_id' => 'nullable|integer',
            'elder_id' => 'nullable|integer',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'nullable|integer',
            'attachment_ids' => 'nullable|array',
            'attachment_ids.*' => 'nullable|integer',
        ];
    }
}
