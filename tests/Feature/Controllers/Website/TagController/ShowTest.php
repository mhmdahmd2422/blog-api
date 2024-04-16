<?php

use App\Http\Resources\Website\TagResource;
use App\Models\Tag;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can show a visible tag', function () {
   $tag = Tag::factory()->hasPlaces(2)->create();

   get(route('website.tags.show', $tag))
       ->assertStatus(200)
       ->assertExactJson([
           'tag' => responseData(TagResource::make($tag->load('places')))
       ]);
});
