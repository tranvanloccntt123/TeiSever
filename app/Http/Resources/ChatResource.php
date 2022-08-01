<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
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
            'group_message_id' => $this->group_message_id,
            'type' => $this->type,
            'content' => $this->content,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->userName
            ]
        ];
    }
}
