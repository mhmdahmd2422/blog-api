<?php

use App\Http\Resources\Website\PlaceSimpleResource;
use App\Models\Place;
use function Pest\Laravel\{get};


it('can get all visible places', function () {
   Place::factory()->count(10)->hasImages(3)
       ->sequence(
       ['is_visible' => true],
       ['is_visible' => false],
   )->create();

   get(route('website.places.index'))
       ->assertStatus(200)
       ->assertExactJson([
           'places' => responseData(
               PlaceSimpleResource::collection(Place::visible()->with('images')
                   ->paginate(pagination_length('place')))
           )
       ]);
});
