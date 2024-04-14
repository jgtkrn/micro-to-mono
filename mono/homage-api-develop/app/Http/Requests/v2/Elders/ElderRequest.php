<?php

namespace App\Http\Requests\v2\Elders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ElderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $name = $this->request->get('name');
        $gender = $this->request->get('gender');
        //mapping M/F to male/female
        if ($gender == 'M' || $gender == 'm') {
            $gender = 'male';
        } elseif ($gender == 'F' || $gender == 'f') {
            $gender = 'female';
        }
        $contact_number = $this->request->get('contact_number');
        $birth_year = $this->request->get('birth_year');
        $id = $this->route('elder');
        $unique_combination_rules = Rule::unique('elders')->where(
            function ($query) use ($name, $gender, $contact_number, $birth_year) {
                return $query->where(
                    [
                        ['name', '=', $name],
                        ['gender', '=', $gender],
                        ['contact_number', '=', $contact_number],
                        ['birth_year', '=', $birth_year],
                    ]
                );
            }
        )->ignore($id);

        return [
            'name' => [
                'required',
                'max:120',
                // $unique_combination_rules
            ],
            'name_en' => [
                'nullable',
                'max:120',
            ],
            'gender' => [
                'required',
                'in:male,female,M,F,m,f', //case sensitive
            ],
            'contact_number' => [
                'required',
                'digits:8',
                // $unique_combination_rules
            ],
            'uid' => 'nullable',
            'second_contact_number' => 'sometimes|digits_between:8,13',
            'third_contact_number' => 'sometimes|digits_between:8,13',
            'address' => 'required',
            'birth_day' => 'sometimes|required|min:1|max:31',
            'birth_month' => 'sometimes|required|integer|min:1|max:12',
            'birth_year' => [
                'required',
                'digits:4',
                'integer',
                'min:1900',
                'max:' . date('Y') + 1,
                // $unique_combination_rules
            ],
            'district' => 'required|exists:districts,district_name',
            'zone' => 'required',
            'language' => 'nullable',
            'centre_case_id' => 'sometimes',
            'centre_responsible_worker' => 'nullable',
            'responsible_worker_contact' => 'sometimes',
            'case_type' => 'nullable|in:CGA,BZN',
            'source_of_referral' => 'nullable|exists:referrals,label',
            'relationship' => 'sometimes',
            'emergency_contact_number' => 'nullable|digits_between:8,13',
            'emergency_contact_number_2' => 'sometimes|digits_between:8,13',
            'emergency_contact_name' => 'sometimes',
            'emergency_contact_relationship_other' => 'sometimes',
            'emergency_contact_2_number' => 'sometimes|digits_between:8,13',
            'emergency_contact_2_number_2' => 'sometimes|digits_between:8,13',
            'emergency_contact_2_name' => 'sometimes',
            'emergency_contact_2_relationship_other' => 'sometimes',
            'elder_remark' => 'nullable|string',
            'ccec_number' => 'nullable|string',
            'ccec_number_2' => 'nullable|string',
            'ccec_2_number' => 'nullable|string',
            'ccec_2_number_2' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Combination of name, gender, contact_number, and birth_year already taken',
            'gender.unique' => 'Combination of name, gender, contact_number, and birth_year already taken',
            'contact_number.unique' => 'Combination of name, gender, contact_number, and birth_year already taken',
            'birth_year.unique' => 'Combination of name, gender, contact_number, and birth_year already taken',
        ];
    }
}
