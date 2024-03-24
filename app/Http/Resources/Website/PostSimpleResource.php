<?php

namespace App\Http\Resources\Website;

use App\Http\Resources\Admin\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostSimpleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'created_at' => $this->created_at,
            'image' => ImageResource::make($this->oldestImage),
            'images_count' => $this->images()->count(),
            'categories_count' => $this->visibleCategories()->count(),
            'comments_count' => $this->comments()->count()
        ];
    }
}
