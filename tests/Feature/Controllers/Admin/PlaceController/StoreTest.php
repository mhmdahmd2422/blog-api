<?php

use App\Http\Resources\Admin\PlaceResource;
use App\Models\Image;
use App\Models\Place;
use App\Models\Specification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{post};

beforeEach(function () {
    loginAsUser();
});

it('can store a place', function () {
    $place = Place::factory()->invisible()->hasImages(3)->hasTags(2)
        ->hasAttached(Specification::factory()->count(2), ['description' => 'value'])
        ->create();
    $image = UploadedFile::fake()->image('testImage.png');

    $response = post(route('admin.places.store'), [
        'name' => $place->name,
        'description' => $place->description,
        'images' => [
            ['image' => $image],
            ['image' => $image],
            ['image' => $image, 'is_main' => 1]
        ],
        'tag_id' => $place->tags->pluck('id')->toArray(),
        'specifications' => $place->specifications->map(function ($item) {
                return [
                    'specification_id' => $item['pivot']['specification_id'],
                    'description' => $item['pivot']['description']
                ];
            })->toArray(),
        'is_visible' => $place->is_visible
    ]);

    $place->id = Place::orderBy('id', 'desc')->first()->id;
    $place->slug = Place::orderBy('id', 'desc')->first()->slug;

    $response
        ->assertStatus(200)
        ->assertJson([
            'place' => responseData(PlaceResource::make($place->load('images', 'tags', 'specifications'))),
            'message' => __('places.store')
        ]);

    $this->assertDatabaseHas(Place::class, [
        'name' => $place->name,
        'slug' => $place->slug,
        'description' => $place->description,
        'is_visible' => $place->is_visible
    ]);

    foreach ($place->specifications as $specification) {
        $this->assertDatabaseHas('place_specification', [
            'place_id' => $place->id,
            'specification_id' => $specification->id,
        ]);
    }

    foreach ($place->tags as $tag) {
        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_type' => Place::class,
            'taggable_id' => $place->id,
        ]);
    }

    foreach ($place->images as $image) {
        $this->assertDatabaseHas(Image::class, [
            'imageable_type' => Place::class,
            'imageable_id' => $place->id,
            'path' => $image->path,
        ]);

        Storage::assertExists($image->path);
    }
});

it('requires a valid data when creating', function (array $badData, array|string $errors) {
    $place = Place::factory()->invisible()->hasImages(3)->hasTags(2)
        ->hasAttached(Specification::factory()->count(2), ['description' => 'value'])
        ->create();
    $image = UploadedFile::fake()->image('testImage.png');

    post(route('admin.places.store'), [[
        'name' => $place->name,
        'description' => $place->description,
        'images' => [
            ['image' => $image],
            ['image' => $image],
            ['image' => $image, 'is_main' => 1]
        ],
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
    [['name' => str_repeat('a', 101)], 'name'],
    [['description' => null], 'description'],
    [['description' => 2], 'description'],
    [['description' => 1.5], 'description'],
    [['description' => true], 'description'],
    [['description' => str_repeat('a', 1001)], 'description'],
    [['is_visible' => null], 'is_visible'],
    [['is_visible' => 5], 'is_visible'],
    [['is_visible' => 1.5], 'is_visible'],
    [['is_visible' => 'string'], 'is_visible'],
    [['images' => null], 'images'],
    [['images' => 5], 'images'],
    [['images' => 1.5], 'images'],
    [['images' => 'string'], 'images'],
    [['images' => [
        ['image' => UploadedFile::fake()->create('testImage', 2049)]
    ]], 'images.0.image'], // max image size
    [['images' => [
        ['image' => UploadedFile::fake()->image('testImage1')],
        ['image' => UploadedFile::fake()->image('testImage1')],
        ['image' => UploadedFile::fake()->image('testImage1')],
        ['image' => UploadedFile::fake()->image('testImage1'), 'is_main' => 1],
    ]], ['images', 'images', 'images', 'images']], // max images
    [['images' => [
        ['image' => UploadedFile::fake()->create('testImage')]
    ]], 'images'], // no main image
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
        ['specification_id' => 1, 'description' => str_repeat('a', 101)]
    ]], 'specifications.0.description'],
    [['images' => [
        ['image' => UploadedFile::fake()->image('testImage1')],
        ['image' => UploadedFile::fake()->image('testImage1')],
        ['image' => UploadedFile::fake()->image('testImage1')],
        ['image' => UploadedFile::fake()->image('testImage1'), 'is_main' => 1],
    ]], ['images', 'images', 'images', 'images']], // max images
    [['images' => [
        ['image' => UploadedFile::fake()->create('testImage')]
    ]], 'images'], // no main image
]);
