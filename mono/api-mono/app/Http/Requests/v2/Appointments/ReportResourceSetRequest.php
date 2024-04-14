<?php

namespace App\Http\Requests\v2\Appointments;

use Illuminate\Foundation\Http\FormRequest;

class ReportResourceSetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ];
    }
}
