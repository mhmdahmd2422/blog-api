<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->whenPivotLoaded('place_specification', fn() => $this->pivot->description),
            'icon' => ImageResource::make($this->whenLoaded('image', $this->icon)),
            'places' => PlaceSimpleResource::collection($this->whenLoaded('places'))
        ];
    }
}
