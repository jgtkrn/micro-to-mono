<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class PhoneNumberAvailabilityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'contact_number' => 'nullable|string',
        ];
    }
}
