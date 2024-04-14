<?php

namespace App\Http\Requests\v2\Users;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'code' => 'required|unique:roles,code',
        ];
    }
}
