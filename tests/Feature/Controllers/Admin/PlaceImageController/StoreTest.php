<?php

use App\Http\Resources\Admin\PlaceResource;
use App\Models\Image;
use App\Models\Place;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\post;

it('can add images to an existing place', function () {
   $place = Place::factory()->invisible()->create();
   $image = UploadedFile::fake()->image('testImage.png');

   expect($place->images)
       ->toHaveCount(0);

   post(route('admin.places.images.store', $place), [
       'images' => [
           ['image' => $image],
           ['image' => $image, 'is_main' => true]
       ]
   ])
       ->assertStatus(200)
       ->assertJson([
           'place' => responseData(PlaceResource::make($place->load('images'))),
           'message' => __('places.image.store')
       ]);


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
    $place = Place::factory()->invisible()->create();

    post(route('admin.places.images.store', $place), [[
        'images' => [
            'image' => UploadedFile::fake()->image('testImage'),
            'is_main' => 1
        ]
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['images' => [null]], 'images'],
    [['images' => [5]], 'images'],
    [['images' => [1.5]], 'images'],
    [['images' => ['string']], 'images'],
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
]);
