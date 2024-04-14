<?php

namespace App\Http\Requests\v2\Assessments;

use Illuminate\Foundation\Http\FormRequest;

class CgaConsultationNotesStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'cga_target_id' => 'nullable|integer|exists:cga_care_targets,id,deleted_at,NULL',
            'signature_file' => 'nullable|max:12288',
            'attachment_file' => 'nullable|array',
            'attachment_file.*' => 'nullable|max:12288',
            'assessor_1' => 'nullable|string',
            'assessor_2' => 'nullable|string',
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
            'purpose' => 'nullable|string',
            'content' => 'nullable|string',
            'progress' => 'nullable|string',
            'case_summary' => 'nullable|string',
            'followup_options' => 'nullable|integer',
            'followup' => 'nullable|string',
            'personal_insight' => 'nullable|string',
            'visiting_duration' => 'nullable|string',
            'case_status' => 'nullable|integer',
            'case_remark' => 'nullable|string',
        ];
    }
}
