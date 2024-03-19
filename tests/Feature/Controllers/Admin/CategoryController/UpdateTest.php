<?php

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{put};

it('can update a post', function () {
    $oldCategory = Category::factory()->invisible()->create();
    $updatedCategory = Category::factory()->visible()->create([
        'name' => 'updated name',
    ]);
    $response = put(route('admin.categories.update', $oldCategory), [
        'name' => $updatedCategory->name,
        'is_visible' => $updatedCategory->is_visible,
    ]);

    $updatedCategory->id = $oldCategory->fresh()->id;

    $response->assertStatus(200)
        ->assertExactJson([
            'category' => responseData(CategoryResource::make($updatedCategory)),
            'message' => __('categories.update')
        ]);

    $updatedCategory->delete();

    $this->assertDatabaseHas(Category::class, [
        'id' => $oldCategory->fresh()->id,
        'name' => 'updated name',
        'is_visible' => true
    ]);
});

it('can update image of an existing category', function () {
    $category = Category::factory()->invisible()->create();
    $oldImage = UploadedFile::fake()->image('testImage1.png');
    $oldImagePath = 'uploads/categories/'.$oldImage->hashName();
    $newImage = UploadedFile::fake()->image('testImage2.png');
    $newImagePath = 'uploads/categories/'.$newImage->hashName();
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
            'category' => responseData(CategoryResource::make($category->fresh())),
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

it('requires a valid data when updating', function (array $badData, array|string $errors) {
    $oldCategory = Category::factory()->visible()->create();
    $updatedCategory = Category::factory()->invisible()->create([
        'name' => 'updated category name',
    ]);

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
    [['is_visible' => null], 'is_visible'],
    [['is_visible' => 5], 'is_visible'],
    [['is_visible' => 1.5], 'is_visible'],
    [['is_visible' => 'string'], 'is_visible'],
]);
