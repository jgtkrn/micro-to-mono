<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBznConsultationNotesRequest extends FormRequest
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
            'assessment_time' => 'nullable|string',
            'sbp' => 'nullable|integer',
            'dbp' => 'nullable|integer',
            'pulse' => 'nullable|integer',
            'pao' => 'nullable|integer',
            'hstix' => 'nullable|integer',
            'body_weight' => 'nullable|integer',
            'waist' => 'nullable|integer',
            'circumference' => 'nullable|integer',
            'domain' => 'nullable|integer',
            'urgency' => 'nullable|integer',
            'category' => 'nullable|integer',
            'intervention_remark' => 'nullable|integer',
            'consultation_remark' => 'nullable|integer',
            'area' => 'nullable|integer',
            'priority' => 'nullable|integer',
            'target' => 'nullable|integer',
            'modifier' => 'nullable|integer',
            'ssa' => 'nullable|integer',
            'knowledge' => 'nullable|integer',
            'behaviour' => 'nullable|integer',
            'status' => 'nullable|integer',
            'omaha_s' => 'nullable|integer',
            'visiting_duration' => 'nullable|integer',
            'case_status' => 'nullable|integer',
            'case_remark' => 'nullable|integer',
            'signature_file' => 'nullable|max:12288',
            'attachment_file' => 'nullable|array',
            'attachment_file.*' => 'nullable|max:12288',
        ];
    }
}
