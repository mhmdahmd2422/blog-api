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

it('requires a valid data when updating', function (array $badData, array|string $errors) {
    $place = Place::factory()->invisible()->hasImages(3)->hasTags(2)
        ->hasAttached(Specification::factory()->count(2), ['description' => 'value'])
        ->create();

    put(route('admin.places.update', $place), [[
        'name' => $place->name,
        'description' => $place->description,
        'tag_id' => $place->tags->pluck('id')->toArray(),
        'specifications' => $place->specifications->map(function ($item) {
            return [
                'specification_id' => $item['pivot']['specification_id'],
                'description' => $item['pivot']['description']
            ];
        })->toArray(),
        'is_visible' => $place->is_visible
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['name' => null], 'name'],
    [['name' => 2], 'name'],
    [['name' => 1.5], 'name'],
    [['name' => true], 'name'],
    [['name' => str_repeat('a', 1)], 'name'],
    [['name' => str_repeat('a', 101)], 'name'],
    [['description' => null], 'description'],
    [['description' => 2], 'description'],
    [['description' => 1.5], 'description'],
    [['description' => true], 'description'],
    [['description' => str_repeat('a', 1)], 'description'],
    [['description' => str_repeat('a', 1001)], 'description'],
    [['is_visible' => null], 'is_visible'],
    [['is_visible' => 5], 'is_visible'],
    [['is_visible' => 1.5], 'is_visible'],
    [['is_visible' => 'string'], 'is_visible'],
    [['tag_id' => null], 'tag_id'],
    [['tag_id' => 1.5], 'tag_id'],
    [['tag_id' => true], 'tag_id'],
    [['tag_id' => 'string'], 'tag_id'],
    [['tag_id' => [null]], 'tag_id.0'],
    [['tag_id' => [1.5]], 'tag_id.0'],
    [['tag_id' => [true]], 'tag_id.0'],
    [['tag_id' => ['string']], 'tag_id.0'],
    [['tag_id' => [1, 1]], 'tag_id.0'], // distinct
    [['tag_id' => [3]], 'tag_id.0'], // exists
    [['specifications' => null], 'specifications'],
    [['specifications' => 5], 'specifications'],
    [['specifications' => 1.5], 'specifications'],
    [['specifications' => 'string'], 'specifications'],
    [['specifications' => [
        ['specification_id' => null]
    ]], 'specifications.0.specification_id'],
    [['specifications' => [
        ['specification_id' => 'string']
    ]], 'specifications.0.specification_id'],
    [['specifications' => [
        ['specification_id' => 1, 'description' => 'value'],
        ['specification_id' => 1, 'description' => 'value'],
    ]], 'specifications.0.specification_id'], //distinct
    [['specifications' => [
        ['specification_id' => 5, 'description' => 'value'],
    ]], 'specifications.0.specification_id'], //exists
    [['specifications' => [
        ['specification_id' => 1]
    ]], 'specifications.0.description'], //no specification description
    [['specifications' => [
        ['specification_id' => 1, 'description' => null]
    ]], 'specifications.0.description'],
    [['specifications' => [
        ['specification_id' => 1, 'description' => 1]
    ]], 'specifications.0.description'],
    [['specifications' => [
        ['specification_id' => 1, 'description' => 1.5]
    ]], 'specifications.0.description'],
    [['specifications' => [
        ['specification_id' => 1, 'description' => true]
    ]], 'specifications.0.description'],
    [['specifications' => [
        ['specification_id' => 1, 'description' => str_repeat('a', 1)]
    ]], 'specifications.0.description'],
    [['specifications' => [
        ['specification_id' => 1, 'description' => str_repeat('a', 101)]
    ]], 'specifications.0.description'],
]);
