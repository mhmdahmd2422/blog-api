<?php

namespace App\Http\Controllers\Website;

use App\Filters\Website\PlaceFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Website\PlaceResource;
use App\Http\Resources\Website\PlaceSimpleResource;
use App\Models\Place;
use Illuminate\Http\Response;

class PlaceController extends Controller
{
    public function index(PlaceFilter $filters): Response
    {
        $paginationLength = pagination_length('place');

        return response([
            'places' => PlaceSimpleResource::collection(Place::with('images')->filter($filters)
                ->visible()->get())->paginate($paginationLength)->withQueryString()
        ]);
    }

    public function show(Place $place): Response
    {
        return response([
            'place' => PlaceResource::make($place->load('images', 'tags', 'specifications')),
        ]);
    }
}
