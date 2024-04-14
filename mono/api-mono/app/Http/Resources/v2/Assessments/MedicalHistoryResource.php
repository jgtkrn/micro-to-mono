<?php

namespace App\Http\Resources\v2\Assessments;

use Illuminate\Http\Resources\Json\JsonResource;

class MedicalHistoryResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
