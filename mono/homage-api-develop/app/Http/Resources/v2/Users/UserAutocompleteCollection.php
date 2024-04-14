<?php

namespace App\Http\Resources\v2\Users;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserAutocompleteCollection extends ResourceCollection
{
    public $collects = UserAutocompleteResource::class;
}
