<?php

namespace App\Http\Resources\v2\Assessments;

use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentCaseStatusResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'remarks' => $this->remarks,
        ];
    }
}
