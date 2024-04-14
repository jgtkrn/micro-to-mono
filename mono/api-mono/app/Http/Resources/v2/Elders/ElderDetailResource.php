<?php

namespace App\Http\Resources\v2\Elders;

use Illuminate\Http\Resources\Json\JsonResource;

class ElderDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'uid' => $this->uid,
            'contact_number' => $this->contact_number,
            'address' => $this->address,
        ];
    }
}
