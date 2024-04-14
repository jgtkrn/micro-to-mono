<?php

namespace App\Http\Resources\v2\Elders;

use Illuminate\Http\Resources\Json\JsonResource;

class ElderSingleResource extends JsonResource
{
    public function toArray($request)
    {
        $centre_responsible_worker = null;
        $centre_responsible_worker_code = (bool) $this->centreResponsibleWorker ? $this->centreResponsibleWorker->code : null;
        if ($centre_responsible_worker_code) {
            if ($centre_responsible_worker_code == 'other') {
                $centre_responsible_worker = $this->centre_responsible_worker_other;
            } else {
                $centre_responsible_worker = $this->centreResponsibleWorker->name;
            }
        }

        $zone = null;
        $zone_code = (bool) $this->zone ? $this->zone->code : null;
        if ($zone_code) {
            if ($zone_code == 'other') {
                $zone = $this->zone_other;
            } else {
                $zone = $this->zone->name;
            }
        }

        return [
            'id' => $this->id,
            'uid' => $this->uid,
            'name' => $this->name,
            'name_en' => $this->name_en,
            'gender' => $this->gender,
            'birth_day' => $this->birth_day,
            'birth_month' => $this->birth_month,
            'birth_year' => (int) $this->birth_year,
            'contact_number' => $this->contact_number,
            'second_contact_number' => $this->second_contact_number,
            'third_contact_number' => $this->third_contact_number,
            'address' => $this->address,
            'district_id' => $this->district_id,
            'district' => $this->district->district_name,
            'zone_id' => $this->zone_id,
            'zone' => $zone,
            'language' => $this->language,
            'centre_case_id' => $this->centre_case_id,
            'centre_responsible_worker_id' => $this->centre_responsible_worker_id,
            'centre_responsible_worker' => $centre_responsible_worker,
            'responsible_worker_contact' => $this->responsible_worker_contact,
            'relationship' => $this->relationship,
            'uid_connected_with' => $this->uid_connected_with,
            'case_type' => $this->case_type,
            'referral_id' => $this->referral_id,
            'source_of_referral' => $this->referral->label,
            'emergency_contact_number' => $this->emergency_contact_number,
            'emergency_contact_number_2' => $this->emergency_contact_number_2,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_relationship_other' => $this->emergency_contact_relationship_other,
            'emergency_contact_2_number' => $this->emergency_contact_2_number,
            'emergency_contact_2_number_2' => $this->emergency_contact_2_number_2,
            'emergency_contact_2_name' => $this->emergency_contact_2_name,
            'emergency_contact_2_relationship_other' => $this->emergency_contact_2_relationship_other,
            'elder_remark' => $this->elder_remark,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'ccec_number' => $this->ccec_number,
            'ccec_number_2' => $this->ccec_number_2,
            'ccec_2_number' => $this->ccec_2_number,
            'ccec_2_number_2' => $this->ccec_2_number_2,
        ];
    }
}
