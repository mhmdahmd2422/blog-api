<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'is_visible' => $this->when(Str::contains($request->route()->uri(), 'admin'), $this->is_visible),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
