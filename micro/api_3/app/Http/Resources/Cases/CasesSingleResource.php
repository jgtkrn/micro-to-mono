<?php

namespace App\Http\Resources\Cases;

use App\Http\Resources\Elder\ElderSingleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CasesSingleResource extends JsonResource
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
            'user_type' => $this->case_name,
            'case_number' => $this->case_number,
            'caller_name' => $this->caller_name,
            'case_status' => $this->case_status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_by_name' => $this->created_by_name,
            'updated_by_name' => $this->updated_by_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'elder' => new ElderSingleResource($this->elder),
            'calls' => $this->calls,
            'cga_type' => $this->cga_type,
        ];
    }
}
