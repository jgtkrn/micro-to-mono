<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class SatisfactionEvaluationFormStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'elder_reference_number' => 'nullable|string',
            'assessor_name' => 'nullable|string',
            'evaluation_date' => 'nullable|date',
            'case_id' => 'nullable|exists:cases,id',
            'clear_plan' => 'nullable|string',
            'enough_discuss_time' => 'nullable|string',
            'appropriate_plan' => 'nullable|string',
            'has_discussion_team' => 'nullable|string',
            'own_involved' => 'nullable|string',
            'enough_opportunities' => 'nullable|string',
            'enough_information' => 'nullable|string',
            'selfcare_improved' => 'nullable|string',
            'confidence_team' => 'nullable|string',
            'feel_respected' => 'nullable|string',
            'performance_rate' => 'nullable|string',
            'service_scale' => 'nullable|string',
            'recommend_service' => 'nullable|string',
            'created_by' => 'nullable|integer',
            'updated_by' => 'nullable|integer',
            'created_by_name' => 'nullable|string',
            'updated_by_name' => 'nullable|string',
        ];
    }
}
