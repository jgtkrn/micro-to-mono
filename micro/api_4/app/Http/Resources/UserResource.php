<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nickname' => $this->nickname,
            'user_status' => $this->user_status,
            'staff_number' => $this->staff_number,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'email_cityu' => $this->email_cityu,
            'roles' => $this->roles,
            'teams' => $this->teams,
            'employment_status' => $this->employment_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'access_role' => $this->access_roles != null ? $this->access_roles->name : null
        ];
    }
}
