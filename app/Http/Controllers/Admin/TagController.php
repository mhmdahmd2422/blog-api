<?php

namespace App\Http\Controllers\Admin;

use App\Filters\Admin\TagFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Http\Resources\Admin\TagResource;
use App\Models\Tag;
use Illuminate\Http\Response;

class TagController extends Controller
{
    public function index(TagFilter $filters): Response
    {
        $paginationLength = pagination_length('tag');

        return response([
            'tags' => TagResource::collection(Tag::with('places')
                ->filter($filters)->paginate($paginationLength))
        ]);
    }

    public function store(StoreTagRequest $request): Response
    {
        $tag = $request->storeTag();

        return response([
            'tag' => TagResource::make($tag),
            'message' => __('tags.store')
        ]);
    }

    public function show(Tag $tag): Response
    {
        return response([
            'tag' => TagResource::make($tag->load('places')),
        ]);
    }

    public function update(UpdateTagRequest $request, Tag $tag): Response
    {
        $tag = $request->updateTag();

        return response([
            'tag' => TagResource::make($tag),
            'message' => __('tags.update')
        ]);
    }

    public function destroy(Tag $tag): Response
    {
        if ($tag->remove()) {
            return response([
                'message' => __('tags.destroy')
            ]);
        }

        return response([
            'message' => __('tags.cant_destroy'),
        ], 409);
    }
}
