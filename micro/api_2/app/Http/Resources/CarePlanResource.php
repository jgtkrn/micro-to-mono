<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarePlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'case_id' => $this->case_id,
            'case_type' => $this->case_type,
            'case_manager' => $this->case_manager,
            'handler' => $this->handler,
            'manager_id' => $this->manager_id,
            'handler_id' => $this->handler_id,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
            'bzn_notes' => $this->bznNotes,
            'cga_notes' => $this->cgaNotes,
            'coaching_pam' => $this->coachingPam,
            'pre_coaching_pam' => $this->preCoachingPam,
            'other_managers' => $this->caseManagers
        ];
    }
}
