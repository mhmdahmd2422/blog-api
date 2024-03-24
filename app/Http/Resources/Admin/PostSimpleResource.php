<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostSimpleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'is_visible' => $this->is_visible,
            'created_at' => $this->created_at,
            'image' => ImageResource::make($this->oldestImage),
            'images_count' => $this->images()->count(),
            'categories_count' => $this->categories()->count(),
            'comments_count' => $this->comments()->count()
        ];
    }
}
