<?php

use App\Http\Resources\Admin\TagResource;
use App\Models\Tag;
use function Pest\Laravel\{put};

beforeEach(function () {
    loginAsUser();
});

it('can update a tag', function () {
    $oldTag = Tag::factory()->visible()->create();
    $newTag = Tag::factory()->visible()->create();

    $newTag->delete();
    $newTag->id = $oldTag->fresh()->id;

    $response = put(route('admin.tags.update', $oldTag), [
        'name' => $newTag->name,
        'is_visible' => $newTag->is_visible,
    ]);

    $response
        ->assertStatus(200)
        ->assertExactJson([
            'tag' => responseData(TagResource::make($newTag)),
            'message' => __('tags.update')
        ]);

    $this->assertDatabaseHas(Tag::class, [
        'id' => $newTag->id,
        'name' => $newTag->name,
        'is_visible' => $newTag->is_visible,
    ]);
});

it('requires a valid data when updating', function (array $badData, array|string $errors) {
    $oldTag = Tag::factory()->invisible()->create();
    $updatedTag = Tag::factory()->visible()->create();

    put(route('admin.tags.update', $oldTag), [[
        'name' => $updatedTag->name,
        'is_visible' => $updatedTag->is_visible,
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['name' => null], 'name'],
    [['name' => 2], 'name'],
    [['name' => 1.5], 'name'],
    [['name' => true], 'name'],
    [['name' => str_repeat('a', 101)], 'name'],
    [['name' => 'Tag name'], 'name'], //regex
    [['is_visible' => null], 'is_visible'],
    [['is_visible' => 5], 'is_visible'],
    [['is_visible' => 1.5], 'is_visible'],
    [['is_visible' => 'string'], 'is_visible'],
]);
