<?php

namespace App\Http\Requests\v2\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'device_name' => 'required',
        ];
    }
}
