<?php

use App\Http\Resources\Admin\PlaceSimpleResource;
use App\Models\Place;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can get all places', function () {
   $places = Place::factory()->count(10)->hasImages(3)
       ->sequence(
       ['is_visible' => true],
       ['is_visible' => false],
   )->create();

   get(route('admin.places.index'))
       ->assertStatus(200)
       ->assertExactJson([
           'places' => responseData(
               PlaceSimpleResource::collection($places->load('images')
                   ->paginate(pagination_length('place')))
           )
       ]);
});
