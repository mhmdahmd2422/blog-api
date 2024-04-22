<?php

use App\Http\Resources\Admin\TagResource;
use App\Models\Tag;
use function Pest\Laravel\{post};

beforeEach(function () {
    loginAsUser();
});

it('can store a tag', function () {
   $tag = Tag::factory()->invisible()->create();

   $tag->name = 'Test_Tag_Name';

   $response = post(route('admin.tags.store'), [
       'name' => $tag->name,
       'is_visible' => $tag->is_visible
   ]);

   $tag->id = Tag::orderBy('id', 'desc')->first()->id;

   $response
       ->assertStatus(200)
       ->assertJson([
           'tag' => responseData(TagResource::make($tag)),
           'message' => __('tags.store')
       ]);

   $this->assertDatabaseHas(Tag::class, [
       'name' => $tag->name,
       'is_visible' => $tag->is_visible
   ]);
});

it('requires a valid data when creating', function (array $badData, array|string $errors) {
    $tag = Tag::factory()->invisible()->create([
        'name' => 'test_tag_name'
    ]);

    post(route('admin.tags.store'), [[
        'name' => $tag->name,
        'is_visible' => $tag->is_visible,
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['name' => null], 'name'],
    [['name' => 2], 'name'],
    [['name' => 1.5], 'name'],
    [['name' => true], 'name'],
    [['name' => str_repeat('a', 101)], 'name'],
    [['name' => 'Tag name'], 'name'], //regex
    [['name' => 'test_tag_name'], 'name'], //unique
    [['is_visible' => null], 'is_visible'],
    [['is_visible' => 5], 'is_visible'],
    [['is_visible' => 1.5], 'is_visible'],
    [['is_visible' => 'string'], 'is_visible'],
]);
