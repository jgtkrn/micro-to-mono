<?php

namespace App\Http\Resources\v2\Users;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TeamCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
