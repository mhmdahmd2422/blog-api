<?php

use App\Http\Resources\Admin\SpecificationResource;
use App\Models\Image;
use App\Models\Specification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{put};

beforeEach(function () {
    loginAsUser();
});

it('can update a specification', function () {
    $oldSpecification = Specification::factory()->create();
    $newSpecification = Specification::factory()->create();
    $oldImage = UploadedFile::fake()->image('testImage1.png');
    $oldImagePath = 'uploads/specifications/' . $oldImage->hashName();
    $newImage = UploadedFile::fake()->image('testImage2.png');
    $newImagePath = 'uploads/specifications/' . $newImage->hashName();
    $oldImage->storeAs('uploads/specifications/', $oldImage->hashName());
    Image::factory()->for($oldSpecification, 'imageable')->create([
        'path' => $oldImagePath
    ]);

    $newSpecification->delete();
    $newSpecification->id = $oldSpecification->id;

    $response = put(route('admin.specifications.update', $oldSpecification), [
        'name' => $newSpecification->name,
        'image' => $newImage
    ]);

    $response
        ->assertStatus(200)
        ->assertExactJson([
            'specification' => responseData(
                SpecificationResource::make($newSpecification->load('image'))
            ),
            'message' => __('specifications.update')
        ]);

    $this->assertDatabaseHas(Specification::class, [
        'id' => $newSpecification->id,
        'name' => $newSpecification->name,
    ]);

    $this->assertDatabaseHas(Image::class, [
        'imageable_type' => Specification::class,
        'imageable_id' => $newSpecification->id,
        'path' => $newImagePath
    ]);

    $this->assertDatabaseMissing(Image::class, [
        'imageable_type' => Specification::class,
        'imageable_id' => $newSpecification->id,
        'path' => $oldImagePath
    ]);

    expect($newSpecification->image->path)
        ->toEqual($newImagePath)
        ->and(Image::all())
        ->toHaveCount(1);

    Storage::assertExists($newImagePath);
    Storage::assertMissing($oldImagePath);
});

it('requires a valid data when updating', function (array $badData, array|string $errors) {
    $oldSpecification = Specification::factory()->create();
    $updatedSpecification = Specification::factory()->create([
        'name' => 'another specification name'
    ]);

    put(route('admin.specifications.update', $oldSpecification), [[
        'name' => $updatedSpecification->name,
        'image' => UploadedFile::fake()->image('testImage'),
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['name' => null], 'name'],
    [['name' => 2], 'name'],
    [['name' => 1.5], 'name'],
    [['name' => true], 'name'],
    [['name' => str_repeat('a', 1)], 'name'], // min
    [['name' => str_repeat('a', 101)], 'name'], // max
    [['name' => 'another specification name'], 'name'], // unique
    [['image' => null], 'image'],
    [['image' => 5], 'image'],
    [['image' => 1.5], 'image'],
    [['image' => 'string'], 'image'],
    [['image' => UploadedFile::fake()->create('testImage', 2049)], 'image'], // max
]);
