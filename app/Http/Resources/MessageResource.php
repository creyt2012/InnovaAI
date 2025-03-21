<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'user' => $this->when($this->user_id, new UserResource($this->user)),
        ];
    }
} 