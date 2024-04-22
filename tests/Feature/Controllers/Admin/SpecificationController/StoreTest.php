<?php

use App\Http\Resources\Admin\SpecificationResource;
use App\Models\Image;
use App\Models\Specification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{post};

beforeEach(function () {
    loginAsUser();
});

it('can store a specification', function () {
   $specification = Specification::factory()->make();
   $image = UploadedFile::fake()->image('testImage.png');
   $imagePath = 'uploads/specifications/' . $image->hashName();

   post(route('admin.specifications.store'), [
       'name' => $specification->name,
       'image' => $image,
   ])
       ->assertStatus(200)
       ->assertExactJson([
           'specification' => responseData(
               SpecificationResource::make(Specification::first()->load('image'))
           ),
           'message' => __('specifications.store')
       ]);

   $createdSpecification = Specification::first();

   $this->assertDatabaseHas(Specification::class, [
       'id' => $createdSpecification->id,
       'name' => $createdSpecification->name,
   ]);

   expect($createdSpecification->image)
       ->toEqual(Image::first())
       ->and($createdSpecification->image->path)
       ->toEqual($imagePath);

   $this->assertDatabaseHas(Image::class, [
       'imageable_type' => Specification::class,
       'imageable_id' => $createdSpecification->id,
       'path' => $createdSpecification->image->path,
   ]);

   Storage::assertExists($imagePath);
});

it('requires a valid data when creating', function (array $badData, array|string $errors) {
    $specification = Specification::factory()->create([
        'name' => 'test Specification'
    ]);

    post(route('admin.specifications.store'), [[
        'name' => $specification->name,
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
    [['name' => 'test Specification'], 'name'], // unique
    [['image' => null], 'image'],
    [['image' => 5], 'image'],
    [['image' => 1.5], 'image'],
    [['image' => 'string'], 'image'],
    [['image' => UploadedFile::fake()->create('testImage', 2049)], 'image'], // max
]);
