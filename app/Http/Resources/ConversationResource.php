<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'system_prompt' => $this->system_prompt,
            'last_message' => new MessageResource($this->whenLoaded('lastMessage')),
            'messages_count' => $this->messages_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 