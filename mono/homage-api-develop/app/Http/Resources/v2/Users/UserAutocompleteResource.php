<?php

namespace App\Http\Resources\v2\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAutocompleteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->nickname,
            'email' => $this->email,
        ];
    }
}
