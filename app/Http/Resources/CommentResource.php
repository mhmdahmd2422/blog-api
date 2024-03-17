<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'post' => $this->whenLoaded('post', fn() => PostResource::make($this->post)),
            'body' => $this->body,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
