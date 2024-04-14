<?php

namespace App\Http\Requests\v2\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:50',
            'email' => 'sometimes|required|email|unique:users,email',
            'phone_number' => 'nullable',
            'roles' => 'sometimes|required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'teams' => 'sometimes|required|array|min:1',
            'teams.*' => 'exists:teams,id',
            'employment_status' => 'sometimes|required|in:FT,PT',
            'nickname' => 'sometimes|required',
            'staff_number' => 'sometimes|required|unique:users,staff_number',
            'access_role_id' => 'nullable|exists:access_roles,id',
        ];
    }
}
