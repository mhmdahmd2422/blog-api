<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
          'id' => $this->id,
          'name' => $this->name,
          'posts' => PostResource::collection($this->whenLoaded('posts')),
          'comments' => CommentResource::collection($this->whenLoaded('comments'))
        ];
    }
}
