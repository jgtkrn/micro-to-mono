<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class CreateCentreResponsibleWorker extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:centre_responsible_workers,name',
            'code' => 'required|alpha_dash',
        ];
    }
}
