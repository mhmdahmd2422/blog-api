<?php

use App\Http\Resources\Admin\TagResource;
use App\Models\Tag;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can show a tag', function () {
   $tag = Tag::factory()->invisible()->hasPlaces(2)->create();

   get(route('admin.tags.show', $tag))
       ->assertStatus(200)
       ->assertExactJson([
           'tag' => responseData(TagResource::make($tag->load('places')))
       ]);
});
