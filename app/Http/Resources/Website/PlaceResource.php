<?php

namespace App\Http\Resources\Website;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->title,
            'description' => $this->description,
            'images' => $this->when($this->images->count(), ImageResource::collection($this->whenLoaded('images'))),
            'tags' => $this->when($this->tags->count(), TagResource::collection($this->whenLoaded('tags'))),
            'specifications' => SpecificationResource::collection($this->whenLoaded('specifications')),
        ];
    }
}
