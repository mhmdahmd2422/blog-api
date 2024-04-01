<?php

use App\Http\Resources\Admin\TagResource;
use App\Models\Tag;
use function Pest\Laravel\{get};

it('can get all tags', function () {
   $tags = Tag::factory()->invisible()->count(5)->hasPlaces(2)->create();

   get(route('admin.tags.index'))
       ->assertStatus(200)
       ->assertExactJson([
           'tags' => responseData(
               TagResource::collection($tags->load('places')
                   ->paginate(pagination_length('tag')))
           )
       ]);
});
