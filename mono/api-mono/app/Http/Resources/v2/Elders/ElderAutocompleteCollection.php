<?php

namespace App\Http\Resources\v2\Elders;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ElderAutocompleteCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
