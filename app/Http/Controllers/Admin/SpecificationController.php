<?php

namespace App\Http\Controllers\Admin;

use App\Filters\Admin\SpecificationFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSpecificationRequest;
use App\Http\Requests\Admin\UpdateSpecificationRequest;
use App\Http\Resources\Admin\SpecificationResource;
use App\Models\Specification;
use Illuminate\Http\Response;

class SpecificationController extends Controller
{
    public function index(SpecificationFilter $filters): Response
    {
        $paginationLength = pagination_length('specification');

        return response([
            'specifications' => SpecificationResource::collection(Specification::with('image')
                ->filter($filters)->get())->paginate($paginationLength)->withQueryString()
        ]);
    }

    public function store(StoreSpecificationRequest $request): Response
    {
        $specification = $request->storeSpecification();

        return response([
            'specification' => SpecificationResource::make($specification->load('image')),
            'message' => __('specifications.store')
        ]);
    }

    public function show(Specification $specification): Response
    {
        return response([
            'specification' => SpecificationResource::make($specification->load('image', 'places')),
        ]);
    }

    public function update(UpdateSpecificationRequest $request, Specification $specification): Response
    {
        $specification = $request->updateSpecification();

        return response([
            'specification' => SpecificationResource::make($specification->load('image')),
            'message' => __('specifications.update')
        ]);
    }

    public function destroy(Specification $specification): Response
    {
        if ($specification->remove()) {
            return response([
                'message' => __('specifications.destroy')
            ]);
        }

        return response([
            'message' => __('specifications.cant_destroy'),
        ], 409);
    }
}
