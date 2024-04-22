<?php

namespace App\Http\Controllers\Admin;

use App\Filters\Admin\PlaceFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlaceRequest;
use App\Http\Requests\Admin\UpdatePlaceRequest;
use App\Http\Resources\Admin\PlaceResource;
use App\Http\Resources\Admin\PlaceSimpleResource;
use App\Models\Place;
use Illuminate\Http\Response;

class PlaceController extends Controller
{
    public function index(PlaceFilter $filters): Response
    {
        $paginationLength = pagination_length('place');

        return response([
            'places' => PlaceSimpleResource::collection(Place::with('images')
                ->filter($filters)->get())->paginate($paginationLength)->withQueryString()
        ]);
    }

    public function store(StorePlaceRequest $request): Response
    {
        $place = $request->storePlace();

        return response([
            'place' => PlaceResource::make($place->load('images', 'tags', 'specifications')),
            'message' => __('places.store')
        ]);
    }

    public function show(Place $place): Response
    {
        return response([
            'place' => PlaceResource::make($place->load('images', 'tags', 'specifications')),
        ]);
    }

    public function update(UpdatePlaceRequest $request, Place $place): Response
    {
        $place = $request->updatePlace();

        return response([
            'place' => PlaceResource::make($place->load('images', 'tags', 'specifications')),
            'message' => __('places.update')
        ]);
    }

    public function destroy(Place $place): Response
    {
        if ($place->remove()) {
            return response([
                'message' => __('places.destroy')
            ]);
        }

        return response([
            'message' => __('places.cant_destroy'),
        ], 409);
    }
}
