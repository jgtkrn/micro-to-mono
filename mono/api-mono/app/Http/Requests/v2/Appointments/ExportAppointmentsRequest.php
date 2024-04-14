<?php

namespace App\Http\Requests\v2\Appointments;

use Illuminate\Foundation\Http\FormRequest;

class ExportAppointmentsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'access_role' => 'nullable',
        ];
    }
}
