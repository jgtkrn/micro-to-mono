<?php

namespace App\Http\Resources\v2\Assessments;

use Illuminate\Http\Resources\Json\JsonResource;

class CgaCareTargetResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'care_plan_id' => $this->care_plan_id,
            'target' => $this->target,
            'health_vision' => $this->health_vision,
            'long_term_goal' => $this->long_term_goal,
            'short_term_goal' => $this->short_term_goal,
            'motivation' => $this->motivation,
            'early_change_stage' => $this->early_change_stage,
            'later_change_stage' => $this->later_change_stage,
            'cga_notes' => $this->cgaConsultationNotes,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ];
    }
}
