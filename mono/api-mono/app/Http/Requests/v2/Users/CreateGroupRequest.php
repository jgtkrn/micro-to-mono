<?php

namespace App\Http\Requests\v2\Users;

use Illuminate\Foundation\Http\FormRequest;

class CreateGroupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'code' => 'required|unique:groups,code',
            'users' => 'sometimes|required|array|min:2',
            'users.*' => 'exists:users,id',
        ];
    }
}
