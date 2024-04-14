<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class BznConsultationNotesIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bzn_target_id' => 'nullable|integer|exists:bzn_care_targets,id,deleted_at,NULL',
            'from' => 'nullable|string',
            'to' => 'nullable|string|after_or_equal:from',
        ];
    }
}
