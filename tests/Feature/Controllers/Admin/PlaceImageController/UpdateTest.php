<?php

use App\Http\Resources\Admin\PlaceResource;
use App\Models\Image;
use App\Models\Place;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{put};

beforeEach(function () {
    loginAsUser();
});

it('returns not found if image do not exist for this place', function () {
    $firstPlace = Place::factory()->invisible()->has(
        Image::factory()->is_main()
    )->create();
    $secondPlace = Place::factory()->invisible()->create();
    $secondPlaceImage = Image::factory()->for($secondPlace, 'imageable')->create();
    $image = UploadedFile::fake()->image('testImage.png');

    put(route('admin.posts.images.update', [$firstPlace, $secondPlaceImage->id]), [
        'image' => $image,
        'is_main' => 1
    ])
        ->assertStatus(404);
});

it('can update a place image', function () {
    $place = Place::factory()->invisible()->create();
    $oldImage = UploadedFile::fake()->image('testImage1.png');
    $oldImagePath = 'uploads/places/'.$oldImage->hashName();
    $oldImage->storeAs('uploads/places/', $oldImage->hashName());
    $newImage = UploadedFile::fake()->image('testImage2.png');
    $newImagePath = 'uploads/places/'.$newImage->hashName();
    Image::factory()->count(2)->for($place, 'imageable')->sequence(
        ['path' => $oldImagePath, 'is_main' => false],
        ['is_main' => true]
    )->create();

    put(route('admin.places.images.update', [$place, $place->images()->first()]), [
        'image' => $newImage,
        'is_main' => 1
    ])
        ->assertStatus(200)
        ->assertExactJson([
            'place' => responseData(PlaceResource::make($place->load('images'))),
            'message' => __('places.image.update')
        ]);

    expect($place->main_image->path)
        ->toEqual($newImagePath);

    $this->assertDatabaseHas(Image::class, [
        'imageable_type' => Place::class,
        'imageable_id' => $place->id,
        'path' => $newImagePath,
        'is_main' => 1
    ]);

    $this->assertDatabaseMissing(Image::class, [
        'imageable_type' => Place::class,
        'imageable_id' => $place->id,
        'path' => $oldImagePath
    ]);

    Storage::assertExists($newImagePath);
    Storage::assertMissing($oldImagePath);
});

it('requires a valid data when updating', function (array $badData, array|string $errors) {
    $place = Place::factory()->invisible()->create();
    $image = Image::factory()->is_main()->for($place, 'imageable')->create();

    put(route('admin.places.images.update', [$place, $image->id]), [[
        'image' => UploadedFile::fake()->image('testImage'),
        'is_main' => $image->is_main,
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['image' => null], 'image'],
    [['image' => 5], 'image'],
    [['image' => 1.5], 'image'],
    [['image' => 'string'], 'image'],
    [['image' => UploadedFile::fake()->create('testImage', 2049)], 'image'], // max
    [['is_main' => null], 'is_main'],
    [['is_main' => 5], 'is_main'],
    [['is_main' => 1.5], 'is_main'],
    [['is_main' => 'string'], 'is_main'],
]);
