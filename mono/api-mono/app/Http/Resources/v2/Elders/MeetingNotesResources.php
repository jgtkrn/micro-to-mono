<?php

namespace App\Http\Resources\v2\Elders;

use Illuminate\Http\Resources\Json\JsonResource;

class MeetingNotesResources extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'notes' => $this->notes,
            'cases_id' => $this->cases_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_by_name' => $this->created_by_name,
            'updated_by_name' => $this->updated_by_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
