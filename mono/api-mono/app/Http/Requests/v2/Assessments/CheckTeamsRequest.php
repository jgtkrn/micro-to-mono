<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CheckTeamsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_teams' => 'nullable|array',
            'is_cga' => 'nullable|boolean',
            'is_bzn' => 'nullable|boolean',
        ];
    }
}
