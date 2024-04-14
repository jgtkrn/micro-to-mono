<?php

namespace App\Http\Resources\v2\Assessments;

use Illuminate\Http\Resources\Json\JsonResource;

class BznCareTargetResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'care_plan_id' => $this->care_plan_id,
            'intervention' => $this->intervention,
            'target_type' => $this->target_type,
            'plan' => $this->plan,
            'ct_domain' => $this->ct_domain,
            'ct_urgency' => $this->ct_urgency,
            'ct_category' => $this->ct_category,
            'ct_area' => $this->ct_area,
            'ct_priority' => $this->ct_priority,
            'ct_target' => $this->ct_target,
            'ct_modifier' => $this->ct_modifier,
            'ct_ssa' => $this->ct_ssa,
            'ct_knowledge' => $this->ct_knowledge,
            'ct_behaviour' => $this->ct_behaviour,
            'ct_status' => $this->ct_status,
            'omaha_s' => $this->omaha_s,
            'bzn_notes' => $this->bznConsultationNotes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ];
    }
}
