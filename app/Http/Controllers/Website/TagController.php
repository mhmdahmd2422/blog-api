<?php

namespace App\Http\Controllers\Website;

use App\Filters\Website\TagFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Website\TagResource;
use App\Models\Tag;
use Illuminate\Http\Response;

class TagController extends Controller
{
    public function index(TagFilter $filters): Response
    {
        $paginationLength = pagination_length('tag');

        return response([
            'tags' => TagResource::collection(Tag::with('places')->visible()
                ->filter($filters)->paginate($paginationLength))
        ]);
    }

    public function show(Tag $tag): Response
    {
        return response([
            'tag' => TagResource::make($tag->load('places')),
        ]);
    }
}
