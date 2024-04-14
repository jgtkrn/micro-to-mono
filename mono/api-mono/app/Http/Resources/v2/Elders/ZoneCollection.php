<?php

namespace App\Http\Resources\v2\Elders;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ZoneCollection extends ResourceCollection
{
    public $collects = ZoneResource::class;
}
