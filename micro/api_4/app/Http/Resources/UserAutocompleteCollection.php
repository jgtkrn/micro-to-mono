<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserAutocompleteCollection extends ResourceCollection
{
    public $collects = UserAutocompleteResource::class;
}
