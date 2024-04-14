<?php

namespace App\Http\Resources\Cases;

use Illuminate\Http\Resources\Json\JsonResource;

class CasesResources extends JsonResource
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
            'elder_uid' => $this->elder->uid,
            'elder_name' => $this->elder->name,
            'elder_remark' => $this->elder->elder_remark,
            'elder_contact_number' => $this->elder->contact_number,
            'user_type' => $this->case_name,
            'case_number' => $this->case_number,
            'case_status' => $this->case_status,
            'district' => $this->elder->district->district_name,
            'zone' => $this->elder->zone->name,
            'case_manager' => $this->case_manager,
            'first_visit' => $this->first_visit,
            'last_visit' => $this->last_visit,
            'total_visit' => $this->total_visit,
            'cga_type' => $this->cga_type,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
