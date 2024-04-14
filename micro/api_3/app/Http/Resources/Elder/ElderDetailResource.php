<?php

namespace App\Http\Resources\Elder;

use Illuminate\Http\Resources\Json\JsonResource;

class ElderDetailResource extends JsonResource
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
            'contact_number' => $this->contact_number,
            'address' => $this->address,
        ];
    }
}
