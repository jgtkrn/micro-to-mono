<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CgaConsultationNotesIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cga_target_id' => 'nullable|integer|exists:cga_care_targets,id,deleted_at,NULL',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ];
    }
}
