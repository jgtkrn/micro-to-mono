<?php

namespace App\Http\Resources\v2\Assessments;

use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentCaseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'case_id' => $this->case_id,
            'case_type' => $this->case_type,
            'first_assessor' => $this->first_assessor,
            'second_assessor' => $this->second_assessor,
            'assessment_date' => $this->assessment_date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'priority_level' => $this->priority_level,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'assessment_case_status' => new AssessmentCaseStatusResource($this->whenLoaded('assessmentCaseStatus')),
            'forms_submitted' => $this->case_type == 'BZN' ?
                $this->when($request->isMethod('get'), [ //case type BZN
                    $this->getStatus('physiological_measurement', 'physiologicalMeasurementForm'),
                    $this->getStatus('physical_condition', 'physicalConditionForm'),
                    $this->getStatus('re_physiological_measurement', 'rePhysiologicalMeasurementForm'),
                    $this->getStatus('medical_condition', 'medicalConditionForm'),
                    $this->getStatus('lubben_social_network_scale', 'lubbenSocialNetworkScaleForm'),
                    $this->getStatus('social_background', 'socialBackgroundForm'),
                    $this->getStatus('medication_adherence', 'medicationAdherenceForm'),
                    $this->getStatus('function_mobility', 'functionMobilityForm'),
                    $this->getStatus('barthel_index', 'barthelIndexForm'),
                    $this->getStatus('geriatric_depression_scale', 'geriatricDepressionScaleForm'),
                    $this->getStatus('iadl', 'iadlForm'),
                    $this->getStatus('genogram', 'genogramForm'),
                    $this->getStatus('montreal_cognitive_assessment', 'montrealCognitiveAssessmentForm'),
                    $this->getStatus('assessment_case_status', 'assessmentCaseStatus'),
                    $this->getStatus('attachment', 'assessmentCaseAttachment'),
                    $this->getStatus('signature', 'assessmentCaseSignature'),
                ]) :
                $this->when($request->isMethod('get'), [ //case type CGA
                    $this->getStatus('montreal_cognitive_assessment', 'montrealCognitiveAssessmentForm'),
                    $this->getStatus('assessment_case_status', 'assessmentCaseStatus'),
                    $this->getStatus('attachment', 'assessmentCaseAttachment'),
                    $this->getStatus('signature', 'assessmentCaseSignature'),
                    $this->getStatus('qualtrics', 'qualtricsForm'),
                    $this->getStatus('social_worker', 'socialWorkerForm'),
                ]),
        ];
    }

    private function getStatus($form_name, $relationship)
    {
        $status = $this->whenLoaded($relationship);

        if ($relationship == 'assessmentCaseAttachment') { //collection
            $status = $this->relationLoaded($relationship) ? $status->first() : null;
        }

        $data = [
            'name' => $form_name,
            'submit' => isset($status),
        ];

        return $data;
    }
}
