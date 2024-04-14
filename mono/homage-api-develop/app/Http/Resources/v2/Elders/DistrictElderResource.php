<?php

namespace App\Http\Resources\v2\Elders;

use Illuminate\Http\Resources\Json\JsonResource;

class DistrictElderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'district_name' => $this->district_name,
            'bzn_code' => $this->bzn_code,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'list_elder' => $this->elders,
        ];
    }
}
