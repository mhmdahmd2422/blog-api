<?php

use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{put};

beforeEach(function () {
    loginAsUser();
});

it('can update a category', function () {
    $oldCategory = Category::factory()->invisible()->create();
    $updatedCategory = Category::factory()->visible()->make();

    $response = put(route('admin.categories.update', $oldCategory), [
        'name' => $updatedCategory->name,
        'is_visible' => $updatedCategory->is_visible,
    ]);

    $updatedCategory->id = $oldCategory->fresh()->id;
    $updatedCategory->slug = $oldCategory->fresh()->slug;

    $response->assertStatus(200)
        ->assertExactJson([
            'category' => responseData(CategoryResource::make($updatedCategory->load('image'))),
            'message' => __('categories.update')
        ]);

    $updatedCategory->delete();

    $this->assertDatabaseHas(Category::class, [
        'id' => $updatedCategory->id,
        'name' => $updatedCategory->name,
        'is_visible' => $updatedCategory->is_visible
    ]);
});

it('can update image of an existing category image', function () {
    $category = Category::factory()->invisible()->create();
    $oldImage = UploadedFile::fake()->image('testImage1.png');
    $oldImagePath = 'uploads/categories/' . $oldImage->hashName();
    $newImage = UploadedFile::fake()->image('testImage2.png');
    $newImagePath = 'uploads/categories/' . $newImage->hashName();
    $oldImage->storeAs('uploads/categories/', $oldImage->hashName());
    Image::factory()->for($category, 'imageable')->create([
        'path' => $oldImagePath
    ]);

    expect($category->image->path)
        ->toEqual($oldImagePath);

    put(route('admin.categories.update', $category), [
        'image' => $newImage,
    ])
        ->assertStatus(200)
        ->assertExactJson([
            'category' => responseData(CategoryResource::make($category->fresh()->load('image'))),
            'message' => __('categories.update')
        ]);

    $this->assertDatabaseHas(Image::class, [
        'imageable_type' => Category::class,
        'imageable_id' => $category->id,
        'path' => $newImagePath
    ]);

    $this->assertDatabaseMissing(Image::class, [
        'imageable_type' => Category::class,
        'imageable_id' => $category->id,
        'path' => $oldImagePath
    ]);

    expect($category->fresh()->image->path)
        ->toEqual($newImagePath)
        ->and(Image::all())
        ->toHaveCount(1);

    Storage::assertExists($newImagePath);
    Storage::assertMissing($oldImagePath);
});

it('can add image to an existing category that has no image', function () {
    $category = Category::factory()->invisible()->create();
    $image = UploadedFile::fake()->image('testImage.png');
    $imagePath = 'uploads/categories/' . $image->hashName();


    expect($category->image)
        ->toBeEmpty();

    put(route('admin.categories.update', $category), [
        'image' => $image,
    ])
        ->assertStatus(200)
        ->assertExactJson([
            'category' => responseData(CategoryResource::make($category->fresh()->load('image'))),
            'message' => __('categories.update')
        ]);

    $this->assertDatabaseHas(Image::class, [
        'imageable_type' => Category::class,
        'imageable_id' => $category->id,
        'path' => $imagePath
    ]);

    expect($category->fresh()->image->path)
        ->toEqual($imagePath)
        ->and(Image::all())
        ->toHaveCount(1);

    Storage::assertExists($imagePath);
});

it('requires a valid data when updating', function (array $badData, array|string $errors) {
    $oldCategory = Category::factory()->visible()->create();
    $updatedCategory = Category::factory()->invisible()->create();

    put(route('admin.categories.update', $oldCategory), [[
        'name' => $updatedCategory->name,
        'image' => UploadedFile::fake()->image('testImage'),
        'is_visible' => $updatedCategory->is_visible,
    ], ...$badData])
        ->assertInvalid($errors);
})->with([
    [['name' => null], 'name'],
    [['name' => 2], 'name'],
    [['name' => 1.5], 'name'],
    [['name' => true], 'name'],
    [['name' => str_repeat('a', 26)], 'name'],
    [['image' => null], 'image'],
    [['image' => 5], 'image'],
    [['image' => 1.5], 'image'],
    [['image' => 'string'], 'image'],
    [['image' => UploadedFile::fake()->create('testImage', 2049)], 'image'], // max
    [['is_visible' => null], 'is_visible'],
    [['is_visible' => 5], 'is_visible'],
    [['is_visible' => 1.5], 'is_visible'],
    [['is_visible' => 'string'], 'is_visible'],
]);
