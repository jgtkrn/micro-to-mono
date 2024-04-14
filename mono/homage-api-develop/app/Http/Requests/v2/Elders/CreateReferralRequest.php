<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class CreateReferralRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'label' => 'required|unique:referrals,label',
            'code' => 'required|alpha_dash',
            'bzn_code' => 'required',
            'cga_code' => 'required',
        ];
    }
}
