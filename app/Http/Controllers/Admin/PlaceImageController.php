<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlaceImageRequest;
use App\Http\Requests\Admin\UpdatePlaceImageRequest;
use App\Http\Resources\Admin\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Response;

class PlaceImageController extends Controller
{
    public function store(StorePlaceImageRequest $request, Place $place): Response
    {
        $place = $request->storeImages();

        return response([
            'place' => PlaceResource::make($place->load('images')),
            'message' => __('places.image.store')
        ]);
    }

    public function update(UpdatePlaceImageRequest $request, Place $place, string $imageId): Response
    {
        $place = $request->updateImage();

        if (! $place) {
            return response('', 404);
        }

        return response([
            'place' => PlaceResource::make($place->load('images')),
            'message' => __('places.image.update')
        ]);
    }

    public function destroy(Place $place, string $imageId): Response
    {
        if (! $place->destroyImage($imageId)) {
            return response('', 404);
        }

        return response([
            'message' => __('places.image.destroy')
        ]);
    }
}
