<?php

namespace App\Http\Requests\Referral;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReferralRequest extends FormRequest
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
            'label' => 'sometimes|required|unique:referrals,label',
            'code' => 'sometimes|required|alpha_dash',
            'bzn_code' => 'sometimes|required',
            'cga_code' => 'sometimes|required',
        ];
    }
}
