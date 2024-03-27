<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'images' => $this->when($this->images->count(), ImageResource::collection($this->whenLoaded('images'))),
            'categories' => CategoryResource::collection($this->whenLoaded('visibleCategories')),
        ];
    }
}
