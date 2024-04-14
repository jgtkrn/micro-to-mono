<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user_ids = array();
        $obj_length = count($this->user);
        for($i = 0; $i < $obj_length; $i ++) {
            $user_ids[$i] = $this->user[$i]['user_id'];
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start,
            'end' => $this->end,
            'remark' => $this->remark,
            'case_id' => $this->case_id,
            'category_id' => $this->category_id,
            'user_ids' => $user_ids,
            'elder_id' => $this->elder_id,
            'elder' => isset($this->elder) ? $this->elder : null,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_by_name' => $this->created_by_name,
            'updated_by_name' => $this->updated_by_name,
        ];
    }
}
