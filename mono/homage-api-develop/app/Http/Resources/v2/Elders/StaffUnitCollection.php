<?php

namespace App\Http\Resources\v2\Elders;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StaffUnitCollection extends ResourceCollection
{
    public $collects = ZoneResource::class;
}
