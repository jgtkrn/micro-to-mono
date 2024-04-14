<?php

namespace App\Http\Requests\v2\Appointments;

use Illuminate\Foundation\Http\FormRequest;

class GetTodayUsersRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'nullable|date',
        ];
    }
}
