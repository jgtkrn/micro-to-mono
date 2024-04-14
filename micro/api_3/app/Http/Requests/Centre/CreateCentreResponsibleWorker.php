<?php

namespace App\Http\Requests\Centre;

use Illuminate\Foundation\Http\FormRequest;

class CreateCentreResponsibleWorker extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:centre_responsible_workers,name',
            'code' => 'required|alpha_dash',
        ];
    }
}
