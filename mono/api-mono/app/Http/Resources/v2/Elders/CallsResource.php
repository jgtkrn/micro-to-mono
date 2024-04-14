<?php

namespace App\Http\Resources\v2\Elders;

use Illuminate\Http\Resources\Json\JsonResource;

class CallsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'elder_uid' => $this->case->elder->uid,
            'elder_name' => $this->case->elder->name,
            'caller_id' => $this->caller_id,
            'cases_id' => $this->cases_id,
            'call_status' => $this->call_status,
            'call_date' => $this->call_date,
            'remark' => $this->remark,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'created_by' => $this->created_by,
            'updated_by_name' => $this->updated_by_name,
            'created_by_name' => $this->created_by_name,
        ];
    }
}
