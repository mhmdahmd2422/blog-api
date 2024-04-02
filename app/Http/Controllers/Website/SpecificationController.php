<?php

namespace App\Http\Controllers\Website;

use App\Filters\Website\SpecificationFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Website\SpecificationResource;
use App\Models\Specification;
use Illuminate\Http\Response;

class SpecificationController extends Controller
{
    public function index(SpecificationFilter $filters): Response
    {
        $paginationLength = pagination_length('specification');

        return response([
            'specifications' => SpecificationResource::collection(Specification::with('image')
                ->filter($filters)->paginate($paginationLength))
        ]);
    }

    public function show(Specification $specification): Response
    {
        return response([
            'specification' => SpecificationResource::make($specification->load('image', 'places')),
        ]);
    }
}
