<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Resources\Website\PlaceResource;
use App\Http\Resources\Website\PlaceSimpleResource;
use App\Models\Place;
use Illuminate\Http\Response;

class PlaceController extends Controller
{
    public function index(): Response
    {
        $paginationLength = pagination_length('place');

        return response([
            'places' => PlaceSimpleResource::collection(Place::visible()->get()->load('images', 'tags', 'specifications'))
                ->paginate($paginationLength)
        ]);
    }

    public function show(Place $place): Response
    {
        return response([
            'place' => PlaceResource::make($place->load('images', 'tags', 'specifications')),
        ]);
    }
}
