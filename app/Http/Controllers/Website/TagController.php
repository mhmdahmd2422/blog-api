<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Resources\Website\TagResource;
use App\Models\Tag;
use Illuminate\Http\Response;

class TagController extends Controller
{
    public function index(): Response
    {
        $paginationLength = pagination_length('tag');

        return response([
            'tags' => TagResource::collection(Tag::visible()->with('places')
                ->paginate($paginationLength))
        ]);
    }

    public function show(Tag $tag): Response
    {
        return response([
            'tag' => TagResource::make($tag->load('places')),
        ]);
    }
}
