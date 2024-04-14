<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReferralRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'label' => 'sometimes|required|unique:referrals,label',
            'code' => 'sometimes|required|alpha_dash',
            'bzn_code' => 'sometimes|required',
            'cga_code' => 'sometimes|required',
        ];
    }
}
