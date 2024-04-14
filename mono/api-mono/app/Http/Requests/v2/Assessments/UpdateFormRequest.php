<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'form_name' => ['required', Rule::in(
                'physiological_measurement',
                're_physiological_measurement',
                'medical_condition',
                'medication_adherence',
                'lubben_social_network_scale',
                'social_background',
                'function_mobility',
                'barthel_index',
                'geriatric_depression_scale',
                'iadl',
                'genogram',
                'montreal_cognitive_assessment',
                'physical_condition',
                'assessment_case_status',
                'attachment',
                'signature',
                'qualtrics',
                'social_worker',
            )],
        ];
    }
}
