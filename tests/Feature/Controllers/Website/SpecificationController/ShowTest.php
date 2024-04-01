<?php

use App\Http\Resources\Website\SpecificationResource;
use App\Models\Place;
use App\Models\Specification;
use function Pest\Laravel\{get};

it('can show a visible specification', function () {
   $specification = Specification::factory()->hasImage()->hasAttached(
       Place::factory()->count(3), ['description' => 'value']
   )->create();

   get(route('website.specifications.show', $specification))
       ->assertStatus(200)
       ->assertExactJson([
           'specification' => responseData(
               SpecificationResource::make($specification->load('image', 'places'))
           )
       ]);
});
