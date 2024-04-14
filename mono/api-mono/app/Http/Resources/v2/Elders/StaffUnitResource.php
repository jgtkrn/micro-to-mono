<?php

namespace App\Http\Resources\v2\Elders;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffUnitResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'unit_name' => $this->unit_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
