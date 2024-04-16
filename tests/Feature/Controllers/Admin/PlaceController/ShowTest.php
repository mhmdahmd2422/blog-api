<?php

use App\Http\Resources\Admin\PlaceResource;
use App\Models\Place;
use App\Models\Specification;
use function Pest\Laravel\{get};

beforeEach(function () {
    loginAsUser();
});

it('can show a place', function () {
   $place = Place::factory()->invisible()->hasImages(3)->hasTags(2)
       ->hasAttached(Specification::factory()->count(2), ['description' => 'value'])
       ->create();

   get(route('admin.places.show', $place))
       ->assertStatus(200)
       ->assertExactJson([
           'place' => responseData(PlaceResource::make($place->load('images', 'tags', 'specifications')))
       ]);
});
