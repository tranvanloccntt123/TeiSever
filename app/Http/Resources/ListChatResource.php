<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListChatResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'group' => [
                'id' => $this->group_message_id,
                'name' => $this->groupName
            ],
            'user' => [
                'id' => $this->user_id,
                'name' => $this->userName
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
