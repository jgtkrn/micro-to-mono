<?php

namespace App\Http\Requests\v2\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required',
            'users' => 'sometimes|required|array|min:2',
            'users.*' => 'exists:users,id',
        ];
    }
}
