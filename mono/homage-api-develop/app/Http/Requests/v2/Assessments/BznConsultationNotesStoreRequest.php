<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class BznConsultationNotesStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bzn_target_id' => 'nullable|integer|exists:bzn_care_targets,id,deleted_at,NULL',
            'assessor' => 'nullable|string',
            'meeting' => 'nullable|string',
            'visit_type' => 'nullable|string',
            'assessment_date' => 'nullable|date',
            'assessment_time' => 'nullable',
            'sbp' => 'nullable|integer',
            'dbp' => 'nullable|integer',
            'pulse' => 'nullable|integer',
            'pao' => 'nullable|integer',
            'hstix' => 'nullable',
            'body_weight' => 'nullable|integer',
            'waist' => 'nullable|integer',
            'circumference' => 'nullable|string',
            'domain' => 'nullable|integer',
            'urgency' => 'nullable|integer',
            'category' => 'nullable|integer',
            'intervention_remark' => 'nullable|string',
            'consultation_remark' => 'nullable|string',
            'area' => 'nullable|string',
            'priority' => 'nullable|integer',
            'target' => 'nullable|string',
            'modifier' => 'nullable|integer',
            'ssa' => 'nullable|string',
            'knowledge' => 'nullable|integer',
            'behaviour' => 'nullable|integer',
            'status' => 'nullable|integer',
            'omaha_s' => 'nullable|string',
            'visiting_duration' => 'nullable|string',
            'case_status' => 'nullable|integer',
            'case_remark' => 'nullable|string',
            'signature_file' => 'nullable|max:12288',
            'attachment_file' => 'nullable|array',
            'attachment_file.*' => 'nullable|max:12288',
        ];
    }
}
