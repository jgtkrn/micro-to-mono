<?php

namespace App\Http\Resources\Elder;

use Illuminate\Http\Resources\Json\JsonResource;

class ElderCasesResource extends JsonResource
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
            'name' => $this->name,
            'uid' => $this->uid,
            'case_type' => $this->case_type,
            'gender' => $this->gender,
            'birth_day' => $this->birth_day,
            'birth_month' => $this->birth_month,
            'birth_year' => $this->birth_year,
            'contact_number' => $this->contact_number,
            'address' => $this->address,
            'emergency_contact_number' => $this->emergency_contact_number,
            'emergency_contact_name' => $this->emergency_contact_name,
            'relationship' => $this->relationship,
            'uid_connected_with' => $this->uid_connected_with,
            'health_issue' => $this->health_issue,
            'medication' => $this->medication,
            'limited' => $this->limited_mobility,
            'elder_remark' => $this->elder_remark,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'district_id' => $this->district_id,
            'district' => $this->district->district_name,
            'cases' => $this->cases,
        ];
    }
}
