<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCentreResponsibleWorker extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|unique:centre_responsible_workers,name',
            'code' => 'sometimes|required|alpha_dash',
        ];
    }
}
