<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostSimpleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'created_at' => $this->created_at,
            'image' => ImageResource::make($this->main_image),
            'images_count' => $this->images()->count(),
            'categories_count' => $this->visible_categories->count(),
            'comments_count' => $this->comments()->count()
        ];
    }
}
