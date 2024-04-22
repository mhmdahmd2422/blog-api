<?php

use App\Http\Resources\Website\PlaceSimpleResource;
use App\Models\Place;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can get all visible places', function () {
   Place::factory()->count(10)->hasImages(3)
       ->sequence(
       ['is_visible' => true],
       ['is_visible' => false],
   )->create();

   get(route('website.places.index'))
       ->assertStatus(200)
       ->assertExactJson([
           'places' => responsePaginatedData(
               PlaceSimpleResource::collection(Place::visible()->with('images')
                   ->paginate(pagination_length('place')))
           )
       ]);
});
