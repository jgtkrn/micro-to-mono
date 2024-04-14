<?php

namespace App\Http\Requests\v2\Users;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|exists:roles,id',
            'teams' => 'required|array|min:1',
            'teams.*' => 'required|exists:teams,id',
            'employment_status' => 'sometimes|required|in:FT,PT',
            'nickname' => 'required',
            'staff_number' => 'required|unique:users,staff_number',
            'access_role_id' => 'nullable|exists:access_roles,id',
        ];
    }
}
