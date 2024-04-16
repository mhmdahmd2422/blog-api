<?php

use App\Http\Resources\Admin\PlaceResource;
use App\Models\Place;
use App\Models\Specification;
use function Pest\Laravel\{put};

beforeEach(function () {
    loginAsUser();
});

it('can update a place', function () {
   $oldPlace = Place::factory()->visible()->hasTags(2)
       ->hasAttached(Specification::factory()->count(2), ['description' => 'value'])
       ->create();
   $updatedPlace = Place::factory()->invisible()->hasTags(2)
       ->hasAttached(Specification::factory()->count(2), ['description' => 'value'])
       ->create();

   $response = put(route('admin.places.update', $oldPlace), [
       'name' => $updatedPlace->name,
       'description' => $updatedPlace->description,
       'tag_id' => $updatedPlace->tags->pluck('id')->toArray(),
       'specifications' => $updatedPlace->specifications->map(function ($item) {
           return [
               'specification_id' => $item['pivot']['specification_id'],
               'description' => $item['pivot']['description']
           ];
       })->toArray(),
       'is_visible' => $updatedPlace->is_visible
   ]);

   $updatedPlace->id = $oldPlace->fresh()->id;
   $updatedPlace->slug = $oldPlace->fresh()->slug;

   $response->assertStatus(200)
       ->assertExactJson([
           'place' => responseData(PlaceResource::make($updatedPlace->load('images', 'tags', 'specifications'))),
           'message' => __('places.update')
       ]);

   $this->assertDatabaseHas(Place::class, [
       'name' => $updatedPlace->name,
       'slug' => $updatedPlace->slug,
       'description' => $updatedPlace->description,
       'is_visible' => $updatedPlace->is_visible
   ]);

   $this->assertDatabaseMissing(Place::class, [
       'name' => $oldPlace->name,
       'slug' => $oldPlace->slug,
       'description' => $oldPlace->description,
       'is_visible' => $oldPlace->is_visible
   ]);

   foreach ($updatedPlace->specifications as $specification) {
       $this->assertDatabaseHas('place_specification', [
           'place_id' => $updatedPlace->id,
           'specification_id' => $specification->id,
       ]);
   }

   foreach ($oldPlace->specifications as $specification) {
       $this->assertDatabaseHas('place_specification', [
           'place_id' => $oldPlace->id,
           'specification_id' => $specification->id,
       ]);
   }

   foreach ($updatedPlace->tags as $tag) {
       $this->assertDatabaseHas('taggables', [
           'tag_id' => $tag->id,
           'taggable_type' => Place::class,
           'taggable_id' => $updatedPlace->id,
       ]);
   }
});
