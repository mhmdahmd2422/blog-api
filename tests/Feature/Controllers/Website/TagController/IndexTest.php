<?php

use App\Http\Resources\Website\TagResource;
use App\Models\Tag;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can get all visible tags', function () {
   $tags = Tag::factory()->count(5)->hasPlaces(2)->create();

   get(route('website.tags.index'))
       ->assertStatus(200)
       ->assertExactJson([
           'tags' => responsePaginatedData(
               TagResource::collection(Tag::visible()
                   ->paginate(pagination_length('tag')))
           )
       ]);
});
